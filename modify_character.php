<?php
myconnect();
mysql_select_db("skynet");
$userres = mysql_query("SELECT Users.id FROM Users LEFT JOIN {$_SESSION['table_prefix']}characters as c ON c.userid=Users.id WHERE c.id='{$_GET['character_id']}'");
$userquery = mysql_fetch_array($userres);
$userid = $userquery['id'];
//echo "userid ".$userid;
//echo "session ".$_SESSION['user_id'];
$admin = ($_SESSION['level'] == 3) ? (TRUE) : (FALSE);
$gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
$user = ($_SESSION['user_id'] == $userid) ? (TRUE) : (FALSE);
$platoon_idsql = "SELECT platoon_id FROM {$_SESSION['table_prefix']}characters WHERE id='{$_GET['character_id']}'";
$platoon_idres = mysql_query($platoon_idsql);
while ($row = mysql_fetch_array($platoon_idres)) {
    $platoon_id = $row['platoon_id'];
}

if ($admin || $user || $gm) {
    $charactersql = "SELECT c.platoon_id,c.forname,c.lastname,c.id,c.enlisted,c.age,c.gender,c.unusedxp,awarenesspoints,c.coolpoints,exhaustionpoints,fearpoints,leadershippoints,psychopoints,traumapoints,mentalpoints,c.status,c.status_desc,s.specialty_name_id as specialty,r.rank_id
                FROM {$_SESSION['table_prefix']}characters c
                LEFT JOIN {$_SESSION['table_prefix']}specialty s ON s.character_id=c.id
		LEFT JOIN {$_SESSION['table_prefix']}ranks r ON r.character_id=c.id
                WHERE c.id='{$_GET['character_id']}'";
//                  echo $charactersql."</br>";
    $characterres = mysql_query($charactersql);
    $character = mysql_fetch_array($characterres);
    ?>


    <?php if ($admin || $gm) { ?><form method="post" action="character.php?action=update_character"><?php } ?>
        <table width="50%"  border="0">
            <tr>
                <td>Player</td>
                <td><?php
    $playersql = "SELECT Users.id,Users.forname,Users.lastname FROM Users
                                LEFT JOIN {$_SESSION['table_prefix']}characters as c ON c.userid=Users.id
                                WHERE c.id='{$_GET['character_id']}'";
//																echo $playersql."</br>";
    $playerres = mysql_query($playersql);
    $player = mysql_fetch_array($playerres);
    echo $player['forname'] . " " . $player['lastname'];
    ?>
                    <input type="hidden" name="player" value="<?php echo $player['id']; ?>">
                    <input type="hidden" name="character" value="<?php echo $_GET['character_id']; ?>">

                </td>
                <td>&nbsp;</td>
                <td><a class="colorfont" href="./create_sheet.php?character_id=<?php echo $_GET['character_id']; ?>">Create character sheet</a></td>
            </tr>
            <tr>
                <td>Forname</td>
                <td><input type="text" name="forname" value="<?php echo $character['forname']; ?>"></td>
                <td>Lastname</td>
                <td><input type="text" name="lastname" value="<?php echo $character['lastname']; ?>"></td>
            </tr>
            <tr>
                <td>Platoon</td>
                <td><?php
    $platoonsql = "SELECT id,name_long FROM {$_SESSION['table_prefix']}platoon_names ORDER BY name_long";
//					echo $platoonsql."</br>";
    $platoonres = mysql_query($platoonsql);
    ?>
                    <select name="platoon">
    <?php while ($platoon = mysql_fetch_array($platoonres)) { ?>
                            <option value="<?php echo $platoon['id']; ?>" <?php echo ($platoon['id'] == $character['platoon_id']) ? ("selected") : (""); ?> ><?php echo $platoon['name_long']; ?></option>
    <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Specialty</td>
                <td><?php
    $specialtysql = "SELECT id, specialty_name FROM {$_SESSION['table_prefix']}specialty_names ORDER BY specialty_name";
//								echo $specialtysql;
    $specialtyres = mysql_query($specialtysql);
    ?>
                    <select name="specialty">
                    <?php while ($specialty = mysql_fetch_array($specialtyres)) { ?>
                            <option <?php echo ($specialty['id'] == $character['specialty']) ? ("selected") : (""); ?> value="<?php echo $specialty['id']; ?>" >
                            <?php echo $specialty['specialty_name']; ?></option>
                        <?php } ?>
                    </select></td>
                <td>Rank (Bör ej ändras om karaktären har fått promotion)</td>
                <td><?php
                    $ranksql = "SELECT id, rank_long FROM {$_SESSION['table_prefix']}rank_names";
//								echo $ranksql."</br>";
                    $rankres = mysql_query($ranksql);
                    ?>
                    <select name="rank">
    <?php while ($rank = mysql_fetch_array($rankres)) { ?>
                            <option <?php echo ($rank['id'] == $character['rank_id']) ? ("selected") : (""); ?> value="<?php echo $rank['id']; ?>" >
        <?php echo $rank['rank_long']; ?></option>
    <?php } ?>
                    </select></td>
            </tr>
            <tr>
                <td>Enlisted</td>
                <td><input type="text" name="enlisted" value="<?php echo $character['enlisted']; ?>">  <br/>format: YYYYMMDD</td>
                <td>Age</td>
                <td><input type="text" name="age" value="<?php echo $character['age']; ?>"></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>
                    <select name="gender">
                        <option <?php echo ($character['gender'] == "Male") ? ("selected ") : (""); ?>value="Male" >Male</option>
                        <option <?php echo ($character['gender'] == "Female") ? ("selected ") : (""); ?>value="Female">Female</option>
                    </select>
                </td>
            </tr>
            <?php
            //Ta ut alla attribut
            $attributesql = "SELECT an.id, an.attribute_name,a.value FROM {$_SESSION['table_prefix']}attribute_names an
                            LEFT JOIN {$_SESSION['table_prefix']}attributes a ON a.attribute_id=an.id
                            LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
                            WHERE c.id='{$_GET['character_id']}' ORDER BY an.id";
//                            echo $attributesql."</br>";
            $attributeres = mysql_query($attributesql);
            $allattributesres = mysql_query("SELECT id,attribute_name from {$_SESSION['table_prefix']}attribute_names ORDER BY id");
            $enumerate_attributes = TRUE;
            while ($allattributes = mysql_fetch_array($allattributesres)) {
                if ($enumerate_attributes) {
                    $attribute = mysql_fetch_array($attributeres);
                }
//			if ($allskills['id']==$skill['id']) {
                if ($allattributes['id'] == $attribute['id']) {
                    $enumerate_attributes = TRUE;
                } else {
                    $enumerate_attributes = FALSE;
                }
                ?>
                <tr>
                    <td><?php echo $allattributes['attribute_name']; ?></td>
                    <td><input name="attribute[<?php echo $allattributes['id']; ?>]" type="text" value="<?php echo ($enumerate_attributes) ? ($attribute['value']) : (""); ?>" size="2"></td>
                </tr>
    <?php } ?>
            <tr>
                <td>Unused XP</td>
                <td><input type="text" name="xp" value="<?php echo $character['unusedxp']; ?>" size="2"></td>
            </tr>
            <tr>
                <td>Awareness Points</td>
                <td><input type="text" name="ap" value="<?php echo $character['awarenesspoints']; ?>" size="2"></td>
                <td>Cool Points</td>
                <td><input type="text" name="cp" value="<?php echo $character['coolpoints']; ?>" size="2"></td>
            </tr>
            <tr>
                <td>Exhaustion Points</td>
                <td><input type="text" name="ep" value="<?php echo $character['exhaustionpoints']; ?>" size="2"></td>
                <td>Fear Points</td>
                <td><input type="text" name="fp" value="<?php echo $character['fearpoints']; ?>" size="2"></td>
            </tr>
            <tr>
                <td>Leadership Points</td>
                <td><input type="text" name="lp" value="<?php echo $character['leadershippoints']; ?>" size="2"></td>
                <td>Psycho Points</td>
                <td><input type="text" name="pp" value="<?php echo $character['psychopoints']; ?>" size="2"></td>
            </tr>
            <tr>
                <td>Trauma Points</td>
                <td><input type="text" name="tp" value="<?php echo $character['traumapoints']; ?>" size="2"></td>
                <td>Mental Points</td>
                <td><input type="text" name="mp" value="<?php echo $character['mentalpoints']; ?>" size="2"></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>
                    <select name="status">
                        <option <?php echo ($character['status'] == "Active") ? ("selected ") : (""); ?>value="Active" >Active</option>
                        <option <?php echo ($character['status'] == "PoW") ? ("selected ") : (""); ?>value="PoW">PoW</option>
                        <option <?php echo ($character['status'] == "Retired") ? ("selected ") : (""); ?>value="Retired">Retired</option>
                        <option <?php echo ($character['status'] == "Dead") ? ("selected ") : (""); ?>value="Dead">Dead</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Status Desc.</td>
                <td colspan="3"><input type="text" name="status_desc" value="<?php echo $character['status_desc']; ?>" size="60"></td>
            </tr>
            <?php
            //Ta ut alla skills
            $allskillsres = mysql_query("SELECT sn.id,skill_name,optional FROM {$_SESSION['table_prefix']}skill_names sn
															LEFT JOIN {$_SESSION['table_prefix']}skill_groups sg on sn.skill_group_id=sg.id
															ORDER BY sg.id,sn.skill_name");
            $skillsql = "SELECT sn.id, sn.skill_name,s.value FROM {$_SESSION['table_prefix']}skills s
                            LEFT JOIN {$_SESSION['table_prefix']}skill_names sn ON s.skill_name_id=sn.id
														LEFT JOIN {$_SESSION['table_prefix']}skill_groups sg ON sn.skill_group_id=sg.id
                            LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=s.character_id
                            WHERE c.id='{$_GET['character_id']}' ORDER BY sn.optional,sg.id,sn.skill_name";
//                            echo $skillsql."</br>";
            $skillres = mysql_query($skillsql);
            $enumerate_skill = TRUE;
            $side = 0;
            while ($allskills = mysql_fetch_array($allskillsres)) {
                if ($enumerate_skill) {
                    $skill = mysql_fetch_array($skillres);
                }
                if ($allskills['id'] == $skill['id']) {
                    $enumerate_skill = TRUE;
                } else {
                    $enumerate_skill = FALSE;
                }
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $allskills['skill_name']; ?></td>
                <td><input type="text" name="skills[<?php echo $allskills['id']; ?>]" value="<?php echo ($enumerate_skill) ? ($skill['value']) : (""); ?>" size="2">
                    <input type="hidden" name="optional[<?php echo $allskills['id']; ?>]" value="<?php echo $allskills['optional']; ?>">
                </td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //Traits
            $alltraitssres = mysql_query("SELECT tn.id,trait_name FROM {$_SESSION['table_prefix']}trait_names tn ORDER BY tn.trait_name");
            $traitsql = "SELECT tn.id,trait_name FROM {$_SESSION['table_prefix']}trait_names tn
              LEFT JOIN {$_SESSION['table_prefix']}traits t ON t.trait_name_id=tn.id
              LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=t.character_id
              WHERE c.id='{$_GET['character_id']}' ORDER BY tn.trait_name";
            //          echo $traitsql ."</br>";
            $traitres = mysql_query($traitsql);
            $enumerate_trait = TRUE;
            $side = 0;
            while ($alltraits = mysql_fetch_array($alltraitssres)) {
                if ($enumerate_trait) {
                    $trait = mysql_fetch_array($traitres);
                }
                if ($alltraits['id'] == $trait['id']) {
                    $enumerate_trait = TRUE;
                } else {
                    $enumerate_trait = FALSE;
                }
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $alltraits['trait_name']; ?></td>
                <td><input type="checkbox" name="traits[<?php echo $alltraits['id']; ?>]" <?php echo ($enumerate_trait) ? ("checked") : (""); ?> ></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //advantages
            $alladvssres = mysql_query("SELECT id,advantage_name,value FROM {$_SESSION['table_prefix']}advantage_names ORDER BY advantage_name");
            $advsql = "SELECT an.id,advantage_name FROM {$_SESSION['table_prefix']}advantage_names an
              LEFT JOIN {$_SESSION['table_prefix']}advantages a ON a.advantage_name_id=an.id
              LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
              WHERE c.id='{$_GET['character_id']}' ORDER BY advantage_name";
//            echo $advsql."</br>";
            $advres = mysql_query($advsql);
            $enumerate_adv = TRUE;
            $side = 0;
            while ($alladvs = mysql_fetch_array($alladvssres)) {
                if ($enumerate_adv) {
                    $adv = mysql_fetch_array($advres);
                }
                if ($alladvs['id'] == $adv['id']) {
                    $enumerate_adv = TRUE;
                } else {
                    $enumerate_adv = FALSE;
                }
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $alladvs['advantage_name'] . " (" . $alladvs['value'] . ")"; ?></td>
                <td><input type="checkbox" name="advs[<?php echo $alladvs['id']; ?>]" <?php echo ($enumerate_adv) ? ("checked") : (""); ?> ></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //disadvantages
            $alldisadvssres = mysql_query("SELECT id,disadvantage_name,value FROM {$_SESSION['table_prefix']}disadvantage_names ORDER BY disadvantage_name");
            $disadvsql = "SELECT an.id,disadvantage_name FROM {$_SESSION['table_prefix']}disadvantage_names an
              LEFT JOIN {$_SESSION['table_prefix']}disadvantages a ON a.disadvantage_name_id=an.id
              LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=a.character_id
              WHERE c.id='{$_GET['character_id']}' ORDER BY disadvantage_name";
//            echo $disadvsql."</br>";
            $disadvres = mysql_query($disadvsql);
            $enumerate_disadv = TRUE;
            $side = 0;
            while ($alldisadvs = mysql_fetch_array($alldisadvssres)) {
                if ($enumerate_disadv) {
                    $disadv = mysql_fetch_array($disadvres);
                }
                if ($alldisadvs['id'] == $disadv['id']) {
                    $enumerate_disadv = TRUE;
                } else {
                    $enumerate_disadv = FALSE;
                }
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $alldisadvs['disadvantage_name'] . " (" . $alldisadvs['value'] . ")"; ?></td>
                <td><input type="checkbox" name="disadvs[<?php echo $alldisadvs['id']; ?>]" <?php echo ($enumerate_disadv) ? ("checked") : (""); ?> ></td>
                <?php
                echo ($side == 1) ? ("</tr>") : ("");
                $side = ($side + 1) % 2;
            }
            ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php
            //Certificates
            $allcertres = mysql_query("SELECT id,name FROM {$_SESSION['table_prefix']}certificate_names ORDER BY name");
            $allcertplatoonres = mysql_query("SELECT certificate_id FROM {$_SESSION['table_prefix']}platoon_certificates
																	WHERE platoon_id='{$platoon_id}'");
            $allcertplatoonarray = array();
            while ($row = mysql_fetch_assoc($allcertplatoonres)) {
                $allcertplatoonarray[] = $row['certificate_id'];
            }
            $certsql = "SELECT cn.id,cn.name FROM {$_SESSION['table_prefix']}certificate_names cn
              LEFT JOIN {$_SESSION['table_prefix']}certificates ct ON ct.certificate_name_id=cn.id
              LEFT JOIN {$_SESSION['table_prefix']}characters c ON c.id=ct.character_id
              WHERE c.id='{$_GET['character_id']}' ORDER BY cn.name";
//            echo $certsql."</br>";
            $certres = mysql_query($certsql);
            $certarray = certificates($_GET['character_id'], 1);
            $cert = array();
            foreach ($certarray as $key) {
                $cert[] = $key['id'];
            }
            while ($row = mysql_fetch_array($certres)) {
                $cert[] = $row['id'];
            }
            $enumerate_disadv = TRUE;
            $side = 0;
//		print_r($cert);
//		print_r($allcertplatoonarray);
            while ($allcert = mysql_fetch_array($allcertres)) {
                /* 			if ($enumerate_cert) {
                  $cert=mysql_fetch_array($certres);
                  }
                  if ($allcert['id']==$cert['id']) {
                  $enumerate_cert=TRUE;
                  } else {
                  $enumerate_cert=FALSE;
                  } */
                echo ($side == 0) ? ("<tr>") : ("");
                ?>
                <td><?php echo $allcert['name']; ?></td>
                <td><input type="checkbox" name="certs[<?php echo $allcert['id']; ?>]" <?php echo (in_array($allcert['id'], $cert)) ? ("checked ") : ("");
                echo (in_array($allcert['id'], $allcertplatoonarray)) ? ("disabled ") : (""); ?> ></td>
        <?php
        echo ($side == 1) ? ("</tr>") : ("");
        $side = ($side + 1) % 2;
    }
    ?>
            <tr>
                <td>&nbsp;</td>
            </tr>
    <?php
    $missionsarray = missions($_GET['character_id'], "long");
    foreach ($missionsarray as $mission) {
        ?>
                <tr>
                    <td><?php echo $mission['mission_name']; ?></td>
                    <td colspan="3"><?php echo $mission['text']; ?></td>
                </tr>
    <?php }
    if ($admin || $gm) {
        ?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Submit"></td>
                </tr>
    <?php } ?>
        </table>
    <?php if ($admin || $gm) { ?></form> <?php }
}
?>