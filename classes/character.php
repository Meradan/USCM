<?php

class Character extends DbEntity {
  private $db = NULL;
  private $characterId = NULL;
  private $userId = NULL;
  private $user = NULL;
  private $givenName = NULL;
  private $surname = NULL;
  private $platoonId = NULL;
  private $platoon = NULL;
  private $enlisted = NULL;
  private $age = NULL;
  private $gender = NULL;
  private $unusedXp = NULL;
  private $awarenessPoints = NULL;
  private $coolPoints = NULL;
  private $exhaustionPoints = NULL;
  private $fearPoints = NULL;
  private $leadershipPoints = NULL;
  private $psychoPoints = NULL;
  private $traumaPoints = NULL;
  private $mentalPoints = NULL;
  private $status = NULL;
  private $statusDesc = NULL;
  private $rankLong = NULL;
  private $rankShort = NULL;
  private $rankId = NULL;
  private $rankDesc = NULL;
  private $specialtyName = NULL;
  private $specialtyId = NULL;
  private $certificates = NULL;
  private $medals = NULL;
  private $advantagesVisible = NULL;
  private $advantagesAll = NULL;
  private $advantageIds = NULL;
  private $disadvantagesVisible = NULL;
  private $disadvantagesAll = NULL;
  private $disadvantageIds = NULL;
  private $encounteralien = NULL;
  private $encountergrey = NULL;
  private $encounterpredator = NULL;
  private $encounterai = NULL;
  private $encounterarachnid = NULL;

  function __construct($characterId = NULL) {
    $this->id = $characterId;
    $this->db = getDatabaseConnection();
  }

  public function getGivenName() {
    return $this->givenName;
  }

  public function setGivenName($name) {
    $this->givenName = $name;
  }

  public function getSurname() {
    return $this->surname;
  }

  public function setSurname($name) {
    $this->surname = $name;
  }

  public function getName() {
    return $this->givenName . " " . $this->surname;
  }

  public function getPlayerId() {
    return $this->userId;
  }

  public function setPlayerId($id) {
    $this->userId = $id;
  }

  public function setPlayer($playerProvider) {
    $this->user = new LazyLoader($playerProvider);
  }

  public function getPlatoonId() {
    return $this->platoonId;
  }

  public function setPlatoonId($id) {
    $this->platoonId = $id;
  }

  public function setPlatoon($platoonProvider) {
    $this->platoon = new LazyLoader($platoonProvider);
  }

  public function getEnlistedDate() {
    return $this->enlisted;
  }

  public function setEnlistedDate($date) {
    $this->enlisted = $date;
  }

  public function getAge() {
    return $this->age;
  }

  public function setAge($age) {
    $this->age = $age;
  }

  public function getGender() {
    return $this->gender;
  }

  public function setGender($gender) {
    $this->gender = $gender;
  }

  public function getUnusedXp() {
    return $this->unusedXp;
  }

  public function setUnusedXp($xp) {
    $this->unusedXp = $xp;
  }

  public function getAwarenessPoints() {
    return $this->awarenessPoints;
  }

  public function setAwarenessPoints($points) {
    $this->awarenessPoints = $points;
  }

  public function getCoolPoints() {
    return $this->coolPoints;
  }

  public function setCoolPoints($points) {
    $this->coolPoints = $points;
  }

  public function getExhaustionPoints() {
    return $this->exhaustionPoints;
  }

  public function setExhaustionPoints($points) {
    $this->exhaustionPoints = $points;
  }

  public function getFearPoints() {
    return $this->fearPoints;
  }

  public function setFearPoints($points) {
    $this->fearPoints = $points;
  }

  public function getPsychoLimit() {
    return $this->getAttribute('Psyche');
  }

  public function getFearLimit() {
    return $this->getAttribute('Psyche') * 2;
  }

  public function getExhaustionLimit() {
    return $this->getAttribute('Endurance') * 2;
  }

  private function getAttribute($type) {
    $sql = "SELECT value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name=:type AND character_id=:cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':type', $type, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['value'];
  }

  public function getLeadershipPoints() {
    return $this->leadershipPoints;
  }

  public function setLeadershipPoints($points) {
    $this->leadershipPoints = $points;
  }

  public function getPsychoPoints() {
    return $this->psychoPoints;
  }

  public function setPsychoPoints($points) {
    $this->psychoPoints = $points;
  }

  public function getTraumaPoints() {
    return $this->traumaPoints;
  }

  public function setTraumaPoints($points) {
    $this->traumaPoints = $points;
  }

  public function getMentalPoints() {
    return $this->mentalPoints;
  }

  public function setMentalPoints($points) {
    $this->mentalPoints = $points;
  }

  public function getStatus() {
    return $this->status;
  }

  public function setStatus($status){
    $this->status = $status;
  }

  public function getStatusDescription() {
    return $this->statusDesc;
  }

  public function setStatusDescription($description) {
    $this->statusDesc = $description;
  }

  public function getRankShort() {
    return $this->rankShort;
  }

  public function setRankShort($rank) {
    $this->rankShort = $rank;
  }

  public function getRankLong() {
    return $this->rankLong;
  }

  public function setRankLong($rank) {
    $this->rankLong = $rank;
  }

  public function getRankDescription() {
    return $this->rankDesc;
  }

  public function setRankDescription($rankdesc) {
    $this->rankDesc = $rankdesc;
  }

  public function getRankId() {
    return $this->rankId;
  }

  public function setRankId($rankId) {
    $this->rankId = $rankId;
  }

  public function getSpecialtyName() {
    return $this->specialtyName;
  }

  public function setSpecialtyName($name) {
    $this->specialtyName = $name;
  }

  public function getSpecialtyId() {
    return $this->specialtyId;
  }

  public function setSpecialtyId($id) {
    $this->specialtyId = $id;
  }

  public function getEncounterAlien() {
	  return $this->encounteralien;
  }

  public function setEncounterAlien($val) {
	  $this->encounteralien = $val;
  }

  public function getEncounterGrey() {
	  return $this->encountergrey;
  }

  public function setEncounterGrey($val) {
	  $this->encountergrey = $val;
  }

  public function getEncounterPredator() {
	  return $this->encounterpredator;
  }

  public function setEncounterPredator($val) {
	  $this->encounterpredator = $val;
  }

  public function getEncounterAI() {
	  return $this->encounterai;
  }

  public function setEncounterAI($val) {
	  $this->encounterai = $val;
  }

  public function getEncounterArachnid() {
	  return $this->encounterarachnid;
  }

  public function setEncounterArachnid($val) {
	  $this->encounterarachnid = $val;
  }

  /**
   * @return Advantage[]
   */
  public function getAdvantagesVisible() {
    return call_user_func($this->advantagesVisible);
  }

  public function setAdvantagesVisible($advantageProvider) {
    $this->advantagesVisible = new LazyLoader($advantageProvider);
  }

    /**
   * @return Advantage[]
   */
  public function getAdvantagesAll() {
    return call_user_func($this->advantagesAll);
  }

  public function setAdvantagesAll($advantageProvider) {
    $this->advantagesAll = new LazyLoader($advantageProvider);
  }

  /**
   * $return Disdvantage[]
   */
  public function getDisadvantagesVisible() {
    return call_user_func($this->disadvantagesVisible);
  }

  public function setDisadvantagesVisible($disadvantageProvider) {
    $this->disadvantagesVisible = new LazyLoader($disadvantageProvider);
  }

    /**
   * $return Disadvantage[]
   */
  public function getDisadvantagesAll() {
    return call_user_func($this->disadvantagesAll);
  }

  public function setDisadvantagesAll($disadvantageProvider) {
    $this->disadvantagesAll = new LazyLoader($disadvantageProvider);
  }

  //TODO:remove
  function getDisadvantages($onlyvisible = false) {
    $disadvarray = array ();
    $sql = "SELECT dn.id, disadvantage_name, d.id as uid
          FROM uscm_disadvantage_names dn
          LEFT JOIN uscm_disadvantages d ON d.disadvantage_name_id=dn.id
          LEFT JOIN uscm_characters c ON c.id=d.character_id
          WHERE d.character_id=:cid ORDER BY disadvantage_name";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $disadvarray[$row['id']]['disadvantage_name'] = $row['disadvantage_name'];
      $disadvarray[$row['id']]['uid'] = $row['uid'];
    }
    return $disadvarray;
  }

  public function getAwareness() {
    $sql = "SELECT (value * 2)  as value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          LEFT JOIN uscm_characters c ON c.id=a.character_id
          WHERE an.attribute_name='Perception' AND a.character_id=:cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['value'];
  }

  function getCarryCapacity() {
    $sql = "SELECT 40 + (value * 5) as value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='strength' AND character_id=:cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $modifier = 0;
    if ($this->hasCharacterAdvantage(/*Mule*/62)) {
      $modifier = $modifier + 5;
    }
    if ($this->hasCharacterAdvantage(/*Efficient Packing*/59)) {
      $modifier = $modifier + 5;
    }
    if ($this->hasCharacterDisadvantage(/*Hunchback*/16)) {
      $modifier = $modifier - 5;
    }

    return ($row['value'] + $modifier);
  }

  function getCombatLoad() {
    $sql = "SELECT 15 + (value * 5) as mvalue, value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='strength' AND character_id=:cid";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $modifier = 0;
    if ($this->hasCharacterAdvantage(/*Mule*/62)) {
      $modifier = $modifier + 1;
    }

    if ($row['value'] >= 3) {
      return ($row['mvalue'] - 5 + $modifier);
    } else {
      return ($row['mvalue'] + $modifier);
    }
  }

  public function getLeadership() {
    $sql = "SELECT value  + IF(r.rank_id>2, r.rank_id - 2, 0) as value FROM uscm_attributes a
          LEFT JOIN uscm_attribute_names an ON an.id=a.attribute_id
          LEFT JOIN uscm_characters c ON c.id=a.character_id
          LEFT JOIN uscm_ranks r ON r.character_id=c.id
          WHERE an.attribute_name='Charisma' AND a.character_id=:cid";
	//Need fix for Msgt + advantages
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['value'];
  }

  public function getPlayer() {
    return call_user_func($this->user);
  }

  public function getPlatoon() {
    return call_user_func($this->platoon);
  }

  public function getMissionsShort() {
    return $this->missions("short");
  }

  public function getMissionsLong() {
    return $this->missions("long");
  }

  private function missions($length) {
    $missionarray = array ();
    $sql = "SELECT mn.id,p.name_short,mission_name_short,mission_name,rank_short,medal_short
            FROM uscm_mission_names mn
            LEFT JOIN uscm_missions m ON m.mission_id=mn.id
            LEFT JOIN uscm_characters c ON c.id=m.character_id
            LEFT JOIN uscm_rank_names rn ON rn.id=m.rank_id
            LEFT JOIN uscm_medal_names men ON men.id=m.medal_id
            LEFT JOIN uscm_platoon_names p ON p.id=mn.platoon_id
            WHERE character_id=:cid AND mn.date < NOW() ORDER BY date";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $missionarray[$row['id']]['text'] = "";
      if ($length == "short") {
		$missionarray[$row['id']]['mission_name'] = $row['mission_name_short'];
        if ($row['rank_short']) {
          $missionarray[$row['id']]['text'] = "Prom. " . $row['rank_short'];
        } elseif ($row['medal_short']) {
          $missionarray[$row['id']]['text'] = "Awarded " . $row['medal_short'];
        }
      } elseif ($length == "long") {
		$missionarray[$row['id']]['mission_name'] = $row['name_short'] . ' ' . $row['mission_name_short'];
		//$missionarray[$row['id']]['mission_name_long'] = $row['mission_name'];
        if ($row['rank_short']) {
          $missionarray[$row['id']]['text'] = "Prom. " . $row['rank_short'];
        }
        if ($row['medal_short']) {
          $missionarray[$row['id']]['text'] = $missionarray[$row['id']]['text'] . " Awarded " . $row['medal_short'];
        }
      }
    }
    return $missionarray;
  }

  public function setMedals($medalsProvider) {
    $this->medals = new LazyLoader($medalsProvider);
  }

  /**
   * @return Medal[]
   */
  public function getMedals() {
    return call_user_func($this->medals);

    $medalarray = array ();
    $sql = "SELECT m.id, medal_short, medal_glory
          FROM uscm_medal_names mn
          LEFT JOIN uscm_missions m ON m.medal_id=mn.id
          WHERE m.character_id=:cid ORDER BY medal_glory DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $medalarray[$row['id']]['medal'] = $row['medal_short'] . " (" . $row['medal_glory'] . ")";
    }
    return $medalarray;
  }

  /**
   * @return int
   */
  public function getGlory() {
	  $sql = "select coalesce(sum(medal_glory),0) as glory from uscm_missions join uscm_medal_names on medal_id=uscm_medal_names.id where character_id=:cid";
	$stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row['glory'];
  }

  public function getWeaponSkills() {
    return $this->skills("Weapons", $this->getCertificates());
  }

  public function getPhysicalSkills() {
    return $this->skills("Physical", $this->getCertificates());
  }

  public function getVehiclesSkills() {
    return $this->skills("Vehicles", $this->getCertificates());
  }

  public function getOtherSkills() {
    return $this->skills("Other", $this->getCertificates());
  }

  public function getLanguagesSkills() {
    return $this->skills("Languages", $this->getCertificates());
  }

  private function skills($skilltype, $certarray) {
    $sql = "SELECT skill_name_id, value, skill_name FROM uscm_skills s
          LEFT JOIN uscm_skill_names sn ON sn.id=s.skill_name_id
          LEFT JOIN uscm_skill_groups sg ON sg.id=sn.skill_group_id
          WHERE character_id=:cid AND skill_group_name=:skilltype
          ORDER BY skill_name";
    $certallarray = $this->allCertificateRequirements();
    $skillarray = array ();
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':skilltype', $skilltype, PDO::PARAM_STR);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $skillarray[$row['skill_name_id']]['value'] = $row['value'];
      $skillarray[$row['skill_name_id']]['name'] = $row['skill_name'];
      $skillbonusarray = $this->skillbonus($row['skill_name_id'], $certarray, $certallarray);
      $skillarray[$row['skill_name_id']]['bonus_always'] = $skillbonusarray['always'];
      $skillarray[$row['skill_name_id']]['bonus_sometimes'] = $skillbonusarray['sometimes'];
    }
    return $skillarray;
  }

  private function allCertificateRequirements() {
    $certificates = array ();
    $sql = "SELECT cn.id as cid, cn.mainskill as sid FROM uscm_certificate_names cn";
    $stmt = $this->db->query($sql);
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $certificates[$row['cid']]['sid'] = $row['sid'];
    }
    return $certificates;
  }

  private function skillbonus($skillid, $certarray, $certallarray) {
    $skillbonus = Array ('always' => 0,'sometimes' => Array ()
    );
    // Check certificate bonus
    foreach ( $certarray as $key => $value ) {
      // print_r($key);
      // print_r($certallarray[$key]);
       //print_r($skillid);
      if ($certallarray[$key]['sid'] == $skillid) {
        $skillbonus['always'] = $skillbonus['always'] + 1;
      }
      ;
    }
    // if (certarray[
    // print_r($skillbonus);
     //print_r($certarray);
     //print_r($certallarray);
     //exit;

    // Check adv/disadv/trait bonus
    // $sql = "SELECT skill_name_id, value, skill_name FROM {$_SESSION['table_prefix']}skills s
    // LEFT JOIN {$_SESSION['table_prefix']}skill_names sn ON sn.id=s.skill_name_id
    // LEFT JOIN {$_SESSION['table_prefix']}skill_groups sg ON sg.id=sn.skill_group_id
    // WHERE character_id='{$cid}' AND skill_group_name='{$skilltype}'
    // ORDER BY skill_name";
    $advsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
               FROM uscm_advdisadv_bonus advdis
               INNER JOIN uscm_skill_names sn ON sn.id = advdis.column_id AND table_point_name = 'skill_names'
               INNER JOIN uscm_advantages a ON a.advantage_name_id = advdis.advid
               WHERE column_id = :skillid AND a.character_id = :cid";
    // print_r($advsql);
    $stmt = $this->db->prepare($advsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':skillid', $skillid, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row['value_always_active'] == 1) {
        $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
        $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
    }
    $disadvsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
                     FROM uscm_advdisadv_bonus advdis
                     INNER JOIN uscm_skill_names sn ON sn.id = advdis.column_id AND table_point_name = 'skill_names'
                     INNER JOIN uscm_disadvantages a ON a.disadvantage_name_id = advdis.disadvid
                     WHERE column_id = :skillid AND a.character_id = :cid";
    // print_r($disadvsql);
    $stmt = $this->db->prepare($disadvsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':skillid', $skillid, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row['value_always_active'] == 1) {
        $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
        $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
    }
    $traitsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
                     FROM uscm_advdisadv_bonus advdis
                     INNER JOIN uscm_traits a ON a.trait_name_id = advdis.traitid
                     WHERE column_id = :skillid AND table_point_name = 'skill_names' AND a.character_id = :cid";
    // print_r($traitsql);
    $stmt = $this->db->prepare($traitsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->bindValue(':skillid', $skillid, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      if ($row['value_always_active'] == 1) {
        $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
        $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
    }

    return $skillbonus;
  }

  public function getCertificates() {
    if ($this->certificates == NULL) {
      $this->certificates = $this->calculatedCertificates();
    }
    return $this->certificates;
  }

  private function calculatedCertificates() {
    $chosencertarray = $this->getCertsForCharacterWithoutReqCheck();
    $platooncertarray = $this->getCertsForPlatoon();
    $skillarray = $this->getSkillsForCharacter();
    $attribarray = $this->getAttributesForCharacter();

    $charskillattrib = array ();
    foreach ( $skillarray as $id => $value ) {
      $charskillattrib['skill_names'][$id] = $value;
    }
    foreach ( $attribarray as $id => $value ) {
      $charskillattrib['attribute_names'][$id] = $value;
    }
    $cert = getCertificateRequirements();

//     echo "Certificate requirements: \n";
//     print_r($cert);
//     echo "charskillattrb: \n";
//     print_r($charskillattrib);
    $certificatearray = array ();
    foreach ( $cert as $id => $req ) {
      $req_met = FALSE;
//       echo "cert test $id";
//       print_r($req);
      if (in_array($id, $platooncertarray) || array_key_exists($id, $chosencertarray)) {
        $has_req = FALSE;
        foreach ( $req as $reqid ) {
//           echo $reqid['id'] . "<br>";
//           print_r($charskillattrib[$reqid['table_name']]) . "<br>";

//           echo "testing ".$charskillattrib[$reqid['table_name']][$reqid['id']]." against ".$reqid['value']." ";
//           print "\n<br>";
          if ($reqid['value_greater'] == "1") {
            if (array_key_exists($reqid['id'], $charskillattrib[$reqid['table_name']]) &&
                 $charskillattrib[$reqid['table_name']][$reqid['id']] >= $reqid['value']) {
              $has_req = TRUE;
            } else {
              $has_req = FALSE;
              break;
            }
          } else {
            if ($charskillattrib[$reqid['table_name']][$reqid['id']] <= $reqid['value']) {
              $has_req = TRUE;
            } else {
              $has_req = FALSE;
              break;
            }
          }
        }
        $req_met = $has_req;
      }
      if ($req_met) {
        $certificatearray[$id]['id'] = $id;
        reset($req);
        $name = current($req);
        $certificatearray[$id]['name'] = $name['name'];
//		echo "\n<br>Has $id";
      }
    }
//	  print_r($certificatearray);
//    exit;
    return $certificatearray;
  }

  //get non-platoon certs assigned in db
  public function getCertsForCharacterWithoutReqCheck() {
    $chosencertarray = array ();
    $chosencertsql = "SELECT certificate_name_id, cn.name, c.id as uid FROM uscm_certificates as c
        INNER JOIN uscm_certificate_names as cn on cn.id = certificate_name_id
                    WHERE character_id=:cid";
    $stmt = $this->db->prepare($chosencertsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $chosencertarray[$row['certificate_name_id']]['certificate_name'] = $row['name'];
      $chosencertarray[$row['certificate_name_id']]['uid'] = $row['uid'];
    }
    return $chosencertarray;
  }

  public function getCertsBuyableWithoutReqCheck() {
	  $certarray = array ();
	  $certsql = "select id from uscm_certificate_names where id not in (select cn.id from uscm_characters as c
join uscm_certificates as cc on c.id=cc.character_id
join uscm_platoon_certificates as pc on c.platoon_id=pc.platoon_id
join uscm_certificate_names as cn on cn.id=cc.certificate_name_id or cn.id=pc.certificate_id
where c.id=:cid group by cn.id)";
	  $stmt = $this->db->prepare($certsql);
	  $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
	  $stmt->execute();
	  while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $certarray[] = $row['id'];
    }
    return $certarray;
  }

  //TODO: move?
  public function getCertsForPlatoon() {
    $platooncertarray = array ();
    $platooncertsql = "SELECT certificate_id FROM uscm_platoon_certificates
                    WHERE platoon_id=:platoonid";
    $stmt = $this->db->prepare($platooncertsql);
    $stmt->bindValue(':platoonid', $this->platoonId, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $platooncertarray[] = $row['certificate_id'];
    }
    return $platooncertarray;
  }

  public function getSkillsForCharacter() {
    $skillarray = array ();
    $skillsql = "SELECT skill_name_id as id,value
          FROM uscm_skills
          WHERE character_id=:cid";
    $stmt = $this->db->prepare($skillsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $skillarray[$row['id']] = $row['value'];
    }
    return $skillarray;
  }

  public function getSkillsWithUid() {
    $skillarray = array ();
    $skillsql = "SELECT id as uid, skill_name_id, value
          FROM uscm_skills
          WHERE character_id=:cid";
    $stmt = $this->db->prepare($skillsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getSkillsGrouped() {
    $skillarray = array ();
    $skillsql = "SELECT sn.id, sn.skill_name,s.value FROM uscm_skills s
                    LEFT JOIN uscm_skill_names sn ON s.skill_name_id=sn.id
                    LEFT JOIN uscm_skill_groups sg ON sn.skill_group_id=sg.id
                    LEFT JOIN uscm_characters c ON c.id=s.character_id
                    WHERE c.id=:cid ORDER BY sn.optional,sg.id,sn.skill_name";
    $stmt = $this->db->prepare($skillsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $skillarray[$row['id']] = $row;
    }
    return $skillarray;
  }

  public function getAttributes() {
    return $this->getAttributesForCharacter();
  }

  public function getAttributesForCharacter() {
    $attribarray = array ();
    $attribsql = "SELECT attribute_id as id,value, id as uid
          FROM uscm_attributes
          WHERE character_id=:cid ORDER BY attribute_id";
    $stmt = $this->db->prepare($attribsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $attribarray[$row['id']] = $row['value'];
    }
    return $attribarray;
  }

  public function getAttributesWithUid() {
    $attribarray = array ();
    $attribsql = "SELECT attribute_id as id,value, id as uid
          FROM uscm_attributes
          WHERE character_id=:cid ORDER BY attribute_id";
    $stmt = $this->db->prepare($attribsql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getTraits() {
    $traits = array ();
    $sql = "SELECT tn.id,trait_name, t.id as uid FROM uscm_trait_names tn
              LEFT JOIN uscm_traits t ON t.trait_name_id=tn.id
              LEFT JOIN uscm_characters c ON c.id=t.character_id
              WHERE c.id=:cid ORDER BY tn.trait_name";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
      $traits[$row['id']]['trait_name'] = $row['trait_name'];
      $traits[$row['id']]['uid'] = $row['uid'];
    }
    return $traits;
  }

    /**
   * @param int $characterId Id of a Character
   * @return int
   */
  function getXPvalue() {
	  $sql = "select UnusedXP+coalesce(s.skillxp,0)+coalesce(l.langxp,0)+coalesce(a.attrxp,0)+coalesce(av.advxp,0)+coalesce(dv.dadvxp,0)+coalesce(ce.certxp,0) as xpval from uscm_characters as c
left join (select character_id as cid, sum(round(value*(value+1)/2)) as skillxp from uscm_skills as s join uscm_skill_names as n on s.skill_name_id=n.id where skill_name not like('Language:%') group by cid) as s on c.id=s.cid
left join (select character_id as cid, sum(case value when 1 then 1 when 2 then 3 when 3 then 3 when 4 then 6 when 5 then 6 else 0 end) as langxp from uscm_skills as s join uscm_skill_names as n on s.skill_name_id=n.id where skill_name like('Language:%') group by cid) as l on c.id=l.cid
left join (select character_id as cid, sum(value*8)-200 as attrxp from uscm_attributes where attribute_id !=9 group by character_id) as a on c.id=a.cid
left join (select character_id as cid, sum(value) as advxp from uscm_advantages as a join uscm_advantage_names as n on a.advantage_name_id=n.id group by cid) as av on c.id=av.cid
left join (select character_id as cid, sum(value) as dadvxp from uscm_disadvantages as d join uscm_disadvantage_names as n on d.disadvantage_name_id=n.id group by cid) as dv on c.id=dv.cid
left join (select character_id as cid, count(distinct certificate_name_id)*2 as certxp from uscm_certificates group by cid) as ce on c.id=ce.cid
where c.id=:cid";
	$stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cid', $this->id, PDO::PARAM_INT);
    $stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	return $row['xpval'];
  }

  /**
   *
   * @param int $advantageId Id of a Advantage object
   * @return boolean
   */
  public function hasCharacterAdvantage($advantageId) {
    if ($this->advantageIds == NULL) {
      $this->populateAdvantageIds($this->getAdvantagesAll());
    }
    if (array_key_exists($advantageId, $this->advantageIds)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   *
   * @param int $disadvantageId Id of a Disadvantage object
   * @return boolean
   */
  public function hasCharacterDisadvantage($disadvantageId) {
    if ($this->disadvantageIds == NULL) {
      $this->populateDisadvantageIds($this->getDisadvantagesAll());
    }
    if (array_key_exists($disadvantageId, $this->disadvantageIds)) {
      return TRUE;
    }
    return FALSE;
  }

  private function populateAdvantageIds($advantages) {
    $this->advantageIds = array();
    foreach ($advantages as $advantage) {
      $id = $advantage->getId();
      $this->advantageIds[$id] = $id;
    }
  }

  private function populateDisadvantageIds($disadvantages) {
    $this->disadvantageIds = array();
    foreach ($disadvantages as $disadvantage) {
      $id = $disadvantage->getId();
      $this->disadvantageIds[$id] = $id;
    }
  }
}
