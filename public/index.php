<?php
/**
 * Punt d'entrada principal de l'aplicació
 * Aplicació de Tracking d'Hores
 */

// ✅ ACTIVAR REPORT DE ERRORES (PANTALLA BLANCA SOLUCION)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zona horària correcta Espanya
date_default_timezone_set('Europe/Madrid');

// Iniciar sessió con carpeta propia sin permisos de root
ini_set('session.save_path', __DIR__ . '/../tmp/sessions');
if (!file_exists(__DIR__ . '/../tmp/sessions')) {
    mkdir(__DIR__ . '/../tmp/sessions', 0777, true);
}
session_start();

// Carregar configuració
require_once '../config/database.php';

// Definir rutes
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Rutes públiques
$routes = [
    '/' => 'home',
    '/login' => 'login',
    '/register' => 'register',
    '/logout' => 'logout',
    '/dashboard' => 'dashboard',
];

// Funció per carregar vistes
function load_view($view, $data = []) {
    extract($data);
    include "../src/views/{$view}.php";
}

// Router principal
switch ($request) {
    case '/':
        load_view('home');
        break;
    
    case '/login':
        if ($method === 'POST') {
            // Processar login
            $nom = trim($_POST['nom']);
            $password = $_POST['password'];
            
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE nom = ? AND actiu = TRUE");
            $stmt->execute([$nom]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login correcte
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['authenticated'] = true;

            // Si ja està fitxat a la BD, activar també la sessió local
            $stmt = $db->prepare("SELECT * FROM sessions_treball WHERE user_id = ? AND estat = 'activa' LIMIT 1");
            $stmt->execute([$user['id']]);
            $sessioActiva = $stmt->fetch();
            
            if ($sessioActiva) {
                $_SESSION['sessio_activa'] = true;
                $_SESSION['hora_entrada'] = $sessioActiva['hora_entrada'];
            }
                
                // Actualitzar última connexió
                $stmt = $db->prepare("UPDATE users SET ultima_connexio = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Credencials incorrectes";
                load_view('login', ['error' => $error]);
            }
        } else {
            load_view('login');
        }
        break;
    
    case '/register':
        if ($method === 'POST') {
            // Processar registre d'usuari
            $nom = trim($_POST['nom']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $db = getDBConnection();
            
            try {
                $stmt = $db->prepare("INSERT INTO users (dni, email, password, nom, cognoms, departament, rol, actiu) VALUES (?, ?, ?, ?, ?, ?, 'empleat', TRUE)");
                $stmt->execute([uniqid(), uniqid().'@temp.cat', $password, $nom, '', '']);
                
                $success = "✅ Usuari creat correctament! Ja pots iniciar sessió.";
                load_view('register', ['success' => $success]);
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $error = "❌ Ja existeix un usuari amb aquest correu o DNI";
                } else {
                    $error = "❌ Error al crear l'usuari: " . $e->getMessage();
                }
                load_view('register', ['error' => $error]);
            }
        } else {
            load_view('register');
        }
        break;

    case '/logout':
        session_destroy();
        header('Location: /');
        break;
    
    case '/dashboard':
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
            header('Location: /login');
            exit;
        }
        
        $db = getDBConnection();
        $userId = $_SESSION['user_id'];
        
            // Comprovar estat sessió actual
            try {
                $stmt = $db->prepare("SELECT id, hora_entrada, estat FROM sessions_treball WHERE user_id = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$userId]);
                $sessioActual = $stmt->fetch();
            } catch(Exception $e) {
                $sessioActual = false;
            }
        
        // Obtenir llistat projectes
        $stmt = $db->prepare("SELECT id, codi, nom, client, estat FROM projectes WHERE actiu = 1 ORDER BY nom");
        $stmt->execute();
        $projectes = $stmt->fetchAll();

        // Només si és administrador carreguem TOTS els usuaris
        $totsUsuaris = [];
        if ($_SESSION['user_rol'] === 'administrador' || $_SESSION['user_rol'] === 'admin' || $_SESSION['user_rol'] === 'superadmin') {
            $stmt = $db->prepare("
                SELECT 
                    u.id as id_usuari,
                    u.nom,
                    s.estat,
                    s.hora_entrada,
                    s.hora_sortida,
                    TIMESTAMPDIFF(MINUTE, s.hora_entrada, IFNULL(s.hora_sortida, NOW())) / 60 as hores,
                    (SELECT nom FROM projectes p 
                     JOIN registres_temps rt ON p.id = rt.projecte_id 
                     WHERE rt.user_id = u.id AND rt.estat = 'en_curs' LIMIT 1) as projecte
                FROM users u
                LEFT JOIN sessions_treball s ON u.id = s.user_id
                ORDER BY s.hora_entrada DESC
            ");
            $stmt->execute();
            $totsUsuaris = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        load_view('dashboard', [
            'sessioActual' => $sessioActual,
            'projectes' => $projectes,
            'missatge' => $_SESSION['missatge'] ?? null,
            'totsUsuaris' => $totsUsuaris
        ]);
        
        unset($_SESSION['missatge']);
        break;

    case '/marcar-entrada':
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated'] || $method !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        try {
            $db = getDBConnection();
            
            // ✅ PRIMERO COMPROBAR QUE NO HAYA UNA SESSIÓ ACTIVA YA (EVITAR DUPLICATS)
            $stmtCheck = $db->prepare("SELECT id FROM sessions_treball WHERE user_id = ? AND estat = 'activa' LIMIT 1");
            $stmtCheck->execute([$_SESSION['user_id']]);
            if ($stmtCheck->fetch()) {
                $_SESSION['missatge'] = "⚠️ Ja tens una sessió activa oberta!";
                $_SESSION['sessio_activa'] = true;
                header('Location: /dashboard');
                exit;
            }

            // 1. Crear sessió de treball general
            $stmt = $db->prepare("INSERT INTO sessions_treball (user_id, estat) VALUES (?, 'activa')");
            $stmt->execute([$_SESSION['user_id']]);
            $sessioId = $db->lastInsertId();
            
            // 2. Crear registre de temps per al projecte seleccionat
            $projecteNom = trim($_POST['projecte']);
            $horesEstimades = floatval($_POST['hores_estimades']);
            
            // Busquem si existeix el projecte
            $stmtProjecte = $db->prepare("SELECT id FROM projectes WHERE nom LIKE ? LIMIT 1");
            $stmtProjecte->execute([$projecteNom]);
            $projecte = $stmtProjecte->fetch();
            
            if (!$projecte) {
                // Si NO existeix el projecte, el CREEM automaticament sense comprovacions
                $stmtCrearProjecte = $db->prepare("INSERT INTO projectes (nom, codi, actiu, hores_pressupostades) VALUES (?, ?, 1, ?)");
                $stmtCrearProjecte->execute([$projecteNom, strtoupper(substr($projecteNom, 0, 3)), $horesEstimades]);
                $projecteId = $db->lastInsertId();
            } else {
                $projecteId = $projecte['id'];
            }
            
            // Crear registre de temps
            $stmtRegistre = $db->prepare("INSERT INTO registres_temps (user_id, projecte_id, sessio_id, estat) VALUES (?, ?, ?, 'en_curs')");
            $stmtRegistre->execute([$_SESSION['user_id'], $projecteId, $sessioId]);

            $_SESSION['sessio_activa'] = true;
            $_SESSION['hora_entrada'] = date('Y-m-d H:i:s');
            $_SESSION['projecte'] = $projecteNom;
            $_SESSION['sessio_id'] = $sessioId;
            
            $_SESSION['missatge'] = "✅ Entrada marcada correctament! Bon treball 😊";
            header('Location: /dashboard');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['missatge'] = "❌ ERROR al marcar entrada: " . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
        break;

    case '/marcar-sortida':
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated'] || $method !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        try {
            $db = getDBConnection();
            
            // ✅ PRIMERO COMPROBAR QUE HAYA SESSIÓ ACTIVA PARA PODER TANCAR
            $stmtCheck = $db->prepare("SELECT id FROM sessions_treball WHERE user_id = ? AND estat = 'activa' LIMIT 1");
            $stmtCheck->execute([$_SESSION['user_id']]);
            if (!$stmtCheck->fetch()) {
                $_SESSION['missatge'] = "⚠️ No tens cap sessió activa oberta per tancar!";
                unset($_SESSION['sessio_activa']);
                header('Location: /dashboard');
                exit;
            }

            // 1. Primer tancar registre de temps del projecte activo
            $stmtRegistre = $db->prepare("UPDATE registres_temps SET hora_fi = NOW(), estat = 'finalitzat' WHERE user_id = ? AND estat = 'en_curs'");
            $stmtRegistre->execute([$_SESSION['user_id']]);
            
            // 2. Despres tancar la sessió general de treball
            $stmt = $db->prepare("UPDATE sessions_treball SET hora_sortida = NOW(), estat = 'finalitzada' WHERE user_id = ? AND estat = 'activa'");
            $stmt->execute([$_SESSION['user_id']]);
            
            // Tancar sessió de treball temporal
            unset($_SESSION['sessio_activa']);
            unset($_SESSION['hora_entrada']);
            unset($_SESSION['projecte']);
            unset($_SESSION['sessio_id']);
            
            $_SESSION['missatge'] = "✅ Sortida marcada correctament! Fins aviat 👋";
            header('Location: /dashboard');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['missatge'] = "❌ ERROR al marcar sortida: " . $e->getMessage();
            header('Location: /dashboard');
            exit;
        }
        break;
    
    default:
        http_response_code(404);
        load_view('404');
        break;
}