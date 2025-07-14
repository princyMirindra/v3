<?php
session_start();
require_once '../functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$membre = getMembreProfile($_SESSION['user']['id_membre']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Mon Profil</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <?php if ($membre['image_profil']) { ?>
                            <img src="../assets/images/profiles/<?php echo htmlspecialchars($membre['image_profil']); ?>" 
                                 class="rounded-circle mb-3" width="150" height="150">
                        <?php } else { ?>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mb-3" 
                                 style="width:150px; height:150px; margin:0 auto;">
                                Pas de photo
                            </div>
                        <?php } ?>
                        <h4><?php echo htmlspecialchars($membre['nom']); ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Informations personnelles</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Date de naissance:</strong> <?php echo date('d/m/Y', strtotime($membre['date_naissance'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Genre:</strong> <?php echo htmlspecialchars($membre['genre']); ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($membre['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Ville:</strong> <?php echo htmlspecialchars($membre['ville']); ?></p>
                            </div>
                        </div>
                        <a href="edit_profile.php" class="btn btn-primary">Modifier mon profil</a>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Mes objets</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $mesObjets = getObjetsByMembre($_SESSION['user']['id_membre']);
                        if (count($mesObjets) > 0) { 
                            // Regrouper les objets par catégorie
                            $objetsParCategorie = [];
                            foreach ($mesObjets as $objet) {
                                $cat = $objet['nom_categorie'];
                                if (!isset($objetsParCategorie[$cat])) {
                                    $objetsParCategorie[$cat] = [];
                                }
                                $objetsParCategorie[$cat][] = $objet;
                            }
                        ?>
                            <?php foreach ($objetsParCategorie as $categorie => $objets) { ?>
                                <h6 class="mt-3 mb-2 text-primary"><?php echo htmlspecialchars($categorie); ?></h6>
                                <div class="row">
                                    <?php foreach ($objets as $objet) { ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6><?php echo htmlspecialchars($objet['nom_objet']); ?></h6>
                                                    <?php if ($objet['date_retour']) { ?>
                                                        <span class="badge bg-warning float-end">Emprunté</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-success float-end">Disponible</span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p>Vous n'avez pas encore ajouté d'objets.</p>
                        <?php } ?>
                        <a href="add_object.php" class="btn btn-success">Ajouter un objet</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>