<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
requireLogin();

redirect('admin/dashboard.php');
