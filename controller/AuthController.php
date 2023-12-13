<?php
require_once("Model/User.php"); // inclusion des fichiers nécessaires pour accéder au modl utilisateur et
require_once("config/Database.php"); // aux paramètres de la bdd

class AuthController { // définition de la classe
    private $userModel; // propriété pour stocker une instance du modl utilisateur
    public function __construct() { // constructeur qui initialise l'instance du mdl utilistr lors de la 
        $this->userModel = new User(); // création de l'authcontrllr
    }

    private function handleError($userFacingMessage, $logMessage = '') { // méthode privée pour gérer les erreurs en enregistrant dans 
        // un fichier et en redirigeant l'utilisateur
        if ($logMessage) {
            error_log($logMessage . "\n", 3, "logs\error.log"); // écris dans le fichier log d'erreur si un msg log est fourni
        }
        $_SESSION['error_message'] = $userFacingMessage; // définit un message d'erreur pour l'affichage utilisateur
        header("Location:../index.php"); // redirige vers la page d'accueil 
        exit;
    }

    public function registerAction() { // méthode publique pour gérer le processus d'inscription
        if ($_SERVER["REQUEST_METHOD"] == "POST") { // vérifie si la requête est de type POST
            
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { // vérifie le token contre les attaques 
                exit("Token invalide !");
            }    
            // validation et nettoyage des données d'inscription
            $data = [
            'company_name' => filter_var($_POST["company_name"], FILTER_SANITIZE_STRING),
            'street' => filter_var($_POST["street"], FILTER_SANITIZE_STRING),
            'firstname' => filter_var($_POST["firstname"], FILTER_SANITIZE_STRING),
            'lastname' => filter_var($_POST["lastname"], FILTER_SANITIZE_STRING),
            'siret_number' => filter_var($_POST["siret_number"], FILTER_SANITIZE_STRING),
            'email' => filter_var($_POST["email"], FILTER_VALIDATE_EMAIL),
            'password' => $_POST["password"],
            ];
            // vérifie si les mdp correspondent
            if ($_POST["password"] !== $_POST["confirm_password"]) {
                exit("Les mots de passe ne correspondent pas !");
            }
// var_dump($data);
            // tente d'enregistrer l'utilisateur avec les données validées
            if ($this->userModel->register($data)) {
                $lastUserId = $this->userModel->getLastInsertedId(); // récupère l'id de l'utilisateur inséré
                    // tente d'ajouter un profil pour l'utilisateur
                if (!$this->userModel->addProfile($lastUserId, $data['firstname'], $data['lastname'], "")) {
                    $this->handleError("Erreur lors de l'ajout du profil $lastUserId");
                } else {
                    header("Location: index.php?controller=auth&action=login"); // redirige vers la page de connexion après inscription
                    exit();
                }
            } else {
                $this->handleError("Erreur lors de l'ajout de l'utilisateur"); // gestion de l'erreur si l'utilisateur ne peut pas être enregistré
            }
        } else {
            require("View/register.phtml"); // affiche la page d'inscription si la méthode n'est pas POST
        }
    }
    public function loginAction() { // méthode publique pour gérer le processus de connexion
        if (!isset($_SESSION['csrf_token'])) { // initialise le token si il n'est pas déjà défini pour la session
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") { // vérif rqut type POST
            $email = $_POST["email"]; // lollecte les données de co'
            $password = $_POST["password"];
var_dump($_POST, $_SESSION);
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { // vérif token
                echo("Token invalide!");
                return;
            }
echo "Email: $email, Password: $password";
            $user = $this->userModel->login($email, $password); // tente de co' avec les identifiants fournis

            if ($user) { // si co' réussie, enregistre l'id de l'utilisateur dans la session
                $_SESSION["user_id"] = $user["user_id"];
                    // crée et stocke les infos de la session utilisateur
                $session_id = bin2hex(random_bytes(32));
                $session_start = date("Y-m-d H:i:s");
                $session_end = date("Y-m-d H:i:s", strtotime("+1 hour"));
                $session_IP = $_SERVER['REMOTE_ADDR'];
                    // ajoute la session utilisateur dans la bdd
                if ($this->userModel->addSession($user["user_id"], $session_id, $session_start, $session_end, $session_IP)) {
                    $_SESSION["session_id"] = $session_id;
                } // redirige vers le dashboard de l'utilisateur
                header("location: index.php?controller=user&action=dashboard");
                exit();
            } else { // affiche une erreur si les identifiants sont incorrects
                echo "Identifiants incorrects !";
            } // régénère le token 
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } // prépare les données pour la vue de connexion
        $data = ['csrf_token' => $_SESSION['csrf_token']];
                // affiche la page de connexion
            require("View/login.phtml");        
    }
    public function logoutAction() { // méthode publique pour gérer la déco' de l'utilisateur
        // supprime la session utilisateur de la bdd
        if ($this->userModel->removeSession($_SESSION["session_id"])) {
            session_destroy(); // détruit la session PHP et redirige vers la page de co'
                        header("location: index.php?controller=auth&action=login");
                        exit();
                    } else { // affiche une erreur si la suppression de la session échoue
                        echo "Erreur à la suppression de la session !";
                    }
                }
}
