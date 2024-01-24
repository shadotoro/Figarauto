<?php
class AnnonceController {
    private $db; // stocke l'instance de la bdd
    private $conn; // co' à la bdd

    public function __construct() { // constructeur qui démarre la session si elle n'est pas active, instancie la bdd et établit la co'        
        if (session_status() == PHP_SESSION_NONE) { // vérif si 1 session est démarrée sinon en démarre une
            session_start();
        }
        $this->db = new Database(); // crée une nouvelle instance de la bdd et se co'
        $this->conn = $this->db->connect();
    }

    public function createAction() { // méthode pour créer une nouvelle annonce
        $lastAdId = null;
        $annonce = new Advertisement($this->conn); // crée une new instance de advertisement avec la co' à la bdd
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // vérif si la requête de la méthode est POST
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING); // récup et assainit les données envoyées par l'utilisateur via le form
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
            $fuel = filter_input(INPUT_POST, 'fuel', FILTER_SANITIZE_STRING);
            $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
            $modele = filter_input(INPUT_POST, 'modele', FILTER_SANITIZE_STRING);
            $marque = filter_input(INPUT_POST, 'marque', FILTER_SANITIZE_STRING);
            $annee_de_fabrication = filter_input(INPUT_POST, 'annee_de_fabrication', FILTER_SANITIZE_STRING);
            $mise_en_circulation = filter_input(INPUT_POST, 'mise_en_circulation', FILTER_SANITIZE_STRING);
            $kilometrage = filter_input(INPUT_POST, 'kilometrage', FILTER_SANITIZE_STRING);
            $finition = filter_input(INPUT_POST, 'finition', FILTER_SANITIZE_STRING);
            $version = filter_input(INPUT_POST, 'version', FILTER_SANITIZE_STRING);
            $boite_de_vitesse = filter_input(INPUT_POST, 'boite_de_vitesse', FILTER_SANITIZE_STRING);
            $portieres = filter_input(INPUT_POST, 'portieres', FILTER_SANITIZE_STRING);
            $DIN = filter_input(INPUT_POST, 'DIN', FILTER_SANITIZE_STRING);
            $permis = filter_input(INPUT_POST, 'permis', FILTER_SANITIZE_STRING);
            $critair = filter_input(INPUT_POST, 'critair', FILTER_SANITIZE_STRING);

            
            if (!isset($_SESSION['user_id'])) { // vérifie si l'utilisateur est co' avant de permettre la création de l'annonce
                die("Vous devez être connecté pour créer une annonce.");
            }

            
            $data = [ // prépare les données pour l'insertion dans la bdd
                'user_id' => $_SESSION['user_id'],
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'fuel' => $fuel,
                'color' => $color,
                'modele' => $modele,
                'marque' => $marque,
                'annee_de_fabrication' => $annee_de_fabrication,
                'mise_en_circulation' => $mise_en_circulation,
                'finition' => $finition,
                'version' => $version,
                'kilometrage' => $kilometrage,
                'boite_de_vitesse' => $boite_de_vitesse,
                'portieres' => $portieres,
                'DIN' => $DIN,
                'permis' => $permis,
                'critair' => $critair,
            ];

            $lastAdId = $annonce->create($data);// apl la méthode create de Advertisement pour insérer les données dans la bdd
            
            if ($lastAdId) {
            $uploadedFiles = []; // gestion de l'upload des images pour l'annonce
            $uploadesDir = '/Assets/images';
            $allowedTypes = ['images/jpeg', 'images/png', 'images/gif'];
            $currentTime = date("Y-m-d H:i:s");

            if (!empty($_FILES['images']['name'][0])) { // insère chaque img dans la bdd avec une ref à l'annonce créée
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) { // itère sur chq fichier image téléchargé
                    $fileName = $_FILES['images']['name'][$key];
                    $fileType = $_FILES['images']['type'][$key];
                    $filePath = $uploadesDir . '/' . $fileName; // construit le chemin complet où le fichier sera sauvegardé
                        // vérifie si le type de fichier est autorisé et si le fichier a été déplacé
                    if (in_array($fileType, $allowedTypes) && move_uploaded_file($tmp_name, $filePath)) {
                        $uploadedFiles[] = ['name' => $fileName, 'uploaded_time' => $currentTime]; // si le fichier a été déplacé, l'ajoute a un tableau qui 
                        // enregistre les fichiers téléchargés avec succès
                    }
                }
            }
                // itère sur le tableau des fichiers tlchrgs pour les insérer dans la bdd
            foreach ($uploadedFiles as $File) {
                // prépare la requête pour insérer les infos de l'img dans la table ad_images
                $sql = "INSERT INTO ad_images (ad_id, file_name, uploaded_time) Values (:ad_id, :file_name, :uploaded_time)";
                $stmt = $this->conn->prepare($sql); // prépare la déclaration pour éviter les injections SQL
                // associe les valeurs aux paramètres de la requête 
                $stmt->bindValue(':ad_id', $lastAdId); // ID de l'annonce pour laquelle l'image est téléchargée
                $stmt->bindValue(':file_name', $File['name']); // nom du fichier image
                $stmt->bindValue(':uploaded_time', $File['uploaded_time']); // heure de téléchargmnt du fichier
                $stmt->execute(); 
            }
            
            $adImages = $annonce->getAdImagesInfo($lastAdId);
        }
        }
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') { // vérifie si la requête est ajax
            header('Content-Type: application/json');

            if ($lastAdId) {
                $response = ['success' => true, 'message' => 'L\'annonce a bien été créée'];
            } else {
                $response = ['success' => false,'message' => 'Une erreur est survenue lors de la création de l\'annonce'];
            }
            echo json_encode($response);
            exit;
        }
        require_once 'view/create_annonce.phtml';
    }
    public function detailAction() { // méthode pour afficher les détails d'une annonce spécifique
        if (!isset($_GET['ad_id']) || $_GET['ad_id'] === '') { 
            throw new Exception('ID de l\'annonce non spécifié.');
        } // vérifie si l'id de l'annonce est présent dans la requête GET

            $ad_id = $_GET['ad_id']; // récupère l'id de l'annonce
            $annonce = new Advertisement($this->conn); // crée une nouvelle instance de advertisement avec la co' à la bdd
            $annonceDetails = $annonce->read($ad_id);

var_dump($ad_id); // affiche l'id de l'annonce
var_dump($annonceDetails); // affiche les détails de l'annonce

            if ($this->conn === null) {
                $this->db = new Database();
                $this->conn = $this->db->connect();
            }


            if ($annonceDetails) { // vérif si les détails sont dispos et charge la vue des détails
                $adImages = $annonce->getAdImagesInfo($ad_id);
                require 'View/annonce_details.php';
            } else {
                throw new Exception('Annonce non trouvée'); // lance une exception si l'annonce n'est pas trouvée
            }
    }

    public function editAction() {
        if (!isset($_SESSION['user_id'])) {
            die("Vous devez être connecté pour accéder à cette page.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ad_id'])) { // traitement des requêtes GET pour afficher le formulaire d'édition
var_dump($_GET['ad_id']);
            $ad_id = $_GET['ad_id'];
            $annonce = new Advertisement($this->conn);
            $annonceDetails = $annonce->read($ad_id);

            if ($annonceDetails) {
                require 'View/create_annonce.phtml';
            } else {
                throw new Exception('Annonce non trouvée');
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') { // traitement des requêtes POST pour mettre à jour l'annonce
            $ad_id = filter_input(INPUT_POST, 'ad_id', FILTER_SANITIZE_NUMBER_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT);
            $fuel = filter_input(INPUT_POST, 'fuel', FILTER_SANITIZE_STRING);
            $color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
            $modele = filter_input(INPUT_POST,'modele', FILTER_SANITIZE_STRING);
            $marque = filter_input(INPUT_POST,'marque', FILTER_SANITIZE_STRING);
            $annee_de_fabrication = filter_input(INPUT_POST, 'annee_de_fabrication', FILTER_SANITIZE_STRING);
            $mise_en_circulation = filter_input(INPUT_POST,'mise_en_circulation', FILTER_SANITIZE_STRING);
            $finition = filter_input(INPUT_POST, 'finition', FILTER_SANITIZE_STRING);
            $version = filter_input(INPUT_POST,'version', FILTER_SANITIZE_STRING);
            $kilometrage = filter_input(INPUT_POST, 'kilometrage', FILTER_SANITIZE_STRING);
            $boite_de_vitesse = filter_input(INPUT_POST, 'boite_de_vitesse', FILTER_SANITIZE_STRING);
            $portieres = filter_input(INPUT_POST, 'portieres', FILTER_SANITIZE_STRING);
            $DIN = filter_input(INPUT_POST, 'DIN', FILTER_SANITIZE_STRING);
            $permis = filter_input(INPUT_POST, 'permis', FILTER_SANITIZE_STRING);
            $critair = filter_input(INPUT_POST, 'critair', FILTER_SANITIZE_STRING);

            $annonce = new Advertisement($this->conn); // mise à jour de l'annonce dans la bdd
            $updateData = [
                'ad_id' => $ad_id,
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'fuel' => $fuel,
                'color' => $color,
                'modele' => $modele,
                'marque' => $marque,
                'annee_de_fabrication' => $annee_de_fabrication,
                'mise_en_circulation' => $mise_en_circulation,
                'finition' => $finition,
                'version' => $version,
                'kilometrage' => $kilometrage,
                'boite_de_vitesse' => $boite_de_vitesse,
                'portieres' => $portieres,
                'DIN' => $DIN,
                'permis' => $permis,
                'critair' => $critair,
            ];

            $updated = $annonce->update($updateData);

            if ($updated) {
                header('Location: index.php?controller=Annonce&route=details&ad_id=' . $ad_id);
                exit();
            } else {
                $response = ['success' => false,'message' => 'Une erreur est survenue lors de la modification de l\'annonce'];
            }
        }
    }

    public function deleteAction() {
        if (!isset($_SESSION['user_id'])) {
            die("Vous devez être connecté pour accéder à cette page.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ad_id'])) {
            $ad_id = $_GET['ad_id'];
            $annonce = new Advertisement($this->conn);

            if($annonce->delete($ad_id)) {
                header('Location: index.php?controller=user&action=dashboard');
                exit();
            } throw new Exception('Erreur lors de la suppression de l\'annonce !');
        } else {
            throw new Exception('ID de l\'annonce non spécifié !');
        }
    }
}