<?php
session_start();

// Remove all session data
session_unset();
session_destroy();

// Delete the cookies we set on login
setcookie('zuri_user_name', '', time() - 3600, '/');
setcookie('zuri_user_role', '', time() - 3600, '/');

// Redirect to login
header('Location: /shop/login.php');
exit;
?>