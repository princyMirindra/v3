<?php
$host = 'localhost';
$username = 'ETU003927';
$password = 'bwWLtSAs';
$database = 'db_s2_ETU003927';

$mysqli = mysqli_connect($host, $username, $password, $database);

if (mysqli_connect_errno()) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}
?>