<?php
/**
 * classe Database pour la gestion de la connexion à une base de données.
 */
class Database {
    /**
     * @var string Hôte de la base de données.
     */
    private $host = 'localhost';
    /**
     * @var string Nom de la base de données.
     */
    private $db_name = 'figarauto';
    /**
     * @var string Nom d'utilisateur pour la connexion à la base de données.
     */
    private $username = 'root';
    /**
     * @var string Mot de passe pour la connexion à la base de données.
     */
    private $password = '';
    /**
     * @var PDO|null Instance de connexion à la base de données.
     */
    public $conn;
    /**
     * établit une connexion à la base de données et la retourne.
     * 
     * @return PDO|null retourne l'objet PDO en cas de succès ou null en cas d'échec.
     */

    public function connect() {
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