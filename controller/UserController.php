<?php
// fichiers nécessaires
require_once 'Model/User.php';
require_once 'Model/Advertisement.php';
require_once 'config/Database.php';

class UserController { // déclaration classe
    private $userModel; // propriété pour modèle user
    private $advertisementModel; // 

    public function __construct() { // constructeur de la classe
        $this->userModel = new User(); // initialisation de $usermodel
        $db = new Database(); // création d'un nouvel objet database
        $this->advertisementModel = new Advertisement($db->connect()); // initialisation de $advertisementmodel avec co' à la bdd
    }

    public function dashboardAction() {
        if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) { // vérifie si l'identifiant est défini et non vide
            header("location: index.php?controller=auth&action=login"); // redirection vers la page de co'
            exit(); // arrête l'exécution du script
        }
        $userId = $_SESSION["user_id"]; // stocke l'id de l'utilisateur dans une variable
        $user = $this->userModel->getUserById($_SESSION["user_id"]); // récup l'utilisateur par son id
        
        $userAds = $this->advertisementModel->getAdsWithImagesByUserId($userId); // récupère les annonces de l'utilisateur par son id
        require("View/dashboard.phtml"); // inclut le fichier de vue pour le dashboard
    }
}