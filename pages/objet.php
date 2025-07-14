<?php
session_start();
require_once '../functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$id_objet = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$objet = getObjet($id_objet);
$images = getImagesByObjet($id_objet);
$emprunts = getEmpruntsByObjet($id_objet);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['retourner'])) {
    global $mysqli;
    $date_retour = date('Y-m-d');
    $etat_retour = $_POST['etat_retour'] ?? 'OK';
    
    $update_emprunt = "UPDATE emprunt SET date_retour = '$date_retour', etat_retour = '$etat_retour' 
                      WHERE id_objet = $id_objet AND date_retour IS NULL 
                      ORDER BY date_emprunt DESC LIMIT 1";
    mysqli_query($mysqli, $update_emprunt);
    
    $update_objet = "UPDATE objet SET disponible = 1, date_disponible = NULL WHERE id_objet = $id_objet";
    mysqli_query($mysqli, $update_objet);
    
    header("Location: objet.php?id=$id_objet");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche objet</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="container mt-4">
        <h2><?php echo htmlspecialchars($objet['nom_objet']); ?></h2>
        <div class="row">
            <div class="col-md-6">
                <?php if (count($images) > 0) { ?>
                    <div id="carouselImages" class="carousel slide mb-3" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($images as $key => $img) { ?>
                                <div class="carousel-item <?php if ($key == 0) echo 'active'; ?>">
                                    <img src="../assets/images/<?php echo $img['nom_image']; ?>" class="d-block w-100" alt="Image objet">
                                    <?php if ($_SESSION['user']['id_membre'] == $objet['id_membre']) { ?>
                                        <a href="delete_image.php?id_image=<?php echo $img['id_image']; ?>&id_objet=<?php echo $objet['id_objet']; ?>" 
                                           class="btn btn-danger btn-sm mt-2"
                                           onclick="return confirm('Voulez-vous vraiment supprimer cette image ?');">
                                            Supprimer
                                        </a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                <?php } else { ?>
                    <img src="../assets/images/default.jpg" class="img-fluid" alt="Image par défaut">
                <?php } ?>
            </div>
            <div class="col-md-6">
                <p><strong>Catégorie :</strong> <?php echo htmlspecialchars($objet['nom_categorie']); ?></p>
                <p><strong>Ajouté par :</strong> <?php echo htmlspecialchars($objet['nom_membre']); ?></p>
                <p><strong>Statut :</strong>
                    <?php if ($objet['disponible']) { ?>
                        <span class="badge bg-success">Disponible</span>
                    <?php } else { ?>
                        <span class="badge bg-warning">Emprunté
                            <?php if (!empty($objet['date_disponible'])) {
                                echo ' (Disponible le ' . date('d/m/Y', strtotime($objet['date_disponible'])) . ')';
                            } ?>
                        </span>
                        
                        <?php 
                        $is_emprunte = false;
                        if (count($emprunts) > 0) {
                            $last_emprunt = end($emprunts);
                            $is_emprunte = empty($last_emprunt['date_retour']);
                        }
                        
                        if ($is_emprunte && $_SESSION['user']['id_membre'] == $objet['id_membre']) { ?>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#retournerModal">
                                Retourner
                            </button>
                            
                            <div class="modal fade" id="retournerModal" tabindex="-1" aria-labelledby="retournerModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="post">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="retournerModalLabel">Retour de l'objet</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">État de l'objet :</label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="etat_retour" id="etatOK" value="OK" checked>
                                                        <label class="form-check-label" for="etatOK">OK</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="etat_retour" id="etatAbime" value="ABIMÉ">
                                                        <label class="form-check-label" for="etatAbime">Abîmé</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" name="retourner" class="btn btn-primary">Confirmer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </p>
                
                <h5>Historique des emprunts</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date emprunt</th>
                            <th>Date retour</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprunts as $emprunt) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($emprunt['date_emprunt']); ?></td>
                                <td><?php echo $emprunt['date_retour'] ? htmlspecialchars($emprunt['date_retour']) : 'Non retourné'; ?></td>
                                <td>
                                    <?php if (!empty($emprunt['etat_retour'])) { ?>
                                        <span class="badge <?php echo $emprunt['etat_retour'] == 'OK' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo htmlspecialchars($emprunt['etat_retour']); ?>
                                        </span>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>