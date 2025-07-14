<?php
session_start();
require_once '../functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_objet = $_POST['nom_objet'];
    $id_categorie = $_POST['id_categorie'];
    $id_membre = $_SESSION['user']['id_membre'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    global $mysqli;
    $nom_objet = mysqli_real_escape_string($mysqli, $nom_objet);
    $id_categorie = (int)$id_categorie;
    $id_membre = (int)$id_membre;

    $sql = "INSERT INTO objet (nom_objet, id_categorie, id_membre, disponible) VALUES ('$nom_objet', $id_categorie, $id_membre, $disponible)";
    if (mysqli_query($mysqli, $sql)) {
        $id_objet = mysqli_insert_id($mysqli);

        // Upload des images
        if (!empty($_FILES['images_objet']['name'][0])) {
            foreach ($_FILES['images_objet']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images_objet']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['images_objet']['name'][$key]);
                    $targetDir = '../assets/images/';
                    $targetFile = $targetDir . $fileName;
                    if (move_uploaded_file($tmp_name, $targetFile)) {
                        $img_query = "INSERT INTO images_object (id_objet, nom_image) VALUES ($id_objet, '" . mysqli_real_escape_string($mysqli, $fileName) . "')";
                        mysqli_query($mysqli, $img_query);
                    }
                }
            }
        }
        header('Location: profile.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un objet</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container mt-5">
        <h2>Ajouter un objet</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom_objet" class="form-label">Nom de l'objet</label>
                <input type="text" class="form-control" id="nom_objet" name="nom_objet" required>
            </div>
            <div class="mb-3">
                <label for="id_categorie" class="form-label">Catégorie</label>
                <select class="form-select" id="id_categorie" name="id_categorie" required>
                    <?php foreach ($categories as $cat) { ?>
                        <option value="<?php echo $cat['id_categorie']; ?>"><?php echo htmlspecialchars($cat['nom_categorie']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="images_objet" class="form-label">Images de l'objet</label>
                <input type="file" class="form-control" id="images_objet" name="images_objet[]" accept="image/*" multiple>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="disponible" name="disponible" checked>
                <label class="form-check-label" for="disponible">Disponible</label>
            </div>
            <button type="submit" class="btn btn-success">Ajouter</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>