<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! defined( 'RECRUITMENT_PLUGIN_PATH' ) ) {
    define( 'RECRUITMENT_PLUGIN_PATH', __DIR__ . '/' );
}

if ( ! defined( 'RECRUITMENT_PLUGIN_URL' ) ) {
    define( 'RECRUITMENT_PLUGIN_URL', 'http://127.0.0.1:8000/' );
}

ob_start();
session_start();

// Load core dependencies.
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/class-database.php';
require_once __DIR__ . '/includes/class-application.php';
require_once __DIR__ . '/includes/class-auth.php';
require_once __DIR__ . '/includes/class-router.php';
require_once __DIR__ . '/includes/class-plugin.php';

$auth   = new Recruitment_Auth();
$page   = $_GET['page'] ?? $_GET['recruitment_page'] ?? 'careers';
$page   = [
    'application' => 'application-form',
    'tracking'    => 'application-status',
][ $page ] ?? $page;

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

?><!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAW Recruitment</title>
    <link rel="stylesheet" href="<?= esc_url( recruitment_get_plugin_url( 'assets/css/public.css' ) ) ?>">
    <script src="<?= esc_url( recruitment_get_plugin_url( 'assets/js/public.js' ) ) ?>" defer></script>
    <?php if ( in_array( $page, [ 'login', 'dashboard', 'vacancies', 'applicants', 'applications', 'settings' ], true ) ) : ?>
        <link rel="stylesheet" href="<?= esc_url( recruitment_get_plugin_url( 'assets/css/admin.css' ) ) ?>">
    <?php endif; ?>
</head>
<body>
<?php
$router = new Recruitment_Router();
$router->render( $page, [ 'error' => $error ] );
?>
</body>
</html>
