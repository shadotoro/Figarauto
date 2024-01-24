<?php
require_once 'config/Database.php';

if (isset($_GET['ad_id'])) {
    $ad_Id = $_GET['ad_id'];

    $db = new Database();
    $conn = $db->connect();
    $advertisementModel = new Advertisement($conn);
    $advertisement = $advertisementModel->read($ad_Id);

    if ($advertisement) {
?>
        <!DOCTYPE html>
        <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Détails de l'annonce</title>
            </head>
            <body>
                <h1><?php echo htmlspecialchars($advertisement['title']); ?></h1>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($advertisement['description']); ?></p>
                <p><strong>Prix :</strong> <?php echo htmlspecialchars($advertisement['price']);?></p>
                <p><strong>Marque :</strong> <?php echo htmlspecialchars($advertisement['marque']);?></p>
                <p><strong>Modele :</strong> <?php echo htmlspecialchars($advertisement['modele']);?></p>
                <p><strong>Carburant :</strong> <?php echo htmlspecialchars($advertisement['fuel']);?></p>
                <p><strong>Couleur :</strong> <?php echo htmlspecialchars($advertisement['color']);?></p>
                <p><strong>Année de fabrication :</strong> <?php echo htmlspecialchars($advertisement['annee_de_fabrication']);?></p>
                <p><strong>Mise en circulation :</strong> <?php echo htmlspecialchars($advertisement['mise_en_circulation']);?></p>
                <p><strong>Kilometrage :</strong> <?php echo htmlspecialchars($advertisement['kilometrage']);?></p>
                <p><strong>Finition :</strong> <?php echo htmlspecialchars($advertisement['finition']);?></p>
                <p><strong>Version :</strong> <?php echo htmlspecialchars($advertisement['version']);?></p>
                <p><strong>Boite de vitesse :</strong> <?php echo htmlspecialchars($advertisement['boite_de_vitesse']);?></p>
                <p><strong>Portieres :</strong> <?php echo htmlspecialchars($advertisement['portieres']);?></p>
                <p><strong>DIN :</strong> <?php echo htmlspecialchars($advertisement['DIN']);?></p>
                <p><strong>Permis :</strong> <?php echo htmlspecialchars($advertisement['permis']);?></p>
                <p><strong>Critair :</strong> <?php echo htmlspecialchars($advertisement['critair']);?></p>
                <p><a href="index.php?controller=annonce&action=edit&ad_id=<?php echo $ad_id;?>">Editer l'annonce</a></p>
                <p><a href="index.php?controller=annonce&action=delete&ad_id=<?php echo $ad_id;?>">Supprimer l'annonce</a></p>

                <a href="index.php?controller=user&action=dashboard">Retour à votre tableau de bord.</a>

                <p><a href="index.php?controller=auth&action=logout">Se déconnecter</a></p>
            </body>
            </html>
            <?php
    } else {
        echo "Annonce introuvable !";
    }
} else {
    echo "ID de l'annonce non fourni !";
}
?>