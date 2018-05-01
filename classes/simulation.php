<?php
Class Simulation {
  private $id = NULL;
  private $longName = "";
  
  public function getName() {
    return $this->longName;
  }
  
  public function setName($name) {
    $this->longName = $name;
  }
  
  public function getId() {
    return $this->id;
  }
  
  public function setId($id) {
    $this->id = $id;
  }
  
  
}