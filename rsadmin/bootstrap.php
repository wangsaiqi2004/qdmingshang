<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('QDM_ADMIN_DIR', __DIR__);
define('QDM_DATA_DIR', __DIR__ . '/data');
define('QDM_ADMIN_FILE', QDM_DATA_DIR . '/admin.php');
define('QDM_SETTINGS_FILE', QDM_DATA_DIR . '/settings.php');

function qdm_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function qdm_url($path)
{
    return $path;
}

function qdm_ensure_data_dir()
{
    if (!is_dir(QDM_DATA_DIR)) {
        mkdir(QDM_DATA_DIR, 0755, true);
    }
}

function qdm_default_settings()
{
    return array(
        'site_title' => '青岛艺术壁材代理加盟_青岛艺术壁材厂家_青岛艺术壁材品牌招商批发价格_青岛名商艺术壁材新材料有限公司',
        'company_name' => '青岛名商新材料有限公司',
        'welcome_text' => 'Hello，欢迎您来到名商艺术壁材官方网站！',
        'keywords' => '青岛艺术壁材,青岛艺术壁材代理,青岛艺术壁材加盟,青岛艺术壁材招商,青岛艺术壁材厂家',
        'description' => '青岛名商艺术壁材招商加盟代理新材料有限公司是一家专业从事艺术壁材研发生产销售批发于一体的厂家。',
        'phone_1' => '137-9198-6139（张经理）',
        'phone_2' => '151-9201-6881（张经理）',
        'phone_3' => '13291959681 （张经理）',
        'address' => '山东省青岛市李沧区君峰路8号达翁建材市场软装馆一楼F8',
        'copyright' => '版权所有：青岛名商新材料有限公司',
        'icp' => '鲁ICP备16025427号-2',
        'support' => '技术支持：润商科技',
    );
}

function qdm_load_php_array($file)
{
    if (!is_file($file)) {
        return array();
    }
    $data = include $file;
    return is_array($data) ? $data : array();
}

function qdm_write_php_array($file, array $data)
{
    qdm_ensure_data_dir();
    $body = "<?php\nreturn " . var_export($data, true) . ";\n";
    return file_put_contents($file, $body, LOCK_EX) !== false;
}

function qdm_load_settings()
{
    return array_merge(qdm_default_settings(), qdm_load_php_array(QDM_SETTINGS_FILE));
}

function qdm_save_settings(array $settings)
{
    $allowed = array_keys(qdm_default_settings());
    $clean = array();
    foreach ($allowed as $key) {
        $clean[$key] = trim((string) ($settings[$key] ?? ''));
    }
    return qdm_write_php_array(QDM_SETTINGS_FILE, array_merge(qdm_default_settings(), $clean));
}

function qdm_admin_exists()
{
    return is_file(QDM_ADMIN_FILE);
}

function qdm_load_admin()
{
    return qdm_load_php_array(QDM_ADMIN_FILE);
}

function qdm_save_admin($username, $password)
{
    $username = trim((string) $username);
    if ($username === '' || (string) $password === '') {
        return false;
    }
    return qdm_write_php_array(QDM_ADMIN_FILE, array(
        'username' => $username,
        'password_hash' => password_hash((string) $password, PASSWORD_DEFAULT),
        'created_at' => date('c'),
    ));
}

function qdm_logged_in()
{
    return !empty($_SESSION['qdm_admin_logged_in']);
}

function qdm_require_login()
{
    if (!qdm_admin_exists()) {
        header('Location: setup.php');
        exit;
    }
    if (!qdm_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

function qdm_csrf_token()
{
    if (empty($_SESSION['qdm_csrf'])) {
        $_SESSION['qdm_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['qdm_csrf'];
}

function qdm_check_csrf()
{
    $token = $_POST['csrf'] ?? '';
    return is_string($token) && hash_equals(qdm_csrf_token(), $token);
}

function qdm_flash($type, $message)
{
    $_SESSION['qdm_flash'] = array('type' => $type, 'message' => $message);
}

function qdm_get_flash()
{
    $flash = $_SESSION['qdm_flash'] ?? null;
    unset($_SESSION['qdm_flash']);
    return $flash;
}

function qdm_meta_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function qdm_front_apply_settings($html, array $settings)
{
    $html = preg_replace('/<title>.*?<\/title>/is', '<title>' . qdm_meta_escape($settings['site_title']) . '</title>', $html);
    $html = preg_replace('/<meta name="keywords" content=".*?">/is', '<meta name="keywords" content="' . qdm_meta_escape($settings['keywords']) . '">', $html);
    $html = preg_replace('/<meta name="description" content=".*?">/is', '<meta name="description" content="' . qdm_meta_escape($settings['description']) . '">', $html);

    $plain = array(
        '版权所有：青岛名商新材料有限公司' => $settings['copyright'],
        '青岛名商新材料有限公司' => $settings['company_name'],
        'Hello，欢迎您来到名商艺术壁材官方网站！' => $settings['welcome_text'],
        '137-9198-6139（张经理）' => $settings['phone_1'],
        '151-9201-6881（张经理）' => $settings['phone_2'],
        '13291959681 （张经理）' => $settings['phone_3'],
        '山东省青岛市李沧区君峰路8号达翁建材市场软装馆一楼F8' => $settings['address'],
        '鲁ICP备16025427号-2' => $settings['icp'],
        '技术支持：润商科技' => $settings['support'],
    );

    foreach ($plain as $from => $to) {
        $html = str_replace($from, qdm_h($to), $html);
    }

    return $html;
}

function qdm_admin_header($title)
{
    $flash = qdm_get_flash();
    ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo qdm_h($title); ?> - 站点后台管理系统</title>
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php if ($flash): ?>
    <div class="flash flash-<?php echo qdm_h($flash['type']); ?>"><?php echo qdm_h($flash['message']); ?></div>
<?php endif; ?>
<?php
}

function qdm_admin_footer()
{
    ?>
</body>
</html>
<?php
}
