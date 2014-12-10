<?php
require_once ("config.php");
require_once ("helper_functions.php");
require_once ("character_functions.php");
require_once ("auxiliary_functions.php");
require_once ("classes/db_entity.php");
require_once ("classes/attribute.php");
require_once ("classes/bonus.php");
require_once ("classes/character.php");
require_once ("classes/news.php");
require_once ("classes/medal.php");
require_once ("classes/mission.php");
require_once ("classes/player.php");
require_once ("classes/platoon.php");
require_once ("classes/rank.php");
require_once ("classes/skill.php");
require_once ("classes/specialty.php");
require_once ("controllers/db_controller.php");
require_once ("controllers/character_controller.php");
require_once ("controllers/news_controller.php");
require_once ("controllers/medal_controller.php");
require_once ("controllers/mission_controller.php");
require_once ("controllers/platoon_controller.php");
require_once ("controllers/player_controller.php");
require_once ("controllers/rank_controller.php");
require_once ("controllers/user_controller.php");

$db_connection = NULL;
// set_exception_handler(die("Caught exception, going to die"));
function myconnect() {
  return mysql_connect($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_password']) or
       die("Failed to connect to database");
}

/**
 * Returns a new or existing database connection
 *
 * @return PDO
 */
function getDatabaseConnection() {
  global $db_connection;
  if ($db_connection != NULL) {
    return $db_connection;
  } else {
    $db_connection = new PDO(
        'mysql:host=' . $GLOBALS['db_host'] . ';dbname=' . $GLOBALS['db_database'] . ';charset=utf8',
        $GLOBALS['db_user'], $GLOBALS['db_password']);
    return $db_connection;
  }
}

function validate($level) {
  if (is_numeric($_SESSION['level'])) {
    if ($_SESSION['level'] >= $level) {
      return 1;
    } else {
      return 0;
    }
  } else {
    if ($_SESSION['level'] == "konventsadmin") {
      return 1;
    } else {
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
function login($level) {
  $db = getDatabaseConnection();
  if (isset($_GET['alt'])) {
    $alt = $_GET['alt'];
    if ($alt == "logout") {
      $_SESSION['inloggad'] = 0;
      $_SESSION['level'] = "0";
      unset($_SESSION['anvandarnamn']);
      unset($_SESSION['table_prefix']);
      unset($_SESSION['user_id']);
      unset($_SESSION['platoon_id']);
      return 0;
    } elseif ($alt == "login") {
        $query = "select Users.id,emailadress,platoon_id,Admins.userid as Admin,GMs.userid as GM,
                logintime, count(*) as howmany from Users
                left join Admins on Admins.userid=Users.id
                left join GMs on GMs.userid=Users.id and GMs.active=1
                where emailadress='{$_POST['anvandarnamn']}'
                and password=password('{$_POST['losenord']}')";
        $stmt = $db->query($query);
        $row = $stmt->fetch();
        if ($row['howmany'] == 1) {
          $userinfo = $row;
          if ($userinfo['Admin']) {
            $userlevel = 3;
          } elseif ($userinfo['GM']) {
            $userlevel = 2;
          } else {
            $userlevel = 1;
          }
          $_SESSION['anvandarnamn'] = $_POST['anvandarnamn'];
          $_SESSION['inloggad'] = 1;
          $_SESSION['level'] = $userlevel;
          $_SESSION['table_prefix'] = $_POST['rpg'];
          $_SESSION['user_id'] = $userinfo['id'];
          $_SESSION['platoon_id'] = $userinfo['platoon_id'];
          $db->exec("UPDATE Users SET lastlogintime = '{$userinfo['logintime']}' WHERE id = {$userinfo['id']}");
          $db->exec("UPDATE Users SET logintime = NOW() WHERE id = {$userinfo['id']}");
          return 1;
          exit();
        } elseif ($row['howmany'] > 1) {
          $_SESSION['inloggad'] = 0;
          $_SESSION['level'] = "0";
          return 0;
          exit();
        } else {
          $_SESSION['inloggad'] = 0;
          $_SESSION['level'] = "0";
          return 0;
          exit();
        }

    }
  }
}

/*
 * This function retuns all directory and file names from the given directory.
 * Works recrusive. Based on ltena at gmx dot net's function.
 * Used for checking validity of includes from GET variables
 */
function recursive_dirlist($base_dir) {
  global $getDirList_alldirs, $getDirList_allfiles;

  function getDirList($base, $base_dir) {
    global $getDirList_alldirs, $getDirList_allfiles;
    if (is_dir($base)) {
      $dh = opendir($base);
      while ( false !== ($dir = readdir($dh)) ) {
        if (is_dir($base . "/" . $dir) && $dir !== '.' && $dir !== '..') {
          $subs = $dir;
          $subbase = $base . "/" . $dir;
          $substrip = strip_basedir($subbase, $base_dir);
          $getDirList_alldirs[] = $substrip;
          getDirList($subbase, $base_dir);
        } elseif (is_file($base . "/" . $dir) && $dir !== '.' && $dir !== '..') {
          $subbase = $base . "/" . $dir;
          $substrip = strip_basedir($subbase, $base_dir);
          $getDirList_allfiles[] = $substrip;
        }
      }
      closedir($dh);
    }
  }

  function strip_basedir($dir, $basedir) {
    $stripped_dir = str_replace($basedir, '', $dir);
    return $stripped_dir;
  }

  getDirList($base_dir, $base_dir);
  // $retval['dirs']=$getDirList_alldirs;
  // $retval['files']=$getDirList_allfiles;
  return $getDirList_allfiles;
}


function attribute2visible($attributearray) {
  $attribarray = array ();
  foreach ( $attributearray as $id => $key ) {
    switch ($key['attribute_name']) {
      case ("Charisma") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Don't look at it";
            break;
          case (2) :
            $attribarray[] = "Ugly";
            break;
          case (3) :
            $attribarray[] = "Average looks";
            break;
          case (4) :
            $attribarray[] = "Good looking";
            break;
          case (5) :
            $attribarray[] = "Babe magnet / Fox";
        }
        break;
      case ("Dexterity") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Walking board / Wooden leg";
            break;
          case (2) :
            $attribarray[] = "Stiff";
            break;
          case (3) :
            $attribarray[] = "Agile";
            break;
          case (4) :
            $attribarray[] = "Very agile";
            break;
          case (5) :
            $attribarray[] = "Acrobat";
        }
        break;
      case ("Endurance") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Can't run a meter";
            break;
          case (2) :
            $attribarray[] = "Bad fitness";
            break;
          case (3) :
            $attribarray[] = "Fit";
            break;
          case (4) :
            $attribarray[] = "Good fitness";
            break;
          case (5) :
            $attribarray[] = "Cross-country runner";
        }
        break;
      case ("Perception") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Nearly blind";
            break;
          case (2) :
            $attribarray[] = "Near sighted";
            break;
          case (3) :
            $attribarray[] = "Normal sight";
            break;
          case (4) :
            $attribarray[] = "Good sight";
            break;
          case (5) :
            $attribarray[] = "An eye for details";
        }
        break;
      case ("Psionics") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "";
            break;
          case (2) :
            $attribarray[] = "";
            break;
          case (3) :
            $attribarray[] = "";
            break;
          case (4) :
            $attribarray[] = "";
            break;
          case (5) :
            $attribarray[] = "";
        }
        break;
      case ("Psyche") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Coward";
            break;
          case (2) :
            $attribarray[] = "Nervous";
            break;
          case (3) :
            $attribarray[] = "Calm";
            break;
          case (4) :
            $attribarray[] = "Very calm";
            break;
          case (5) :
            $attribarray[] = "Steady as a Rock";
        }
        break;
      case ("Reaction") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Slow as a snail";
            break;
          case (2) :
            $attribarray[] = "Slow";
            break;
          case (3) :
            $attribarray[] = "Fast";
            break;
          case (4) :
            $attribarray[] = "Very fast";
            break;
          case (5) :
            $attribarray[] = "Lightning reflexes";
        }
        break;
      case ("Strength") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Weakling";
            break;
          case (2) :
            $attribarray[] = "Weak";
            break;
          case (3) :
            $attribarray[] = "Average strength";
            break;
          case (4) :
            $attribarray[] = "Strong";
            break;
          case (5) :
            $attribarray[] = "Hercules";
        }
        break;
      case ("Toughness") :
        switch ($key['value']) {
          case (1) :
            $attribarray[] = "Fragile";
            break;
          case (2) :
            $attribarray[] = "Easily bruised";
            break;
          case (3) :
            $attribarray[] = "Tough";
            break;
          case (4) :
            $attribarray[] = "Very tough";
            break;
          case (5) :
            $attribarray[] = "Tough like a Sergeant";
        }
        break;
    }
  }
  return $attribarray;
}





function print_pdf_bonus($pdf, $bonusarray) {
  if ($bonusarray['always'] != 0) {
    if ($bonusarray['always'] > 0) {
      $bonussign = "+";
    } else {
      $bonussign = "";
    }
    pdf_show($pdf, $bonussign . $bonusarray['always'] . " ");
  }
  if (is_array($bonusarray['sometimes'])) {
    foreach ( $bonusarray['sometimes'] as $bonus ) {
      if ($bonus > 0) {
        $bonussign = "+";
      } else {
        $bonussign = "";
      }
      pdf_show($pdf, " (" . $bonussign . $bonus . ") ");
    }
  }
}

function print_text_without_br($text) {
  return strtr($text, array ("<br/>" => ""
  ));
}


?>
