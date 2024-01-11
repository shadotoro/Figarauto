<?php

class Advertisement
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function create($data) {
        $sql = "INSERT INTO advertisements (`user_id`, `title`, `description`, `price`, `fuel`, `color`, `modele`, `marque`, `annee_de_fabrication`, `mise_en_circulation`, `finition`, `version`, `kilometrage`, `boite_de_vitesse`, `portieres`, `DIN`, `permis`, `critair`) 
                VALUES (:user_id, :title, :description, :price, :fuel, :color, :modele, :marque, :annee_de_fabrication, :mise_en_circulation, :finition, :version, :kilometrage, :boite_de_vitesse, :portieres, :DIN, :permis, :critair)";
    
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':user_id', $data['user_id']);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':description', $data['description']);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':fuel', $data['fuel']);
        $stmt->bindValue(':color', $data['color']);
        $stmt->bindValue(':modele', $data['modele']);
        $stmt->bindValue(':marque', $data['marque']);
        $stmt->bindValue(':annee_de_fabrication', $data['annee_de_fabrication']);
        $stmt->bindValue(':mise_en_circulation', $data['mise_en_circulation']);
        $stmt->bindValue(':finition', $data['finition']);
        $stmt->bindValue(':version', $data['version']);
        $stmt->bindValue(':kilometrage', $data['kilometrage']);
        $stmt->bindValue(':boite_de_vitesse', $data['boite_de_vitesse']);
        $stmt->bindValue(':portieres', $data['portieres']);
        $stmt->bindValue(':DIN', $data['DIN']);
        $stmt->bindValue(':permis', $data['permis']);
        $stmt->bindValue(':critair', $data['critair']);
    
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
    
        return false;
    }    

    public function read($id) {
        $stmt = $this->conn->prepare("SELECT * FROM advertisements WHERE ad_id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $sql = "UPDATE advertisements SET title = :title, description = :description, price = :price, color = :color, modele = :modele, marque = :marque, annee_de_fabrication = :annee_de_fabrication, mise_en_circulation = :mise_en_circulation, finition = :finition, version = :version, kilometrage = :kilometrage, boite_de_vitesse = :boite_de_vitesse, portieres = :portieres, DIN = :DIN, permis = :permis, critair = :critair WHERE ad_id = :ad_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':ad_id', $data['ad_id']);
        $stmt->bindValue(':title', $data['title']);
        $stmt->bindValue(':description', !empty($data['description']) ? $data['description'] : null);
        $stmt->bindValue(':price', $data['price']);
        $stmt->bindValue(':color', !empty($data['color']) ? $data['color'] : null);
        $stmt->bindValue(':modele', !empty($data['modele']) ? $data['modele'] : null);
        $stmt->bindValue('marque', !empty($data['marque']) ? $data['marque'] : null);
        $stmt->bindValue('annee_de_fabrication', !empty($data['annee_de_fabrication']) ? $data['annee_de_fabrication'] : null);
        $stmt->bindValue('mise_en_circulation', !empty($data['mise_en_circulation']) ? $data['mise_en_circulation'] : null);
        $stmt->bindValue('finition', !empty($data['finition']) ? $data['finition'] : null);
        $stmt->bindValue('version', !empty($data['version']) ? $data['version'] : null);
        $stmt->bindValue('kilometrage', !empty($data['kilometrage']) ? $data['kilometrage'] : null);
        $stmt->bindValue('boite_de_vitesse', !empty($data['boite_de_vitesse']) ? $data['boite_de_vitesse'] : null);
        $stmt->bindValue('portieres', !empty($data['portieres']) ? $data['portieres'] : null);
        $stmt->bindValue('DIN', !empty($data['DIN']) ? $data['DIN'] : null);
        $stmt->bindValue('permis', !empty($data['permis']) ? $data['permis'] : null);
        $stmt->bindValue('critair', !empty($data['critair']) ? $data['critair'] : null);

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM advertisements WHERE ad_id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function getAdsWithImagesByUserId($userId) {
        $sql = "SELECT a.*, ai.file_name AS image_file_name, ai.uploaded_time AS image_uploaded_time
                FROM advertisements a 
                LEFT JOIN ad_images ai ON a.ad_id = ai.ad_id 
                WHERE a.user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestAds() {
        $sql = "SELECT * FROM advertisements ORDER BY user_id DESC LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addImageToAd($adId, $fileName, $uploadeTime) {
        $sql = "INSERT INTO ad_images (ad_id, file_name, uploaded_time) VALUES (:ad_id, :file_name, :uploaded_time)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ad_id', $adId);
        $stmt->bindValue(':file_name', $fileName);
        $stmt->bindValue(':uploaded_time', $uploadeTime);
        return $stmt->execute();
    }

    public function getAdImagesInfo($adId) {
        $sql = "SELECT * FROM ad_images WHERE ad_id = :ad_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ad_id', $adId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
