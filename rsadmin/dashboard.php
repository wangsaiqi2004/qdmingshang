<?php
require __DIR__ . '/bootstrap.php';
qdm_require_login();

$settings = qdm_load_settings();
qdm_admin_header('控制台');
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-title">名商后台</div>
        <nav>
            <a class="active" href="dashboard.php">站点设置</a>
            <a href="images.php">图片替换</a>
            <a href="media.php">媒体库</a>
            <a href="../index.php" target="_blank">预览首页</a>
            <a href="logout.php">退出登录</a>
        </nav>
    </aside>
    <main class="panel">
        <header class="panel-header">
            <div>
                <h1>站点设置</h1>
                <p>测试版后台：支持基础信息、图片上传和常用页面图片替换。</p>
            </div>
            <a class="ghost-button" href="../index.php" target="_blank">打开前台</a>
        </header>

        <form method="post" action="save.php" class="settings-form">
            <input type="hidden" name="csrf" value="<?php echo qdm_h(qdm_csrf_token()); ?>">
            <section class="form-section">
                <h2>SEO 信息</h2>
                <label>
                    <span>首页标题</span>
                    <input type="text" name="site_title" value="<?php echo qdm_h($settings['site_title']); ?>">
                </label>
                <label>
                    <span>关键词</span>
                    <textarea name="keywords" rows="3"><?php echo qdm_h($settings['keywords']); ?></textarea>
                </label>
                <label>
                    <span>描述</span>
                    <textarea name="description" rows="3"><?php echo qdm_h($settings['description']); ?></textarea>
                </label>
            </section>

            <section class="form-section">
                <h2>企业信息</h2>
                <label>
                    <span>公司名称</span>
                    <input type="text" name="company_name" value="<?php echo qdm_h($settings['company_name']); ?>">
                </label>
                <label>
                    <span>欢迎语</span>
                    <input type="text" name="welcome_text" value="<?php echo qdm_h($settings['welcome_text']); ?>">
                </label>
                <div class="form-grid">
                    <label>
                        <span>电话一</span>
                        <input type="text" name="phone_1" value="<?php echo qdm_h($settings['phone_1']); ?>">
                    </label>
                    <label>
                        <span>电话二</span>
                        <input type="text" name="phone_2" value="<?php echo qdm_h($settings['phone_2']); ?>">
                    </label>
                    <label>
                        <span>手机/底部联系方式</span>
                        <input type="text" name="phone_3" value="<?php echo qdm_h($settings['phone_3']); ?>">
                    </label>
                </div>
                <label>
                    <span>地址</span>
                    <input type="text" name="address" value="<?php echo qdm_h($settings['address']); ?>">
                </label>
            </section>

            <section class="form-section">
                <h2>页脚</h2>
                <div class="form-grid">
                    <label>
                        <span>版权</span>
                        <input type="text" name="copyright" value="<?php echo qdm_h($settings['copyright']); ?>">
                    </label>
                    <label>
                        <span>备案号</span>
                        <input type="text" name="icp" value="<?php echo qdm_h($settings['icp']); ?>">
                    </label>
                    <label>
                        <span>技术支持文字</span>
                        <input type="text" name="support" value="<?php echo qdm_h($settings['support']); ?>">
                    </label>
                </div>
            </section>

            <div class="form-actions">
                <button type="submit">保存设置</button>
                <a href="../index.php" target="_blank">预览</a>
            </div>
        </form>
    </main>
</div>
<?php qdm_admin_footer(); ?>
