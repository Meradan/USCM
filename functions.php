<?php

include("config.php");
include("helper_functions.php");
include("character_functions.php");
include("auxiliary_functions.php");
include("classes/bonus.php");
include("classes/character.php");
include("classes/player.php");

$db_connection = NULL;
//set_exception_handler(die("Caught exception, going to die"));

function myconnect() {
  return mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_password']) or die("Failed to connect to database");
}

/**
 * Returns a new or existing database connection
 * @return PDO
 */
function getDatabaseConnection() {
  global $db_connection;
  if ($db_connection != NULL) {
    return $db_connection;
  } else {
    $db_connection = new PDO('mysql:host=' . $GLOBALS['db_host'] . ';dbname=' . $GLOBALS['db_database']. ';charset=utf8', $GLOBALS['db_user'], $GLOBALS['db_password']);
    return $db_connection;
  }
}

function validate($level){
  if(is_numeric($_SESSION['level'])){
    if($_SESSION['level'] >= $level){
      return 1;
    }
    else{
      return 0;
    }
  }
  else{
    if($_SESSION['level']=="konventsadmin"){
      return 1;
    }
    else{
      return 0;
    }
  }
}
/*
 * Levels:
 * 0: Logged out
 * 1: Registered user
 * 2: Game master
 * 3: Site admin
 */
function login($level){
  $db = getDatabaseConnection();
  if(isset($_GET['alt'])){
    $alt=$_GET['alt'];
    if($alt=="logout"){
    // Om anvandaren vill logga ut
      $_SESSION['inloggad']=0;
      $_SESSION['level']="0";
      unset($_SESSION['anvandarnamn']);
      unset($_SESSION['table_prefix']);
      unset($_SESSION['user_id']);
      unset($_SESSION['platoon_id']);
      return 0;
    }
    elseif($alt=="login"){
          // Unders�k om anvandarnamn och l�senord st�mmer

      if($level=="konventsadmin"){
        /*$query="select anvandarnamn from administrator
                where anvandarnamn='{$_POST['anvandarnamn']}'
                and losenord=password('{$_POST['losenord']}')";
        mysql_select_db("lincon");
        $res=mysql_query($query);
        if(mysql_num_rows($res)==1){
          $_SESSION['anvandarnamn']=$_POST['anvandarnamn'];
          $_SESSION['inloggad']=1;
          $_SESSION['rattighetsniva']="konventsadmin";
          return 1;
          exit;
        }
        else{
          $_SESSION['inloggad']=0;
          $_SESSION['rattighetsniva']="0";
          return 0;
          exit;
        }
        */
      }
      else{
        $query="select Users.id,emailadress,platoon_id,Admins.userid as Admin,GMs.userid as GM,
                logintime, count(*) as howmany from Users
                left join Admins on Admins.userid=Users.id
                left join GMs on GMs.userid=Users.id and GMs.active=1
                where emailadress='{$_POST['anvandarnamn']}'
                and password=password('{$_POST['losenord']}')";
        $stmt = $db->query($query);
        $row = $stmt->fetch();
        if ($row['howmany'] == 1){ #we have a match and only one! cool!
          $userinfo = $row;
          if ($userinfo['Admin']) {
            $userlevel = 3;
          } elseif ($userinfo['GM']) {
            $userlevel = 2;
          } else {
            $userlevel = 1;
          }
          $_SESSION['anvandarnamn']=$_POST['anvandarnamn'];
          $_SESSION['inloggad']=1;
          $_SESSION['level']=$userlevel;
          $_SESSION['table_prefix']=$_POST['rpg'];
          $_SESSION['user_id']=$userinfo['id'];
          $_SESSION['platoon_id']=$userinfo['platoon_id'];
          $db->exec("UPDATE Users SET lastlogintime = '{$userinfo['logintime']}' WHERE id = {$userinfo['id']}");
          $db->exec("UPDATE Users SET logintime = NOW() WHERE id = {$userinfo['id']}");
          return 1;
          exit;
        } elseif ($row['howmany']>1) { #more than one row returned
            //Om det inte finns endast en anvandare med dessa anvandaruppgifter s� lyckas inte inloggningen
          $_SESSION['inloggad']=0;
          $_SESSION['level']="0";
          return 0;
          exit;
        } else { #no match in the table ($row['howmany'] == 0)
          //Om det inte finns endast en anvandare med dessa anvandaruppgifter s� lyckas inte inloggningen
          $_SESSION['inloggad']=0;
          $_SESSION['level']="0";
          return 0;
          exit;
        }
      }
    }
  }
}

// Quote variable to make safe for insertion in database
function quote_smart($value)
{
  // Stripslashes
  if (get_magic_quotes_gpc()) {
    $value = stripslashes($value);
  }
  $value = mysql_real_escape_string($value);
  return $value;
}

/*
 * This function retuns all directory and file names from the given directory.
 * Works recrusive. Based on ltena at gmx dot net's function.
 * Used for checking validity of includes from GET variables
*/
function recrusive_dirlist($base_dir) {
  global $getDirList_alldirs,$getDirList_allfiles;
  function getDirList($base,$base_dir) {
    global $getDirList_alldirs,$getDirList_allfiles;
    if(is_dir($base)) {
      $dh = opendir($base);
      while (false !== ($dir = readdir($dh))) {
        if (is_dir($base ."/". $dir) && $dir !== '.' && $dir !== '..') {
          $subs = $dir    ;
          $subbase = $base ."/". $dir;
          $substrip = strip_basedir($subbase, $base_dir);
          $getDirList_alldirs[]=$substrip;
          getDirList($subbase,$base_dir);
        }
        elseif(is_file($base ."/". $dir) && $dir !== '.' && $dir !== '..') {
          $subbase = $base ."/". $dir;
          $substrip = strip_basedir($subbase, $base_dir);
          $getDirList_allfiles[]=$substrip;
        }
      }
      closedir($dh);
    }
  }
  function strip_basedir($dir, $basedir) {
    $stripped_dir=str_replace($basedir,'',$dir);
    return $stripped_dir;
  }

  getDirList($base_dir,$base_dir);
//	$retval['dirs']=$getDirList_alldirs;
//	$retval['files']=$getDirList_allfiles;
  return $getDirList_allfiles;
}

/*
 * @deprecated
 */
function characterlisting($dbreference, $sorttype) {
// sorttype is either "alive", "dead", "retired" or "glory"
  $characters = array();
  $medals = "";
  $glory = "";
  while ($character = mysql_fetch_assoc($dbreference)) {
    $characters[sizeof($characters)] = $character;
  }
  foreach ($characters as $key => $character) {
    $medals = "";
    $glory = "";
    $sql="SELECT count(m.id) as missions FROM {$_SESSION['table_prefix']}missions m
          LEFT JOIN {$_SESSION['table_prefix']}mission_names mn ON mn.id=m.mission_id
                  WHERE character_id='{$character['cid']}' AND mn.date < NOW()";
    $missions=mysql_fetch_array(mysql_query($sql));
    $characters[$key]['missions']=$missions['missions'];

    $commendationssql="SELECT medal_short,medal_glory FROM {$_SESSION['table_prefix']}characters c
                  LEFT JOIN {$_SESSION['table_prefix']}missions as missions
                    ON missions.character_id = c.id
                  LEFT JOIN {$_SESSION['table_prefix']}medal_names as mn
                    ON mn.id = missions.medal_id
                  WHERE character_id='{$character['cid']}' ORDER BY medal_glory DESC";
    $commendationsres=mysql_query($commendationssql);
    while ($commendations=mysql_fetch_array($commendationsres)) {
      if ($commendations['medal_short'] != "") { $medals = $medals . " " . $commendations['medal_short']; }
      $glory = $glory + $commendations['medal_glory'];
    }
    $characters[$key]['medals'] = ($medals!="")?($medals):("-");
    $characters[$key]['glory'] = ($glory!="")?($glory):("0");
    unset($medals,$glory);
  }
  // Obtain a list of columns
  $missions = array();
  $rank = array();
  $glory = array();
  $medals = array();
  foreach ($characters as $key => $row) {
//		var_dump($row);
//		echo "<br>";
    $rank[$key]  = $row['rank_id'];
    $missions[$key] = $row['missions'];
    $glory[$key] = $row['glory'];
    $medals[$key] = $row['medals'];
  }

  // Sort the data with volume descending, edition ascending
  // Add $data as the last parameter, to sort by the common key
  if ($sorttype == "alive") {
    array_multisort($rank,SORT_DESC,$missions,SORT_DESC,$characters);
  } elseif ($sorttype == "dead" || $sorttype == "retired") {
    array_multisort($missions,SORT_DESC,$rank,SORT_DESC,$glory,SORT_DESC,$characters);
  } elseif ($sorttype == "glory") {
    array_multisort($glory,SORT_DESC,$missions,SORT_DESC,$rank,SORT_DESC,$characters);
  }
  return $characters;
}


/*
 * Functions that's mostly relevant for character sheet
 * genereation. Takes character_id as an argument and returns
 * an array.
 */
function characterdata ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT c.forname, c.lastname, p.forname as pforname, p.lastname as plastname, emailadress,
                  age, specialty_name, rank_long, enlisted, gender, unusedxp, awarenesspoints,
                  coolpoints, exhaustionpoints, fearpoints, leadershippoints, psychopoints, traumapoints,
                  mentalpoints, pn.name_short, pn.name_long
                  FROM {$_SESSION['table_prefix']}characters c
                  LEFT JOIN Users p ON p.id=c.userid
                  LEFT JOIN {$_SESSION['table_prefix']}specialty s ON s.character_id=c.id
                  LEFT JOIN {$_SESSION['table_prefix']}specialty_names sn ON sn.id=s.specialty_name_id
                  LEFT JOIN {$_SESSION['table_prefix']}ranks r ON r.character_id=c.id
                  LEFT JOIN {$_SESSION['table_prefix']}rank_names rn ON rn.id=r.rank_id
                  LEFT JOIN {$_SESSION['table_prefix']}platoon_names pn ON pn.id=c.platoon_id
                  WHERE c.id='{$cid}'";
  $sqlres=mysql_query($sql);

  $char=mysql_fetch_assoc($sqlres);
  return $char;
}

function character_attributes ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT attribute_id,attribute_name, value
                  FROM {$_SESSION['table_prefix']}characters c
                  LEFT JOIN {$_SESSION['table_prefix']}attributes a ON a.character_id=c.id
                  LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
                  WHERE c.id='{$cid}' ORDER BY attribute_name";
  $sqlres=mysql_query($sql);
  $attributearray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $attributearray[$row['attribute_id']]['value']=$row['value'];
    $attributearray[$row['attribute_id']]['attribute_name']=$row['attribute_name'];
  }
  return $attributearray;
}

function attributebonus($cid, $modifiertype, $attribute) {
   myconnect();
   mysql_select_db("skynet");
   $bonus = Array('always' => 0, 'sometimes' => Array());
   $advsql = "SELECT $modifiertype, value_always_active
      FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
      INNER JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id = advdis.advid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = $cid
         AND $modifiertype is not NULL";
      //print_r($advsql);
   $advres=mysql_query($advsql);
   while ($row=mysql_fetch_assoc($advres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row["$modifiertype"];
      } else {
         $bonus['sometimes'][] = $row["$modifiertype"];
      }
   }
   $disadvsql = "SELECT $modifiertype, value_always_active
      FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
      INNER JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id = advdis.disadvid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = $cid
         AND $modifiertype is not NULL";
            //print_r($disadvsql);
   $disadvres=mysql_query($disadvsql);
   while ($row=mysql_fetch_assoc($disadvres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row["$modifiertype"];
      } else {
         $bonus['sometimes'][] = $row["$modifiertype"];
      }
   }
   $traitsql = "SELECT $modifiertype, value_always_active
      FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
      INNER JOIN {$_SESSION['table_prefix']}traits a ON a.trait_name_id = advdis.traitid
      WHERE column_id = $attribute AND table_point_name = 'attribute_names' AND a.character_id = $cid
         AND $modifiertype is not NULL";
            //print_r($traitsql);
   $traitres=mysql_query($traitsql);
   while ($row=mysql_fetch_assoc($traitres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row["$modifiertype"];
      } else {
         $bonus['sometimes'][] = $row["$modifiertype"];
      }
   }
   return $bonus;
}

function attribute2visible ($attributearray) {
  $attribarray = array();
  foreach ( $attributearray as $id => $key ) {
    switch ($key['attribute_name']) {
      case ("Charisma"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Don't look at it";
            break;
          case (2):
            $attribarray[] = "Ugly";
            break;
          case (3):
            $attribarray[] = "Average looks";
            break;
          case (4):
            $attribarray[] = "Good looking";
            break;
          case (5):
            $attribarray[] = "Babe magnet / Fox";
        }
        break;
      case ("Dexterity"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Walking board / Wooden leg";
            break;
          case (2):
            $attribarray[] = "Stiff";
            break;
          case (3):
            $attribarray[] = "Agile";
            break;
          case (4):
            $attribarray[] = "Very agile";
            break;
          case (5):
            $attribarray[] = "Acrobat";
        }
        break;
      case ("Endurance"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Can't run a meter";
            break;
          case (2):
            $attribarray[] = "Bad fitness";
            break;
          case (3):
            $attribarray[] = "Fit";
            break;
          case (4):
            $attribarray[] = "Good fitness";
            break;
          case (5):
            $attribarray[] = "Cross-country runner";
        }
        break;
      case ("Perception"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Nearly blind";
            break;
          case (2):
            $attribarray[] = "Near sighted";
            break;
          case (3):
            $attribarray[] = "Normal sight";
            break;
          case (4):
            $attribarray[] = "Good sight";
            break;
          case (5):
            $attribarray[] = "An eye for details";
        }
        break;
      case ("Psionics"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "";
            break;
          case (2):
            $attribarray[] = "";
            break;
          case (3):
            $attribarray[] = "";
            break;
          case (4):
            $attribarray[] = "";
            break;
          case (5):
            $attribarray[] = "";
        }
        break;
      case ("Psyche"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Coward";
            break;
          case (2):
            $attribarray[] = "Nervous";
            break;
          case (3):
            $attribarray[] = "Calm";
            break;
          case (4):
            $attribarray[] = "Very calm";
            break;
          case (5):
            $attribarray[] = "Steady as a Rock";
        }
        break;
      case ("Reaction"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Slow as a snail";
            break;
          case (2):
            $attribarray[] = "Slow";
            break;
          case (3):
            $attribarray[] = "Fast";
            break;
          case (4):
            $attribarray[] = "Very fast";
            break;
          case (5):
            $attribarray[] = "Lightning reflexes";
        }
        break;
      case ("Strength"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Weakling";
            break;
          case (2):
            $attribarray[] = "Weak";
            break;
          case (3):
            $attribarray[] = "Average strength";
            break;
          case (4):
            $attribarray[] = "Strong";
            break;
          case (5):
            $attribarray[] = "Hercules";
        }
        break;
      case ("Toughness"):
        switch ($key['value']) {
          case (1):
            $attribarray[] = "Fragile";
            break;
          case (2):
            $attribarray[] = "Easily bruised";
            break;
          case (3):
            $attribarray[] = "Tough";
            break;
          case (4):
            $attribarray[] = "Very tough";
            break;
          case (5):
            $attribarray[] = "Tough like a Sergeant";
        }
        break;
    }
  }
  return $attribarray;
}

function characterskills ($cid,$skilltype,$certarray) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT skill_name_id, value, skill_name FROM {$_SESSION['table_prefix']}skills s
          LEFT JOIN {$_SESSION['table_prefix']}skill_names sn ON sn.id=s.skill_name_id
          LEFT JOIN {$_SESSION['table_prefix']}skill_groups sg ON sg.id=sn.skill_group_id
          WHERE character_id='{$cid}' AND skill_group_name='{$skilltype}'
          ORDER BY skill_name";
  $certsql = "SELECT cn.id as cid, sn.id as sid
              FROM {$_SESSION['table_prefix']}certificate_names cn
              LEFT JOIN {$_SESSION['table_prefix']}certificate_requirements cr ON cn.id = cr.certificate_id
              LEFT JOIN {$_SESSION['table_prefix']}skill_names sn ON cr.req_item = sn.id
              GROUP BY cr.certificate_id";
  $certres=mysql_query($certsql);
  while ($row=mysql_fetch_assoc($certres)) {
    $certallarray[$row['cid']]['sid']=$row['sid'];
  }
  $sqlres=mysql_query($sql);
  $skillarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $skillarray[$row['skill_name_id']]['value']=$row['value'];
    $skillarray[$row['skill_name_id']]['name']=$row['skill_name'];
    $skillbonusarray = skillbonus($cid,$row['skill_name_id'],$certarray,$certallarray);
    $skillarray[$row['skill_name_id']]['bonus_always']=$skillbonusarray['always'];
    $skillarray[$row['skill_name_id']]['bonus_sometimes']=$skillbonusarray['sometimes'];
  }
  return $skillarray;
}
function skillbonus($cid,$skillid,$certarray,$certallarray) {
   myconnect();
   mysql_select_db("skynet");
  $skillbonus = Array('always' => 0, 'sometimes' => Array());
  // Check certificate bonus
  foreach($certarray as $key => $value) {
//		print_r($key);
//		print_r($certallarray[$key]);
//		print_r($skillid);
    if ($certallarray[$key]['sid'] == $skillid) { $skillbonus['always'] = $skillbonus['always'] + 1; };
  }
//	if (certarray[
//	print_r($skillbonus);
//	print_r($certarray);
//	print_r($certallarray);
//	exit;

  // Check adv/disadv/trait bonus
//   $sql = "SELECT skill_name_id, value, skill_name FROM {$_SESSION['table_prefix']}skills s
//               LEFT JOIN {$_SESSION['table_prefix']}skill_names sn ON sn.id=s.skill_name_id
//               LEFT JOIN {$_SESSION['table_prefix']}skill_groups sg ON sg.id=sn.skill_group_id
//               WHERE character_id='{$cid}' AND skill_group_name='{$skilltype}'
 //              ORDER BY skill_name";
   $advsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
               FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
               INNER JOIN {$_SESSION['table_prefix']}skill_names sn ON sn.id = advdis.column_id AND table_point_name = 'skill_names'
               INNER JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id = advdis.advid
               WHERE column_id = $skillid AND a.character_id = $cid";
//                     print_r($advsql);
   $advres=mysql_query($advsql);
   while ($row=mysql_fetch_assoc($advres)) {
      if ($row['value_always_active'] == 1) {
         $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
         $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
   }
   $disadvsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
                     FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
                     INNER JOIN {$_SESSION['table_prefix']}skill_names sn ON sn.id = advdis.column_id AND table_point_name = 'skill_names'
                     INNER JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id = advdis.disadvid
                     WHERE column_id = $skillid AND a.character_id = $cid";
//                     print_r($disadvsql);
   $disadvres=mysql_query($disadvsql);
   while ($row=mysql_fetch_assoc($disadvres)) {
      if ($row['value_always_active'] == 1) {
         $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
         $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
   }
   $traitsql = "SELECT modifier_dice_value, modifier_basic_value, value_always_active
                     FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
                     INNER JOIN {$_SESSION['table_prefix']}traits a ON a.trait_name_id = advdis.traitid
                     WHERE column_id = $skillid AND table_point_name = 'skill_names' AND a.character_id = $cid";
                     //print_r($traitsql);
   $traitres=mysql_query($traitsql);
   while ($row=mysql_fetch_assoc($traitres)) {
      if ($row['value_always_active'] == 1) {
         $skillbonus['always'] = $skillbonus['always'] + $row['modifier_dice_value'];
      } else {
         $skillbonus['sometimes'][] = $row['modifier_dice_value'];
      }
   }


  return $skillbonus;
}
function awareness ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT (value * 2)  as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
          WHERE an.attribute_name='Perception' AND a.character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function leadership ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT value  + (r.rank_id - 2) as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
          LEFT JOIN {$_SESSION['table_prefix']}ranks r ON r.character_id=c.id
          WHERE an.attribute_name='Charisma' AND a.character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function carrycapacity ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT 40 + (value * 5) as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='strength' AND character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function carrycapacitybonus ($cid) {
   myconnect();
   mysql_select_db("skynet");
   $capacitybonus = Array('always' => 0, 'sometimes' => Array());
   $advsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id = advdis.advid
            WHERE table_point_name = 'carrycapacity' AND a.character_id = $cid";
            //print_r($advsql);
   $advres=mysql_query($advsql);
   while ($row=mysql_fetch_assoc($advres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $disadvsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id = advdis.disadvid
            WHERE table_point_name = 'carrycapacity' AND a.character_id = $cid";
            //print_r($disadvsql);
   $disadvres=mysql_query($disadvsql);
   while ($row=mysql_fetch_assoc($disadvres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $traitsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}traits a ON a.trait_name_id = advdis.traitid
            WHERE table_point_name = 'carrycapacity' AND a.character_id = $cid";
            //print_r($traitsql);
   $traitres=mysql_query($traitsql);
   while ($row=mysql_fetch_assoc($traitres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   return $capacitybonus;
}
function combatload ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT 15 + (value * 5) as mvalue, value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='strength' AND character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  if ($result['value'] >= 3) {
    return ($result['mvalue']-5);
  } else {
    return $result['mvalue'];
  }
}
function combatloadbonus ($cid) {
   myconnect();
   mysql_select_db("skynet");
   $capacitybonus = Array('always' => 0, 'sometimes' => Array());
   $advsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id = advdis.advid
            WHERE table_point_name = 'combatload' AND a.character_id = $cid";
            //print_r($advsql);
   $advres=mysql_query($advsql);
   while ($row=mysql_fetch_assoc($advres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $disadvsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id = advdis.disadvid
            WHERE table_point_name = 'combatload' AND a.character_id = $cid";
            //print_r($disadvsql);
   $disadvres=mysql_query($disadvsql);
   while ($row=mysql_fetch_assoc($disadvres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $traitsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}traits a ON a.trait_name_id = advdis.traitid
            WHERE table_point_name = 'combatload' AND a.character_id = $cid";
            //print_r($traitsql);
   $traitres=mysql_query($traitsql);
   while ($row=mysql_fetch_assoc($traitres)) {
      if ($row['value_always_active'] == 1) {
         $capacitybonus['always'] = $capacitybonus['always'] + $row['modifier_basic_value'];
      } else {
         $capacitybonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   return $capacitybonus;
}
function psycholimit ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT value as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='psyche' AND character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function pointandlimitbonus ($cid, $bonustype) {
   myconnect();
   mysql_select_db("skynet");
   $bonus = Array('always' => 0, 'sometimes' => Array());
   $advsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id = advdis.advid
            WHERE table_point_name = '$bonustype' AND a.character_id = $cid";
            //print_r($advsql);
   $advres=mysql_query($advsql);
   while ($row=mysql_fetch_assoc($advres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row['modifier_basic_value'];
      } else {
         $bonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $disadvsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id = advdis.disadvid
            WHERE table_point_name = '$bonustype' AND a.character_id = $cid";
            //print_r($disadvsql);
   $disadvres=mysql_query($disadvsql);
   while ($row=mysql_fetch_assoc($disadvres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row['modifier_basic_value'];
      } else {
         $bonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   $traitsql = "SELECT modifier_basic_value, value_always_active
            FROM {$_SESSION['table_prefix']}advdisadv_bonus advdis
            INNER JOIN {$_SESSION['table_prefix']}traits a ON a.trait_name_id = advdis.traitid
            WHERE table_point_name = '$bonustype' AND a.character_id = $cid";
            //print_r($traitsql);
   $traitres=mysql_query($traitsql);
   while ($row=mysql_fetch_assoc($traitres)) {
      if ($row['value_always_active'] == 1) {
         $bonus['always'] = $bonus['always'] + $row['modifier_basic_value'];
      } else {
         $bonus['sometimes'][] = $row['modifier_basic_value'];
      }
   }
   return $bonus;
}
function fearlimit ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT (value * 2) as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='psyche' AND character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function exhaustionlimit ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT (value * 2) as value FROM {$_SESSION['table_prefix']}attributes a
          LEFT JOIN {$_SESSION['table_prefix']}attribute_names an ON an.id=a.attribute_id
          WHERE an.attribute_name='endurance' AND character_id='{$cid}'";
  $sqlres=mysql_query($sql);
  $result=mysql_fetch_assoc($sqlres);
  return $result['value'];
}
function missions ($cid,$length) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT mn.id,mission_name_short, rank_short, medal_short
          FROM {$_SESSION['table_prefix']}mission_names mn
          LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.mission_id=mn.id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=m.character_id
          LEFT JOIN {$_SESSION['table_prefix']}rank_names rn ON rn.id=m.rank_id
          LEFT JOIN {$_SESSION['table_prefix']}medal_names men ON men.id=m.medal_id
          WHERE character_id='{$cid}' AND mn.date < NOW() ORDER BY date";
  $sqlres=mysql_query($sql);
  $missionarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $missionarray[$row['id']]['mission_name']=$row['mission_name_short'];
    $missionarray[$row['id']]['text'] = "";
    if ( $length=="short") {
      if ($row['rank_short']) {
        $missionarray[$row['id']]['text']="Prom. ".$row['rank_short'];
      }
      elseif ($row['medal_short']) {
        $missionarray[$row['id']]['text']="Awarded ".$row['medal_short'];
      }
    } elseif ( $length=="long") {
      if ($row['rank_short']) {
        $missionarray[$row['id']]['text']="Prom. ".$row['rank_short'];
      }
      if ($row['medal_short']) {
        $missionarray[$row['id']]['text']=$missionarray[$row['id']]['text']." Awarded ".$row['medal_short'];
      }
    }
  }
  return $missionarray;
}
function medals ($cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT m.id, medal_short, medal_glory
          FROM {$_SESSION['table_prefix']}medal_names mn
          LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.medal_id=mn.id
          WHERE m.character_id='{$cid}' ORDER BY medal_glory DESC";
  $sqlres=mysql_query($sql);
  $medalarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $medalarray[$row['id']]['medal']=$row['medal_short']." (".$row['medal_glory'].")";
  }
  return $medalarray;
}

function certificates_ ($cid,$platoon_id) {
  myconnect();
  mysql_select_db("skynet");
  $skillsql = "SELECT skill_name_id as id,value
          FROM {$_SESSION['table_prefix']}skills
          WHERE character_id='{$cid}'";
  $attribsql = "SELECT attribute_id as id,value
          FROM {$_SESSION['table_prefix']}attributes
          WHERE character_id='{$cid}'";
  $certreqsql = "SELECT certificate_id, req_item,value,value_greater,table_name,name
                FROM {$_SESSION['table_prefix']}certificate_requirements cr
                LEFT JOIN {$_SESSION['table_prefix']}certificate_names cn ON cn.id=cr.certificate_id";
  $platooncertsql = "SELECT certificate_id FROM {$_SESSION['table_prefix']}platoon_certificates
                    WHERE platoon_id='{$platoon_id}'";
  $chosencertsql = "SELECT certificate_name_id FROM {$_SESSION['table_prefix']}certificates
                    WHERE character_id='{$cid}'";
  $chosencertres=mysql_query($chosencertsql);
  $chosencertarray = array();
  while ($row=mysql_fetch_assoc($chosencertres)) {
    $chosencertarray[]=$row['certificate_name_id'];
  }
  $platooncertres=mysql_query($platooncertsql);
  $platooncertarray = array();
  while ($row=mysql_fetch_assoc($platooncertres)) {
    $platooncertarray[]=$row['certificate_id'];
  }

  $skillres=mysql_query($skillsql);
  $skillarray = array();
  while ($row=mysql_fetch_assoc($skillres)) {
    $skillarray[$row['id']]=$row['value'];
  }
  $attribres=mysql_query($attribsql);
  $attribarray = array();
  while ($row=mysql_fetch_assoc($attribres)) {
    $attribarray[$row['id']]=$row['value'];
  }
  $charskillattrib = array();
  foreach ($skillarray as $id => $value) {
    $charskillattrib['skill_names'][$id]=$value;
  }
  foreach ($attribarray as $id => $value) {
    $charskillattrib['attribute_names'][$id]=$value;
  }
  $certres=mysql_query($certreqsql);
  $cert = array();
  /*
  Array
  (
    [1] => Array //certificate id
    (
      [1] => Array //req_item id
      (
        [value] => 4
        [value_greater] => 1
        [table] => skill_names
      )
    )
  }
  */
  while ($row = mysql_fetch_assoc($certres)) {
    $cert[$row['certificate_id']][$row['req_item']]['id']=$row['req_item'];
    $cert[$row['certificate_id']][$row['req_item']]['value']=$row['value'];
    $cert[$row['certificate_id']][$row['req_item']]['value_greater']=$row['value_greater'];
    $cert[$row['certificate_id']][$row['req_item']]['name']=$row['name'];
    $cert[$row['certificate_id']][$row['req_item']]['table_name']=$row['table_name'];
  }
//	print_r($cert);
  $certificatearray = array();
  foreach ($cert as $id => $req) {
    $req_met = FALSE;
//		echo "cert test ".$id." ";
//		print_r($req);
    if (in_array($id,$platooncertarray) || in_array($id,$chosencertarray)) {
                        $has_req = FALSE;
      foreach ($req as $reqid) {
//echo $reqid['id'] . "<br>";
//print_r($charskillattrib[$reqid['table_name']]) . "<br>";
//
//				echo "testing ".$charskillattrib[$reqid['table_name']][$reqid['id']]." against ".$reqid['value']." ";
//				print "\n<br>";
        if ($reqid['value_greater']=="1") {
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
      $name=current($req);
      $certificatearray[$id]['name'] = $name['name'];
    }
  }
//	print_r($certificatearray);
//	exit;
  return $certificatearray;
}

function traits_( $cid) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT tn.id, trait_name
          FROM {$_SESSION['table_prefix']}trait_names tn
          LEFT JOIN {$_SESSION['table_prefix']}traits t ON t.trait_name_id=tn.id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=t.character_id
          WHERE t.character_id='{$cid}' ORDER BY trait_name";
  $sqlres=mysql_query($sql);
  $traitarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $traitarray[$row['id']]['trait_name']=$row['trait_name'];
  }
  return $traitarray;
}

function advantages_( $cid, $onlyvisible = false) {
  myconnect();
  mysql_select_db("skynet");
  $visible = $onlyvisible ? " AND an.visible = 1" : "";
  $sql = "SELECT an.id, advantage_name
          FROM {$_SESSION['table_prefix']}advantage_names an
          LEFT JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id=an.id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
          WHERE a.character_id='{$cid}' {$visible} ORDER BY advantage_name";
  $sqlres=mysql_query($sql);
  $advarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $advarray[$row['id']]['advantage_name']=$row['advantage_name'];
  }
  return $advarray;
}

function disadvantages_( $cid, $onlyvisible = false) {
  myconnect();
  mysql_select_db("skynet");
  $sql = "SELECT dn.id, disadvantage_name
          FROM {$_SESSION['table_prefix']}disadvantage_names dn
          LEFT JOIN {$_SESSION['table_prefix']}disadvantages d ON d.disadvantage_name_id=dn.id
          LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=d.character_id
          WHERE d.character_id='{$cid}' ORDER BY disadvantage_name";
  $sqlres=mysql_query($sql);
  $disadvarray=array();
  while ($row=mysql_fetch_assoc($sqlres)) {
    $disadvarray[$row['id']]['disadvantage_name']=$row['disadvantage_name'];
  }
  return $disadvarray;
}

function print_pdf_bonus($pdf, $bonusarray) {
   if ($bonusarray['always'] != 0) {
      if ($bonusarray['always'] > 0) {
         $bonussign = "+";
      } else {
         $bonussign = "";
      }
      pdf_show($pdf,$bonussign.$bonusarray['always']." ");
   }
   if (is_array($bonusarray['sometimes'])) {
      foreach ($bonusarray['sometimes'] as $bonus) {
         if ($bonus > 0) {
            $bonussign = "+";
         } else {
            $bonussign = "";
         }
         pdf_show($pdf," (".$bonussign.$bonus.") ");
      }
   }
}

function print_text_without_br($text) {
   return strtr($text ,array("<br/>"=>""));
}
?>
