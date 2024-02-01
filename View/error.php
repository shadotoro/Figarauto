<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$userErrorMessage = isset($_SESSION['error_message'])? $_SESSION['error_message'] : '';
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Erreur</title>
</head>
<body>
    <header class="container">
        <h1 class="row col col-12">Oups !</h1>
    </header>
    <main class="container">
        <section class="error-container row col col-12">
            <p><?php echo htmlspecialchars($userErrorMessage); ?></p>
            <p>Si le problème persiste , contacter l'administrateur du site.</p>
            <a href="home.phtml">Retour à l'accueil</a>
        </section>
    </main>
</body>
</html>
