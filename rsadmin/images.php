<?php
require __DIR__ . '/bootstrap.php';
qdm_require_login();

$targets = qdm_asset_targets();
$files = qdm_list_media_files();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!qdm_check_csrf()) {
        qdm_flash('error', '请求无效，请重试。');
        header('Location: images.php');
        exit;
    }

    $targetKey = (string) ($_POST['target'] ?? '');
    $sourcePath = qdm_normalize_public_path($_POST['source_path'] ?? '');

    if (!empty($_FILES['image']['name'])) {
        list($ok, $result) = qdm_handle_upload('image');
        if (!$ok) {
            qdm_flash('error', $result);
            header('Location: images.php');
            exit;
        }
        $sourcePath = $result;
    }

    list($ok, $result) = qdm_replace_asset($targetKey, $sourcePath);
    if ($ok) {
        qdm_flash('success', '已替换：' . $targets[$targetKey]['label']);
    } else {
        qdm_flash('error', $result);
    }
    header('Location: images.php');
    exit;
}

qdm_admin_header('图片替换');
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-title">名商后台</div>
        <nav>
            <a href="dashboard.php">站点设置</a>
            <a class="active" href="images.php">图片替换</a>
            <a href="media.php">媒体库</a>
            <a href="../index.php" target="_blank">预览首页</a>
            <a href="logout.php">退出登录</a>
        </nav>
    </aside>
    <main class="panel">
        <header class="panel-header">
            <div>
                <h1>图片替换</h1>
                <p>选择页面里的常用图片位置，上传新图后会覆盖原图片路径，所有引用该图片的页面都会生效。</p>
            </div>
            <a class="ghost-button" href="media.php">去媒体库</a>
        </header>

        <section class="form-section">
            <h2>上传并替换</h2>
            <form method="post" enctype="multipart/form-data" class="settings-form compact-form">
                <input type="hidden" name="csrf" value="<?php echo qdm_h(qdm_csrf_token()); ?>">
                <label>
                    <span>图片位置</span>
                    <select name="target" required>
                        <?php foreach ($targets as $key => $target): ?>
                            <option value="<?php echo qdm_h($key); ?>"><?php echo qdm_h($target['label']); ?> - <?php echo qdm_h($target['path']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>上传新图片</span>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                </label>
                <label>
                    <span>或者选择媒体库路径</span>
                    <select name="source_path">
                        <option value="">不从媒体库选择</option>
                        <?php foreach ($files as $file): ?>
                            <option value="<?php echo qdm_h($file['path']); ?>"><?php echo qdm_h($file['path']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <div class="form-actions inline-actions">
                    <button type="submit">替换图片</button>
                </div>
                <p class="help-text">如果同时上传图片和选择媒体库路径，会优先使用新上传的图片。</p>
            </form>
        </section>

        <section class="form-section">
            <h2>当前常用图片</h2>
            <div class="asset-grid">
                <?php foreach ($targets as $key => $target): ?>
                    <?php $filePath = qdm_site_path($target['path']); ?>
                    <article class="asset-card">
                        <div class="asset-preview">
                            <?php if ($filePath && is_file($filePath)): ?>
                                <img src="../<?php echo qdm_h($target['path']); ?>?v=<?php echo qdm_h((string) filemtime($filePath)); ?>" alt="">
                            <?php else: ?>
                                <span>缺图</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong><?php echo qdm_h($target['label']); ?></strong>
                            <code><?php echo qdm_h($target['path']); ?></code>
                            <p><?php echo qdm_h($target['hint']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>
<?php qdm_admin_footer(); ?>
