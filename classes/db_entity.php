<?php
Class DbEntity {
  protected $id = NULL;

  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }
}
