<?php
/** Keluar dari CMS admin. */
require_once dirname(__DIR__) . '/app/bootstrap.php';
requireLogin();

$logoutFlash = ['type' => 'success', 'message' => 'Anda telah keluar dari sistem CMS dengan aman.'];
$_SESSION['flash'] = $logoutFlash;
do_logout();
redirect('admin/login.php');
