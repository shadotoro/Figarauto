<?php
class AdminController {
    private $authController;
    private $adminModel;
    public function __construct($authController, $adminModel) {
        $this->authController = $authController;
        $this->adminModel = $adminModel;
    }

    public function manageAdsAction() {
        $this->authController->checkAdmin();
        $ads = $this->adminModel->getAllAds();
        include 'View/manage_ads.phtml';
    }

    public function manageUsersAction() {
        $this->authController->checkAdmin();
        $users = $this->adminModel->getAllUsers();
        include 'View/manage_users.phtml';
    }
}