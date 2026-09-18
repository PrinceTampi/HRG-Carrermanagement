<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    http_response_code( 403 );
    exit;
}

session_start();

// Load core dependencies.
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/class-database.php';
require_once __DIR__ . '/includes/class-auth.php';
require_once __DIR__ . '/includes/class-router.php';
require_once __DIR__ . '/includes/class-plugin.php';

$auth   = new Recruitment_Auth();
$page   = $_GET['page'] ?? 'careers';

if ( $page === 'logout' ) {
    $auth->logout();
    header( 'Location: ?page=login' );
    exit;
}

$error = null;
if ( $page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    if ( $auth->login( $_POST['username'] ?? '', $_POST['password'] ?? '' ) ) {
        header( 'Location: ?page=dashboard' );
        exit;
    }
    $error = 'Username atau password salah. Silakan coba lagi.';
}

$router = new Recruitment_Router();
$router->render( $page, [ 'error' => $error ] );
