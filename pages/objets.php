<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../functions.php';
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

// Traitement emprunt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_objet'], $_POST['jours'])) {
    $id_objet = (int)$_POST['id_objet'];
    $jours = (int)$_POST['jours'];
    $date_disponible = date('Y-m-d', strtotime("+$jours days"));

    $stmt = $mysqli->prepare("UPDATE objet SET disponible = 0, date_disponible = ? WHERE id_objet = ?");
    $stmt->bind_param('si', $date_disponible, $id_objet);
    $stmt->execute();
    $stmt->close();

    header("Location: objets.php");
    exit();
}

$categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : null;
$nom = isset($_GET['nom']) ? $_GET['nom'] : null;
$disponible = isset($_GET['disponible']) && $_GET['disponible'] == 'on';

$objets = getObjets($categorieId, $nom, $disponible);
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des objets</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container mt-4">
        <h2>Liste des objets</h2>
        <form method="get" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="categorie" class="form-select">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat) { ?>
                        <option value="<?php echo $cat['id_categorie']; ?>" <?php if ($categorieId == $cat['id_categorie']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['nom_categorie']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="nom" class="form-control" placeholder="Nom de l'objet" >
            </div>
            <div class="col-md-2">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="disponible" id="disponible" <?php if ($disponible) echo 'checked'; ?>>
                    <label class="form-check-label" for="disponible">Disponible uniquement</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>
        </form>
        <div class="row">
            <?php foreach ($objets as $objet) { 
                $images = getImagesByObjet($objet['id_objet']);
                $image_principale = isset($images[0]['nom_image']) ? $images[0]['nom_image'] : 'default.jpg';
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="../assets/images/<?php echo $image_principale; ?>" class="card-img-top" alt="Image objet" width="200" height="250">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($objet['nom_objet']); ?></h5>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($objet['nom_categorie']); ?></span>
                            <?php if ($objet['disponible']) { ?>
                                <span class="badge bg-success float-end">Disponible</span>
                                <button type="button" class="btn btn-warning btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#emprunterModal<?php echo $objet['id_objet']; ?>">
                                    Emprunter
                                </button>
                                <!-- Modal emprunter -->
                                <div class="modal fade" id="emprunterModal<?php echo $objet['id_objet']; ?>" tabindex="-1" aria-labelledby="emprunterLabel<?php echo $objet['id_objet']; ?>" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <form method="post" action="objets.php">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="emprunterLabel<?php echo $objet['id_objet']; ?>">Emprunter l'objet</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                          <input type="hidden" name="id_objet" value="<?php echo $objet['id_objet']; ?>">
                                          <div class="mb-3">
                                            <label for="jours<?php echo $objet['id_objet']; ?>" class="form-label">Nombre de jours</label>
                                            <input type="number" class="form-control" id="jours<?php echo $objet['id_objet']; ?>" name="jours" min="1" required>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="submit" class="btn btn-primary">Valider</button>
                                        </div>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                            <?php } else { ?>
                                <span class="badge bg-warning float-end">
                                    Emprunté
                                    <?php if (!empty($objet['date_disponible'])) {
                                        echo ' (Disponible le ' . date('d/m/Y', strtotime($objet['date_disponible'])) . ')';
                                    } ?>
                                </span>
                            <?php } ?>
                            <a href="objet.php?id=<?php echo $objet['id_objet']; ?>" class="btn btn-outline-primary mt-2">Voir la fiche</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>