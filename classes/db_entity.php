<?php
Class DbEntity {
  protected $id = NULL;

  /**
   * @return int Id of database entry
   */
  public function getId() {
    return $this->id;
  }

  public function setId($id) {
    $this->id = $id;
  }
}
