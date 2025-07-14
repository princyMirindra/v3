<?php
session_start();
require_once '../functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id_image']) && isset($_GET['id_objet'])) {
    $id_image = (int)$_GET['id_image'];
    $id_objet = (int)$_GET['id_objet'];

    global $mysqli;
    $sql = "SELECT nom_image FROM images_object WHERE id_image = $id_image";
    $result = mysqli_query($mysqli, $sql);
    $image = mysqli_fetch_assoc($result);

    if ($image) {
        $file = '../assets/images/' . $image['nom_image'];
        if (file_exists($file)) {
            unlink($file);
        }
        mysqli_query($mysqli, "DELETE FROM images_object WHERE id_image = $id_image");
    }

    header("Location: objet.php?id=$id_objet");
    exit();
} else {
    echo "Paramètres manquants.";
}
?>