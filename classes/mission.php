<?php
Class Mission {
  private $id = NULL;
  private $longName = "";
  private $shortName = "";
  private $platoonShortName = "";
  private $gmId = NULL;
  private $date = "";
  private $briefing = "";
  private $debriefing = "";
  private $platoonId = NULL;

  public function getName() {
    return $this->longName;
  }

  public function setName($name) {
    $this->longName = $name;
  }

  public function getShortName() {
    return $this->shortName;
  }

  public function setShortName($name) {
    $this->shortName = $name;
  }

  public function getId() {
    return $this->id;
  }
  public function setId($id) {
    $this->id = $id;
  }

  public function getPlatoonShortName() {
    return $this->platoonShortName;
  }

  public function setPlatoonShortName($name) {
    $this->platoonShortName = $name;
  }

  public function getGmId() {
    return $this->gmId;
  }

  public function setGmId($id) {
    $this->gmId = $id;
  }

  public function getDate() {
    return $this->date;
  }

  public function setDate($date) {
    $this->date = $date;
  }

  public function getBriefing() {
    return $this->briefing;
  }

  public function setBriefing($briefing) {
    $this->briefing = $briefing;
  }

  public function getDebriefing() {
    return $this->debriefing;
  }

  public function setDebriefing($debriefing) {
    $this->debriefing = $debriefing;
  }

  public function getPlatoonId() {
    return $this->platoonId;
  }

  public function setPlatoonId($id) {
    $this->platoonId = $id;
  }

}
