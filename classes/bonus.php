<?php

class Bonus {
  private $db = NULL;
  private $characterId;

  function __construct($characterId) {
    $this->characterId = $characterId;
    $this->db = getDatabaseConnection();
  }

  public function attributeBonus($modifiertype, $attribute) {
    $bonus = Array ('always' => 0,'sometimes' => Array ()
    );
    $advsql = "SELECT $modifiertype, value_always_active
      FROM uscm_advdisadv_bonus advdis
      INNER JOIN uscm_advantages a ON a.advantage_name_id = advdis.advid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = :cid
         AND $modifiertype is not NULL";
    // print_r($advsql);
    $stmt = $this->db->prepare($advsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ["$modifiertype"];
      } else {
        $bonus ['sometimes'] [] = $row ["$modifiertype"];
      }
    }
    $disadvsql = "SELECT $modifiertype, value_always_active
      FROM uscm_advdisadv_bonus advdis
      INNER JOIN uscm_disadvantages a ON a.disadvantage_name_id = advdis.disadvid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = :cid
         AND $modifiertype is not NULL";
    // print_r($disadvsql);
    $stmt = $this->db->prepare($disadvsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ["$modifiertype"];
      } else {
        $bonus ['sometimes'] [] = $row ["$modifiertype"];
      }
    }
    $traitsql = "SELECT $modifiertype, value_always_active
      FROM uscm_advdisadv_bonus advdis
      INNER JOIN uscm_traits a ON a.trait_name_id = advdis.traitid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = :cid
         AND $modifiertype is not NULL";
    // print_r($traitsql);
    $stmt = $this->db->prepare($traitsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ["$modifiertype"];
      } else {
        $bonus ['sometimes'] [] = $row ["$modifiertype"];
      }
    }
    return $bonus;
  }

  function pointAndLimitBonus($bonustype) {
    $bonus = Array ('always' => 0,'sometimes' => Array ()
    );
    $advsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_advantages a ON a.advantage_name_id = advdis.advid
            WHERE table_point_name = '$bonustype' AND a.character_id = :cid";
    // print_r($advsql);
    $stmt = $this->db->prepare($advsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    $disadvsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_disadvantages a ON a.disadvantage_name_id = advdis.disadvid
            WHERE table_point_name = '$bonustype' AND a.character_id = :cid";
    // print_r($disadvsql);
    $stmt = $this->db->prepare($disadvsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    $traitsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_traits a ON a.trait_name_id = advdis.traitid
            WHERE table_point_name = '$bonustype' AND a.character_id = :cid";
    // print_r($traitsql);
    $stmt = $this->db->prepare($traitsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    return $bonus;
  }

  function carryCapacity() {
    return $this->bonus('carrycapacity');
  }

  function combatLoad() {
    return $this->bonus('combatlod');
  }

  function psychoPoints() {
    return $this->bonus('psychopoints');
  }
  function psychoLimit() {
    return $this->bonus('psycholimit');
  }
  function fearPoints() {
    return $this->bonus('fearpoints');
  }
  function fearLimit() {
    return $this->bonus('fearlimit');
  }
  function exhaustionPoints() {
    return $this->bonus('exhaustionpoints');
  }
  function exhaustionLimit() {
    return $this->bonus('exhaustionlimit');
  }

  private function bonus($bonusType) {
    $bonus = Array ('always' => 0,'sometimes' => Array ()
    );
    $advsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_advantages a ON a.advantage_name_id = advdis.advid
            WHERE table_point_name = :bonusType AND a.character_id = :cid";
    // print_r($advsql);
    $stmt = $this->db->prepare($advsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->bindValue(':bonusType', $this->characterId, PDO::PARAM_STR);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    $disadvsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_disadvantages a ON a.disadvantage_name_id = advdis.disadvid
            WHERE table_point_name = :bonusType AND a.character_id = :cid";
    // print_r($disadvsql);
    $stmt = $this->db->prepare($disadvsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->bindValue(':bonusType', $this->characterId, PDO::PARAM_STR);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    $traitsql = "SELECT modifier_basic_value, value_always_active
            FROM uscm_advdisadv_bonus advdis
            INNER JOIN uscm_traits a ON a.trait_name_id = advdis.traitid
            WHERE table_point_name = :bonusType AND a.character_id = :cid";
    // print_r($traitsql);
    $stmt = $this->db->prepare($traitsql);
    $stmt->bindValue(':cid', $this->characterId, PDO::PARAM_INT);
    $stmt->bindValue(':bonusType', $this->characterId, PDO::PARAM_STR);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row ['value_always_active'] == 1) {
        $bonus ['always'] = $bonus ['always'] + $row ['modifier_basic_value'];
      } else {
        $bonus ['sometimes'] [] = $row ['modifier_basic_value'];
      }
    }
    return $bonus;
  }
}
