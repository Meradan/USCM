<?php
Class DbController {
  protected $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }
}
