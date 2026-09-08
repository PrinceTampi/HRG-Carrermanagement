<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRD Login | Recruitment Plugin</title>
    <link rel="stylesheet" href="assets/css/public.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body class="auth-page">
<main class="auth-shell">
    <a class="brand" href="?page=careers">Recruitment<span>Plugin</span></a>
    <section class="auth-card">
        <p class="eyebrow">HRD Access</p>
        <h1>Welcome back.</h1>
        <p class="muted">Masuk untuk membuka dashboard rekrutmen.</p>
        <?php if ( $error ) : ?><div class="alert" role="alert"><?= htmlspecialchars( $error, ENT_QUOTES, 'UTF-8' ) ?></div><?php endif; ?>
        <form method="post" action="?page=login">
            <label for="username">Username</label>
            <input id="username" name="username" type="text" autocomplete="username" required>
            <label for="password">Password</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button class="button" type="submit">Sign in <span aria-hidden="true">-&gt;</span></button>
        </form>
        <p class="hint">Demo: <strong>admin</strong> / <strong>admin123</strong></p>
    </section>
</main>
</body>
</html>
