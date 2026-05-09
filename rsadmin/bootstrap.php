<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('QDM_ADMIN_DIR', __DIR__);
define('QDM_SITE_DIR', dirname(__DIR__));
define('QDM_DATA_DIR', __DIR__ . '/data');
define('QDM_ADMIN_FILE', QDM_DATA_DIR . '/admin.php');
define('QDM_SETTINGS_FILE', QDM_DATA_DIR . '/settings.php');
define('QDM_UPLOAD_DIR', QDM_SITE_DIR . '/uploadfile/admin');
define('QDM_MAX_UPLOAD_BYTES', 6 * 1024 * 1024);

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

function qdm_normalize_public_path($path)
{
    $path = trim((string) $path);
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#/+#', '/', $path);
    $path = ltrim($path, '/');
    if ($path === '' || strpos($path, '..') !== false) {
        return '';
    }
    return $path;
}

function qdm_site_path($publicPath)
{
    $publicPath = qdm_normalize_public_path($publicPath);
    if ($publicPath === '') {
        return '';
    }
    return QDM_SITE_DIR . '/' . $publicPath;
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

function qdm_asset_targets()
{
    return array(
        'logo' => array('label' => '网站 Logo', 'path' => 'statics/default/img/logo.png', 'hint' => '建议透明 PNG，宽高按原图比例。'),
        'header_phone' => array('label' => '顶部电话图', 'path' => 'statics/default/img/upimages/1/dianhua1.png', 'hint' => '右上角电话图片。'),
        'banner_1' => array('label' => '首页 Banner 1', 'path' => 'statics/default/img/upimages/1/banner.jpg', 'hint' => '首页轮播首图。'),
        'banner_2' => array('label' => '首页 Banner 2', 'path' => 'statics/default/img/upimages/1/banner2.jpg', 'hint' => '首页轮播第二张。'),
        'banner_3' => array('label' => '首页 Banner 3', 'path' => 'statics/default/img/upimages/1/banner3.jpg', 'hint' => '首页轮播第三张。'),
        'banner_4' => array('label' => '首页 Banner 4', 'path' => 'statics/default/img/upimages/1/banner4.jpg', 'hint' => '首页轮播第四张。'),
        'mobile_qr' => array('label' => '手机浏览二维码', 'path' => 'statics/default/img/1592011475497672da4289b556e54.png', 'hint' => '底部“手机浏览”二维码。'),
        'douyin_qr' => array('label' => '抖音二维码', 'path' => 'statics/default/img/20230720170932.png', 'hint' => '底部“打开抖音扫一扫”二维码。'),
        'wechat_qr' => array('label' => '在线客服微信二维码', 'path' => 'statics/default/img/upimages/weixin/weixin.jpg', 'hint' => '右侧在线客服弹窗二维码。'),
        'footer_image' => array('label' => '底部背景图', 'path' => 'statics/default/img/upimages/13/tu13.png', 'hint' => '底部联系区域图片。'),
        'join_image' => array('label' => '招商加盟主图', 'path' => 'statics/default/img/upimages/12/tu12.jpg', 'hint' => '首页招商/资讯附近展示图。'),
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

function qdm_allowed_upload_types()
{
    return array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    );
}

function qdm_detect_mime($file)
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            if ($mime) {
                return $mime;
            }
        }
    }
    if (function_exists('mime_content_type')) {
        return mime_content_type($file);
    }
    return '';
}

function qdm_handle_upload($fieldName)
{
    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return array(false, '没有选择文件。');
    }
    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return array(false, '上传失败，错误码：' . (int) ($file['error'] ?? 0));
    }
    if (($file['size'] ?? 0) <= 0 || ($file['size'] ?? 0) > QDM_MAX_UPLOAD_BYTES) {
        return array(false, '图片大小不能超过 6MB。');
    }

    $mime = qdm_detect_mime($file['tmp_name']);
    $types = qdm_allowed_upload_types();
    if (!isset($types[$mime])) {
        return array(false, '只支持 JPG、PNG、GIF、WEBP 图片。');
    }

    $subdir = date('Ymd');
    $targetDir = QDM_UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        return array(false, '上传目录创建失败。');
    }

    $name = date('His') . '_' . bin2hex(random_bytes(4)) . '.' . $types[$mime];
    $target = $targetDir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return array(false, '保存上传文件失败。');
    }

    return array(true, 'uploadfile/admin/' . $subdir . '/' . $name);
}

function qdm_list_media_files()
{
    $files = array();
    if (!is_dir(QDM_UPLOAD_DIR)) {
        return $files;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(QDM_UPLOAD_DIR, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'), true)) {
            continue;
        }
        $path = str_replace('\\', '/', $file->getPathname());
        $root = str_replace('\\', '/', QDM_SITE_DIR) . '/';
        if (strpos($path, $root) === 0) {
            $public = substr($path, strlen($root));
            $files[] = array(
                'path' => $public,
                'size' => $file->getSize(),
                'mtime' => $file->getMTime(),
            );
        }
    }
    usort($files, function ($a, $b) {
        return $b['mtime'] <=> $a['mtime'];
    });
    return $files;
}

function qdm_replace_asset($targetKey, $sourcePublicPath)
{
    $targets = qdm_asset_targets();
    if (!isset($targets[$targetKey])) {
        return array(false, '图片位置不存在。');
    }
    $sourcePublicPath = qdm_normalize_public_path($sourcePublicPath);
    $source = qdm_site_path($sourcePublicPath);
    if ($source === '' || !is_file($source)) {
        return array(false, '上传图片不存在。');
    }
    $targetPublicPath = $targets[$targetKey]['path'];
    $target = qdm_site_path($targetPublicPath);
    if ($target === '') {
        return array(false, '目标路径无效。');
    }
    $sourceExt = strtolower(pathinfo($source, PATHINFO_EXTENSION));
    $targetExt = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    if ($sourceExt === 'jpeg') {
        $sourceExt = 'jpg';
    }
    if ($targetExt === 'jpeg') {
        $targetExt = 'jpg';
    }
    if ($sourceExt !== $targetExt) {
        return array(false, '图片格式需要和原图一致，当前目标需要 .' . $targetExt . ' 文件。');
    }
    $targetDir = dirname($target);
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        return array(false, '目标目录不可写。');
    }
    if (is_file($target)) {
        $backup = $target . '.bak';
        if (!is_file($backup)) {
            copy($target, $backup);
        }
    }
    if (!copy($source, $target)) {
        return array(false, '替换失败，请检查文件权限。');
    }
    return array(true, $targetPublicPath);
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
