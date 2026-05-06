<?php
ob_start();
require_once 'config.php';

$_SESSION = [];
session_destroy();

header('Location: /');
exit;
