<?php
/**
 * Punt d'entrada principal de l'aplicació
 * Aplicació de Tracking d'Hores
 */

// Iniciar sessió
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
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            
            $db = getDBConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND actiu = TRUE");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login correcte
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_rol'] = $user['rol'];
                $_SESSION['authenticated'] = true;
                
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
        $stmt = $db->prepare("SELECT id, hora_entrada, estat FROM sessions_treball WHERE user_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$userId]);
        $sessioActual = $stmt->fetch();
        
        // Obtenir llistat projectes
        $stmt = $db->prepare("SELECT id, codi, nom, client, estat FROM projectes WHERE actiu = 1 ORDER BY nom");
        $stmt->execute();
        $projectes = $stmt->fetchAll();
        
        load_view('dashboard', [
            'sessioActual' => $sessioActual,
            'projectes' => $projectes,
            'missatge' => $_SESSION['missatge'] ?? null
        ]);
        
        unset($_SESSION['missatge']);
        break;

    case '/marcar-entrada':
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated'] || $method !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        $db = getDBConnection();
        $userId = $_SESSION['user_id'];
        
        // Comprovar si ja té sessió activa
        $stmt = $db->prepare("SELECT id FROM sessions_treball WHERE user_id = ? AND estat = 'activa'");
        $stmt->execute([$userId]);
        
        if (!$stmt->fetch()) {
            // Crear nova sessió de treball
            $stmt = $db->prepare("INSERT INTO sessions_treball (user_id, hora_entrada, estat) VALUES (?, NOW(), 'activa')");
            $stmt->execute([$userId]);
        }
        
        header('Location: /dashboard');
        exit;
        break;

    case '/marcar-sortida':
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated'] || $method !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        $db = getDBConnection();
        $userId = $_SESSION['user_id'];
        
        try {
            // Comprovar primer si hi ha sessió activa
            $stmt = $db->prepare("SELECT id FROM sessions_treball WHERE user_id = ? AND estat = 'activa'");
            $stmt->execute([$userId]);
            
            if ($sessio = $stmt->fetch()) {
                // Només si hi ha sessió activa la tanquem
                $stmt = $db->prepare("UPDATE sessions_treball SET hora_sortida = NOW(), estat = 'finalitzada' WHERE id = ?");
                $stmt->execute([$sessio['id']]);
            }
        } catch (PDOException $e) {
            // Ignorar error de restricció única, sempre redirigir
            error_log("Error marcar sortida: " . $e->getMessage());
        }
        
        header('Location: /dashboard');
        exit;
        break;
    
    default:
        http_response_code(404);
        load_view('404');
        break;
}