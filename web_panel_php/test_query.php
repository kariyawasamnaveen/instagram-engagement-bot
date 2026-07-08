<?php
define('ENVIRONMENT', 'development');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_URI'] = '/order?status=processing';
$_GET['status'] = 'processing';

// Mock session
session_start();
$_SESSION['uid'] = 1;

require 'index.php';
