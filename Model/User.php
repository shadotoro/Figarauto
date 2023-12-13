<?php
require_once 'config/Database.php';

class User { // la classe user gère toutes les opérations liées à l'utilisateur, inscription, connexion, session ...
    private $conn; // cet attribut va contenir l'objet de co' à la bdd
    private $table = 'users'; // le nom de la table dans la bdd associée à ce modèle

    public function __construct() { // constructeur pour initialiser l'objet User avec une co' à la bdd
        $database = new Database(); // créa d'un nouvel objet database
        $this->conn = $database->connect(); // établissement d'une co'à la bdd
    }

    public function register($data) { // méthode pour enregistrer un nouvel utilisateur avec les données fournies
        // requête SQL pour insérer un nouvel enregistrement dans la table user
        $query = "INSERT INTO " . $this->table . " (company_name, street, email, hashed_password, siret_number) VALUES (:company_name, :street, :email, :hashed_password, :siret_number)";
        $stmt = $this->conn->prepare($query); // préparation de la requête pour l'exécution
            // définition des clés autorisées à être utilisées dans l'instruction SQL
        $allowed_keys = ['company_name', 'street', 'email', 'hashed_password', 'siret_number'];
            // pour chaque donnée on la lie à la requête si elle fait partie des clés autorisées
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $hashed_password = password_hash($value, PASSWORD_DEFAULT);
                $stmt->bindParam(':hashed_password', $hashed_password, PDO::PARAM_STR);
            } elseif (in_array($key, $allowed_keys)) {
                $stmt->bindValue(':'. $key, $value, PDO::PARAM_STR);
            }
        }
        if ($stmt->execute()) { // exécution de la requête
            return true;
        } else {
            echo "Erreur lors de l'enregistrement de l'utilisateur";
            print_r($stmt->errorInfo());
            return false;
        }
    }

    public function addProfile($user_id, $firstname, $lastname, $profile_picture) { // ajout d'un profil à utilisateur après l'inscription
        // requête pour inérer un nouvel enregistrement dans la table de profil utilisateur
        $query = "INSERT INTO userprofile (user_id, firstname, lastname, profile_picture) VALUES (:user_id, :firstname, :lastname, :profile_picture)";
        $stmt = $this->conn->prepare($query);
            // liaison des données de profil à la requête
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':profile_picture', $profile_picture);

        if ($stmt->execute()) { 
            return true;
        }
        return false;
    }

    public function addSession($userId, $sessionId, $start, $end, $ip) { // ajout d'une session pour utilisateur lors de la co'
        // requête pour insérer un nouvel enregistrement dans la table de session
        $sql = "INSERT INTO sessions (session_id, user_id, session_start, session_end, session_IP) values (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$sessionId, $userId, $start, $end, $ip]);
    }

    public function getLastInsertedId() { // méthode pour obtenir l'id du dernier enregistrement inséré
        return $this->conn->lastInsertId();
    }

    public function login($email, $password) { // méthode pour connecter un utilisateur
$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // requête pour le sélectionner en fonction de l'email
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':email', $email, PDO::PARAM_STR); // liaison de l'email à la requête
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Query: $query, Email: $email";
print_r($user);

        if ($user && password_verify($password, $user['hashed_password'])) { // vérif du mdp et retour de l'utilisateur si valide
            return $user;
        } else {
            echo "la connexion a échoué !";
            return false;
        }
    }

    public function removeSession($sessionId) { // méthode pour obtenir les données d'un utilisateur par son ID
        // requête pour sélectionner un utilisateur en fonction de son id
        $sql = "DELETE FROM sessions WHERE session_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$sessionId]);
    }

    public function getUserById($user_id) {
        $query = "SELECT * FROM ". $this->table. " WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}