<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: pages/objets.php');
} else {
    header('Location: pages/login.php');
}
exit();
?>