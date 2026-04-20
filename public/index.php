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
        load_view('dashboard');
        break;
    
    default:
        http_response_code(404);
        load_view('404');
        break;
}