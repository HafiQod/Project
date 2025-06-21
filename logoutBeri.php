<?php
// logoutBeri.php
session_start();
require_once 'config.php';

// Logout penyumbang
UserAuth::logoutPenyumbang();

// Redirect to login page
header('Location: loginBeri.php?message=logout_success');
exit();
?>