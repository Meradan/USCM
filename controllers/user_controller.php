<?php
Class UserController {
  private $db;
  private $playerController;

  function __construct() {
    $this->db = getDatabaseConnection();
    $this->playerController = new PlayerController();
  }

  public function getCurrentUser() {
    if (array_key_exists('user_id', $_SESSION)) {
      return $this->playerController->getPlayer($_SESSION['user_id']);
    } else {
      return new Player();
    }
  }
}
