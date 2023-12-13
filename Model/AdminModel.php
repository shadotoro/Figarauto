<?php
class AdminModel {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function createAdminUser($email, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (email, hashed_password, is_admin) VALUES (:email, :hashed_password, 1)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    public function getAllAds() {
        try {
            $query = "SELECT * FROM advertisements";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $ads = $stmt->fetchALL(PDO::FETCH_ASSOC);
            return $ads;
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT * FROM users";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchALL(PDO::FETCH_ASSOC);
            return $users;
        } catch (PDOException $e) {
            $this->logError($e);
            return false;
        }
    }

    public function logError($exception) {
        $logFile = __DIR__ . '/../logs/error.log';
        $errorMessage = '[' . date('Y-m-d H:i:s') . '] ' . $exception->getMessage() . PHP_EOL;
        file_put_contents($logFile, $errorMessage, FILE_APPEND);
    }
}