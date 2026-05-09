<?php
require __DIR__ . '/bootstrap.php';

if (qdm_admin_exists()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!qdm_check_csrf()) {
        $error = '请求已过期，请刷新后重试。';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        if ($username === '') {
            $error = '请输入管理员账号。';
        } elseif (strlen($password) < 8) {
            $error = '密码至少 8 位。';
        } elseif ($password !== $confirm) {
            $error = '两次密码不一致。';
        } elseif (!qdm_save_admin($username, $password)) {
            $error = '保存失败，请确认 rsadmin/data 目录可写。';
        } else {
            qdm_save_settings(qdm_default_settings());
            qdm_flash('success', '管理员创建成功，请登录。');
            header('Location: index.php');
            exit;
        }
    }
}

qdm_admin_header('初始化');
?>
<main class="login-shell">
    <section class="login-card setup-card">
        <header>
            <div class="brand-mark">MS</div>
            <h1>初始化后台</h1>
            <p>首次访问时创建管理员账号</p>
        </header>
        <?php if ($error): ?>
            <div class="inline-error"><?php echo qdm_h($error); ?></div>
        <?php endif; ?>
        <form method="post" class="login-form">
            <input type="hidden" name="csrf" value="<?php echo qdm_h(qdm_csrf_token()); ?>">
            <label>
                <span>管理员账号</span>
                <input type="text" name="username" autocomplete="username" required autofocus>
            </label>
            <label>
                <span>管理员密码</span>
                <input type="password" name="password" autocomplete="new-password" required>
            </label>
            <label>
                <span>确认密码</span>
                <input type="password" name="confirm" autocomplete="new-password" required>
            </label>
            <button type="submit">创建后台账号</button>
        </form>
        <footer>创建后该页面会自动关闭初始化能力</footer>
    </section>
</main>
<?php qdm_admin_footer(); ?>
