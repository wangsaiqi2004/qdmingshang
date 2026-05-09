<?php
require __DIR__ . '/bootstrap.php';

if (!qdm_admin_exists()) {
    header('Location: setup.php');
    exit;
}

if (qdm_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!qdm_check_csrf()) {
        $error = '请求已过期，请刷新后重试。';
    } else {
        $admin = qdm_load_admin();
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if ($username === ($admin['username'] ?? '') && password_verify($password, $admin['password_hash'] ?? '')) {
            $_SESSION['qdm_admin_logged_in'] = true;
            $_SESSION['qdm_admin_username'] = $username;
            header('Location: dashboard.php');
            exit;
        }
        $error = '账号或密码不正确。';
    }
}

qdm_admin_header('登录');
?>
<main class="login-shell">
    <section class="login-card">
        <header>
            <div class="brand-mark">MS</div>
            <h1>站点后台管理系统</h1>
            <p>Management System</p>
        </header>
        <?php if ($error): ?>
            <div class="inline-error"><?php echo qdm_h($error); ?></div>
        <?php endif; ?>
        <form method="post" class="login-form">
            <input type="hidden" name="csrf" value="<?php echo qdm_h(qdm_csrf_token()); ?>">
            <label>
                <span>账号</span>
                <input type="text" name="username" autocomplete="username" required autofocus>
            </label>
            <label>
                <span>密码</span>
                <input type="password" name="password" autocomplete="current-password" required>
            </label>
            <button type="submit">立即登录</button>
        </form>
        <footer>测试后台 · 仅用于静态镜像站轻量管理</footer>
    </section>
</main>
<?php qdm_admin_footer(); ?>
