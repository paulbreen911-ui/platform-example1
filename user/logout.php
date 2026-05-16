<?php

$page_title = 'Log Out';

require_once __DIR__ . '/../config.php';
require_once ROOT_PATH . '/functions.php';
include ROOT_PATH . '/required/header.php';

$_SESSION = [];
session_destroy();

header('Location: /');
exit;
