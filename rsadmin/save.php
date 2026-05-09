<?php
require __DIR__ . '/bootstrap.php';
qdm_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !qdm_check_csrf()) {
    qdm_flash('error', '请求无效，请重试。');
    header('Location: dashboard.php');
    exit;
}

if (!qdm_save_settings($_POST)) {
    qdm_flash('error', '保存失败，请确认 rsadmin/data 目录可写。');
    header('Location: dashboard.php');
    exit;
}

qdm_flash('success', '设置已保存，打开前台 index.php 可查看效果。');
header('Location: dashboard.php');
