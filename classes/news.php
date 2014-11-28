<?php
Class News extends DbEntity {
  private $date = NULL;
  private $writtenBy = NULL;
  private $text = NULL;

  public function getDate() {
    return $this->date;
  }

  public function setDate($date) {
    $this->date = $date;
  }

  public function getWrittenBy() {
    return $this->writtenBy;
  }

  public function setWrittenBy($author) {
    $this->writtenBy = $author;
  }

  public function getText() {
    return $this->text;
  }

  public function setText($text) {
    $this->text = $text;
  }
}
