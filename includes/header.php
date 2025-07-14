<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Emprunt d'objets</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../pages/objets.php">Objets</a>
                </li>
                <?php if (isset($_SESSION['user'])) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/profile.php">Mon profil</a>
                </li>
                <?php } ?>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user'])) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/logout.php">Déconnexion</a>
                </li>
                <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/login.php">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../pages/register.php">Inscription</a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>