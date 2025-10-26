<?php

require_once('../classes/db/UserService.php');
require_once('../classes/db/RentalService.php');
require_once('../classes/Renderer.php');
require_once('../classes/Utils.php');

abstract class BasicPage {
    private $loginInfo;
    private $isAdmin;
    private $isAdminPanel;

    public function __construct($isAdminPanel = false){
        $this->isAdminPanel = $isAdminPanel;
        $this->refreshStatus();
    }

    public function refreshStatus() {
        $this->loginInfo = Utils::getLoggedIn();
        $this->isAdmin = User::isUserAdmin($this->loginInfo);
        Renderer::inject('loginInfo', $this->loginInfo);
        Renderer::inject('isAdmin', $this->isAdmin);
        Renderer::inject('isAdminPanel', $this->isAdminPanel);
    }

    public function setTitle($title) {
        Renderer::inject('title', $title);
    }

    public function getLoginInfo() {
        return $this->loginInfo;
    }

    public function isAdmin(){
        return $this->isAdmin;
    }

    public function isAdminPanel(){
        return $this->isAdminPanel;
    }

    public abstract function render();
}