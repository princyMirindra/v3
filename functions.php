<?php
require_once '../config/database.php';

function getCategories() {
    global $mysqli;
    $query = "SELECT * FROM categorie_object";
    $result = mysqli_query($mysqli, $query);
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    return $categories;
}

function getObjets($categorieId = null, $nom = null, $disponible = false) {
    global $mysqli;
    $where = [];
    if ($categorieId) $where[] = "o.id_categorie = " . intval($categorieId);
    if ($nom) $where[] = "o.nom_objet LIKE '%" . mysqli_real_escape_string($mysqli, $nom) . "%'";
    if ($disponible) $where[] = "o.disponible = 1";
    $sql = "SELECT o.*, c.nom_categorie FROM objet o JOIN categorie_object c ON o.id_categorie = c.id_categorie";
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $result = mysqli_query($mysqli, $sql);
    $objets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $objets[] = $row;
    }
    return $objets;
}

function getObjetsByMembre($id_membre) {
    global $mysqli;
    $sql = "SELECT o.*, c.nom_categorie FROM objet o JOIN categorie_object c ON o.id_categorie = c.id_categorie WHERE o.id_membre = " . intval($id_membre);
    $result = mysqli_query($mysqli, $sql);
    $objets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $objets[] = $row;
    }
    return $objets;
}

// Récupérer les images d'un objet
function getImagesByObjet($id_objet) {
    global $mysqli;
    $sql = "SELECT * FROM images_object WHERE id_objet = " . intval($id_objet) . " ORDER BY id_image ASC";
    $result = mysqli_query($mysqli, $sql);
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

function getMembreProfile($id_membre) {
    global $mysqli;
    $sql = "SELECT * FROM membre WHERE id_membre = " . intval($id_membre);
    $result = mysqli_query($mysqli, $sql);
    return mysqli_fetch_assoc($result);
}

function getObjet($id_objet) {
    global $mysqli;
    $sql = "SELECT o.*, c.nom_categorie, m.nom as nom_membre FROM objet o JOIN categorie_object c ON o.id_categorie = c.id_categorie JOIN membre m ON o.id_membre = m.id_membre WHERE o.id_objet = " . intval($id_objet);
    $result = mysqli_query($mysqli, $sql);
    return mysqli_fetch_assoc($result);
}

function getEmpruntsByObjet($id_objet) {
    global $mysqli;
    $sql = "SELECT * FROM emprunt WHERE id_objet = " . intval($id_objet) . " ORDER BY date_emprunt DESC";
    $result = mysqli_query($mysqli, $sql);
    $emprunts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $emprunts[] = $row;
    }
    return $emprunts;
}

function loginUser($email, $password) {
    global $mysqli;
    $sql = "SELECT * FROM membre WHERE email = '" . mysqli_real_escape_string($mysqli, $email) . "' AND mdp = '" . mysqli_real_escape_string($mysqli, $password) . "'";
    $result = mysqli_query($mysqli, $sql);
    return mysqli_fetch_assoc($result);
}

function registerUser($data) {
    global $mysqli;
    $sql = "INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp) VALUES (
        '" . mysqli_real_escape_string($mysqli, $data['nom']) . "',
        '" . mysqli_real_escape_string($mysqli, $data['date_naissance']) . "',
        '" . mysqli_real_escape_string($mysqli, $data['genre']) . "',
        '" . mysqli_real_escape_string($mysqli, $data['email']) . "',
        '" . mysqli_real_escape_string($mysqli, $data['ville']) . "',
        '" . mysqli_real_escape_string($mysqli, $data['mdp']) . "'
    )";
    return mysqli_query($mysqli, $sql);
}
?>