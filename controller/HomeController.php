<?php

class HomeController {
    private $advertisementModel;

    public function __construct() {
        $db = new Database();
        $this->advertisementModel = new Advertisement($db->connect());
    }

    public function homeAction() {
        $latestAds = $this->getLatestAds();
        require BASE_PATH . '/view/home.phtml';
    }

    private function getLatestAds() {
        return $this->advertisementModel->getLatestAds();
    }
}

