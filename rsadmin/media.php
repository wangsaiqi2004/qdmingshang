<?php
require __DIR__ . '/bootstrap.php';
qdm_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!qdm_check_csrf()) {
        qdm_flash('error', '请求无效，请重试。');
        header('Location: media.php');
        exit;
    }
    list($ok, $result) = qdm_handle_upload('image');
    if ($ok) {
        qdm_flash('success', '图片已上传：' . $result);
    } else {
        qdm_flash('error', $result);
    }
    header('Location: media.php');
    exit;
}

$files = qdm_list_media_files();
qdm_admin_header('媒体库');
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-title">名商后台</div>
        <nav>
            <a href="dashboard.php">站点设置</a>
            <a href="images.php">图片替换</a>
            <a class="active" href="media.php">媒体库</a>
            <a href="../index.php" target="_blank">预览首页</a>
            <a href="logout.php">退出登录</a>
        </nav>
    </aside>
    <main class="panel">
        <header class="panel-header">
            <div>
                <h1>媒体库</h1>
                <p>上传图片后可以复制路径，也可以在“图片替换”里直接替换到页面常用位置。</p>
            </div>
        </header>

        <section class="form-section">
            <h2>上传图片</h2>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <input type="hidden" name="csrf" value="<?php echo qdm_h(qdm_csrf_token()); ?>">
                <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                <button type="submit">上传到媒体库</button>
            </form>
            <p class="help-text">支持 JPG、PNG、GIF、WEBP，单张不超过 6MB。</p>
        </section>

        <section class="form-section">
            <h2>已上传图片</h2>
            <?php if (!$files): ?>
                <p class="empty-text">还没有上传图片。</p>
            <?php else: ?>
                <div class="media-grid">
                    <?php foreach ($files as $file): ?>
                        <article class="media-card">
                            <div class="media-thumb">
                                <img src="../<?php echo qdm_h($file['path']); ?>" alt="">
                            </div>
                            <div class="media-info">
                                <strong><?php echo qdm_h(basename($file['path'])); ?></strong>
                                <code><?php echo qdm_h($file['path']); ?></code>
                                <span><?php echo qdm_h(round($file['size'] / 1024, 1)); ?> KB</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<?php qdm_admin_footer(); ?>
