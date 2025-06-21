<?php
// logoutTerima.php
session_start();
require_once 'config.php';

// Logout penerima
UserAuth::logoutPenerima();

// Redirect to login page
header('Location: loginTerima.php?message=logout_success');
exit();
?>