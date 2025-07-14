<?php

session_start();
require_once '../functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$membre = getMembreProfile($_SESSION['user']['id_membre']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $date_naissance = $_POST['date_naissance'];
    $genre = $_POST['genre'];
    $email = $_POST['email'];
    $ville = $_POST['ville'];
    $image_profil = $membre['image_profil'];

    if (isset($_FILES['image_profil']) && $_FILES['image_profil']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image_profil']['tmp_name'];
        $fileName = uniqid() . '_' . basename($_FILES['image_profil']['name']);
        $targetDir = '../assets/images/profiles/';
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($fileTmp, $targetFile)) {
            $image_profil = $fileName;
        }
    }

    global $mysqli;
    $id = $_SESSION['user']['id_membre'];
    $nom = mysqli_real_escape_string($mysqli, $nom);
    $date_naissance = mysqli_real_escape_string($mysqli, $date_naissance);
    $genre = mysqli_real_escape_string($mysqli, $genre);
    $email = mysqli_real_escape_string($mysqli, $email);
    $ville = mysqli_real_escape_string($mysqli, $ville);
    $image_profil = mysqli_real_escape_string($mysqli, $image_profil);

    $query = "UPDATE membre SET nom='$nom', date_naissance='$date_naissance', genre='$genre', email='$email', ville='$ville', image_profil='$image_profil' WHERE id_membre='$id'";
    if (mysqli_query($mysqli, $query)) {
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['email'] = $email;
        header('Location: profile.php');
        exit();
    } else {
        $error = "Erreur lors de la mise à jour";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier mon profil</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier mon profil</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3 text-center">
                <?php if ($membre['image_profil']) { ?>
                    <img src="../assets/images/profiles/<?= htmlspecialchars($membre['image_profil']) ?>" class="rounded-circle mb-2" width="120" height="120">
                <?php } else { ?>
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mb-2" style="width:120px; height:120px; margin:0 auto;">Pas de photo</div>
                <?php } ?>
                <input type="file" name="image_profil" class="form-control mt-2" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom complet</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($membre['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="date_naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($membre['date_naissance']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select class="form-select" id="genre" name="genre" required>
                    <option value="Homme" <?= $membre['genre'] === 'Homme' ? 'selected' : '' ?>>Homme</option>
                    <option value="Femme" <?= $membre['genre'] === 'Femme' ? 'selected' : '' ?>>Femme</option>
                    <option value="Autre" <?= $membre['genre'] === 'Autre' ? 'selected' : '' ?>>Autre</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($membre['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?= htmlspecialchars($membre['ville']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="profile.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>