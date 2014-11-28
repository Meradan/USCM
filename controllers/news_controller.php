<?php
Class NewsController {
  private $db = NULL;

  function __construct() {
    $this->db = getDatabaseConnection();
  }

  /**
   *
   * @return News[]
   */
  public function getLastYearsNews() {
    $lastyears = date("Y") - 1 . date("-m-d");
    $sql = "SELECT id, date,written_by,text FROM uscm_news WHERE date > '$lastyears' ORDER BY date DESC, id DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $listOfNews = array();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $news = new News();
      $news->setId($row['id']);
      $news->setDate($row['date']);
      $news->setWrittenBy($row['written_by']);
      $news->setText($row['text']);
      $listOfNews[] = $news;
    }
    return $listOfNews;
  }

  /**
   *
   * @param News $news
   */
  public function save($news) {
    $sql = "INSERT INTO uscm_news SET date=:date, written_by=:writtenBy, text=:text";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':date', $news->getDate(), PDO::PARAM_STR);
    $stmt->bindValue(':writtenBy', $news->getWrittenBy(), PDO::PARAM_STR);
    $stmt->bindValue(':text', $news->getText(), PDO::PARAM_STR);
    $stmt->execute();
  }
}
