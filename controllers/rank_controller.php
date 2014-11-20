<?php
Class RankController extends DbController {
  public function getRanks() {
    $ranks = array ();
    $sql = "SELECT id, rank_long, rank_short, rank_desc
                FROM uscm_rank_names";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $rank = new Rank();
      $rank->setId($row['id']);
      $rank->setName($row['rank_long']);
      $rank->setShortName($row['rank_short']);
      $rank->setDescription($row['rank_desc']);
      $ranks[] = $rank;
    }
    return $ranks;
  }

  public function getRank($rankId) {
    $ranks = array ();
    $sql = "SELECT id, rank_long, rank_short, rank_desc, count(*) as howmany
                FROM uscm_rank_names " .
                " WHERE id = :rankId";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':rankId', $rankId, PDO::PARAM_INT);
    $stmt->execute();
    $rank = new Rank();
    $row = $stmt->fetch();
    if ($row['howmany'] == 1) {
      $rank->setId($row['id']);
      $rank->setName($row['rank_long']);
      $rank->setShortName($row['rank_short']);
      $rank->setDescription($row['rank_desc']);
    }
    return $rank;
  }

  /**
   *
   * @param Character $character
   * @return Rank[]
   */
  public function getPromotableRanksForCharacter($character) {
//     $charactersql = "SELECT rank_id FROM {$_SESSION['table_prefix']}ranks WHERE character_id='{$_POST['character']}'";
//     $ranksql = "SELECT id,rank_long FROM {$_SESSION['table_prefix']}rank_names WHERE id>=('{$characterrank['rank_id']}'+1) OR id=('{$characterrank['rank_id']}'-1)";
//     $demotionRank = $this->getRank($character->getRankId()-1);
//     $promotionRank = $this->getRank($character->getRankId()+1);
//     return array($demotionRank, $promotionRank);
    return $this->getRanks();
  }
}
