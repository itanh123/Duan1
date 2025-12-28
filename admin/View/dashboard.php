<?php
// Dashboard content - sử dụng layout
ob_start();
require_once('./admin/View/dashboard_content.php');
$content = ob_get_clean();

$pageTitle = 'Dashboard';
require_once('./admin/View/layout.php');
?>
