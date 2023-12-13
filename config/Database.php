<?php
//définition de la classe Database
class Database {
    //propriétés privées pour les détails de connexion à la bdd
    private $host = 'localhost'; // hôte de la bdd
    private $db_name = 'figarauto'; // nom de la bdd
    private $username = 'root'; // nom d'utilisateur pour s'y connecter
    private $password = ''; // mdp pour s'y connecter
    public $conn; // variable publique pour la co'
// méthode publique pour établir une co' à la bdd
    public function connect() { // initialisation de la co' à null
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8"; //construction du DSN (data source name)
            $this->conn = new PDO($dsn, $this->username, $this->password); //création d'une nouvelle instance PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // config des attributs de PDO pour gérer les erreurs
    } catch (PDOException $exception) { // capture des exceptions si la co' échoue
        echo "Problème avec la connexion à la base de données :" . $exception->getMessage();
    }
    return $this->conn; // retourne l'objet de connexion
    }
}