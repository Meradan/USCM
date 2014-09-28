<?php
$admin = ($_SESSION['level'] == 3) ? (TRUE) : (FALSE);
$gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
if ($admin || $gm) {
    myconnect();
    mysql_select_db("skynet");
    $missionssql = "SELECT mission_name_short,date,gm,mission_name,id,briefing,debriefing,platoon_id
				FROM {$_SESSION['table_prefix']}mission_names
				WHERE id='{$_GET['mission']}'";
    $missionres = mysql_query($missionssql);
    $mission = mysql_fetch_array($missionres);
    $platoon_names_sql = "SELECT name_long,name_short,id FROM
				{$_SESSION['table_prefix']}platoon_names";
    $platoon_names_res = mysql_query($platoon_names_sql);
    ?>
    <br><br>

    <table width="50%"  border="0"> 
    <?php if ($_GET['what'] == "names") { ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $_GET['mission']; ?>"> 
                <tr>
                    <td>Mission</td>
                    <td><input type="text" name="mission_name_short" value="<?php echo $mission['mission_name_short']; ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><input type="text" name="mission_name" value="<?php echo $mission['mission_name']; ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td><input type="text" name="date" value="<?php echo $mission['date']; ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Platoon</td>
                    <td><select name="platoon_id" style="width:200;">
                            <?php while ($platoon_name = mysql_fetch_assoc($platoon_names_res)) { ?>
                                <option value="<?php echo $platoon_name['id']; ?>" <?php echo ($platoon_name['id'] == $mission['platoon_id']) ? ("selected") : (""); ?> ><?php echo $platoon_name['name_long']; ?></option>
        <?php } ?></select>		
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Change"></td>
                </tr>
            </form>
        <?php
        } elseif ($_GET['what'] == "gm") {
            ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $_GET['mission']; ?>"> 
                <tr>
                    <td>Game master</td>
                    <td>
                        <select name="gm">
                            <option value="" <?php echo ($mission['gm'] == "") ? ("selected ") : (""); ?> ></option>
                            <?php
                            $gmsql = "SELECT Users.id,forname,lastname FROM Users LEFT JOIN GMs on GMs.userid=Users.id
								LEFT JOIN RPG on RPG.id=GMs.rpg_id
								WHERE table_prefix='{$_SESSION['table_prefix']}'";
                            $gmres = mysql_query($gmsql);
                            while ($gm = mysql_fetch_array($gmres)) {
                                ?>
                                <option value="<?php echo $gm['id']; ?>" <?php echo ($mission['gm'] == $gm['id']) ? ("selected ") : (""); ?> ><?php echo $gm['forname'] . " " . $gm['lastname']; ?></option>
        <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "briefing") { ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $_GET['mission']; ?>"> 
                <tr>
                    <td>Briefing<br><br>Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning</td>
                </tr>
                <tr>
                    <td><textarea name="briefing" cols="80" rows="40"><?php echo print_text_without_br($mission['briefing']); ?></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "debriefing") {
        ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $_GET['mission']; ?>"> 
                <tr>
                    <td>Debriefing<br><br>Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning</td>
                </tr>
                <tr>
                    <td><textarea name="debriefing" cols="80" rows="40"><?php echo print_text_without_br($mission['debriefing']); ?></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "characters") {
        ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $_GET['mission']; ?>"> 
                <tr>
                    <td>Characters</td>
                    <?php
                    $charactersql = "SELECT c.id,c.forname,c.lastname
							FROM {$_SESSION['table_prefix']}characters c 
							WHERE c.status!='Dead' AND c.status!='Retired'
							ORDER BY c.lastname,c.forname";
//							echo $charactersql;
                    $characterres = mysql_query($charactersql);
                    $withonmissionres = mysql_query("SELECT character_id FROM {$_SESSION['table_prefix']}missions m WHERE mission_id='{$_GET['mission']}'");
                    $withonmission = array();
                    while ($with = mysql_fetch_array($withonmissionres)) {
                        $withonmission[$with['character_id']] = TRUE;
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        <select name="characters[]" size="24" multiple>
                            <?php while ($character = mysql_fetch_array($characterres)) { ?>
                                <option value="<?php echo $character['id']; ?>" <?php echo ($withonmission[$character['id']]) ? ("selected ") : (""); ?> >
            <?php echo $character['forname'] . " " . $character['lastname']; ?></option>
        <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input type="submit" value="Change"></td>
                </tr>
            </form>
            <?php } elseif ($_GET['what'] == "commendations") {
                ?>
            <tr>
                <td colspan="2" style="width:100%"><br/><br/><br/>F�r tillf�llet g�r det ej att tilldela en medalj fr�n USCM och en nationsmedalj, utan de skriver bara �ver varandra om man f�rs�ker.<br/><br/><br/><br/><br/>Characters</td>
                <?php
                $charactersql = "SELECT c.id,c.forname,c.lastname
							FROM {$_SESSION['table_prefix']}characters c
							LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.character_id = c.id
							WHERE m.mission_id='{$_GET['mission']}'";
//							echo $charactersql;
                $characterres = mysql_query($charactersql);
                $medalsql = "SELECT id,medal_short,medal_name FROM {$_SESSION['table_prefix']}medal_names mn WHERE mn.foreign_medal='0' ORDER BY medal_glory";
                $medalres = mysql_query($medalsql);
                if ($_POST['characters']) {
                    $awardedmedalssql = "SELECT mn.id as medalid,m.character_id  FROM {$_SESSION['table_prefix']}medal_names mn
							LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.medal_id=mn.id
							WHERE m.mission_id='{$_GET['mission']}' AND mn.foreign_medal='0'";
                    $first = TRUE;
                    foreach ($_POST['characters'] as $character_id => $on) {
                        if ($first) {
                            $awardedmedalssql = $awardedmedalssql . " AND (m.character_id='{$character_id}'";
                            $firstmedal = $character_id;
                            $first = FALSE;
                        } else {
                            $awardedmedalssql = $awardedmedalssql . " OR m.character_id='{$character_id}'";
                        }
                    }
                    $awardedmedalssql = $awardedmedalssql . ") ORDER BY mn.medal_glory DESC";
//			echo $awardedmedalssql;
                    $awardedmedalsres = mysql_query($awardedmedalssql);
                    $awardedmedal = mysql_fetch_array($awardedmedalsres);
                }
                ?>
            </tr>
            <form method="post" action="index.php?url=modify_mission.php&mission=<?php echo $_GET['mission']; ?>&what=commendations&selectedcharacters=true&foreign=false">
        <?php while ($character = mysql_fetch_array($characterres)) { ?>
                    <tr>
                        <td><?php echo $character['forname'] . " " . $character['lastname']; ?>
                        </td>
                        <td><input type="checkbox" name="characters[<?php echo $character['id']; ?>]" <?php echo ($_POST['characters'][$character['id']]) ? ("checked ") : (""); ?>></td>
                    </tr>
        <?php } ?>
                <tr>
                    <td colspan="2"><input type="submit" value="Select characters"></td>
            </form>
        </tr>
        <tr>
            <td colspan="2" style="width:100%">Medals</td>
        </tr>
        <tr>
        <form method="post" action="mission.php?what=commendations&mission=<?php echo $_GET['mission']; ?>"> 
            <?php if ($_POST['characters']) { ?>
            <?php foreach ($_POST['characters'] as $character_id => $dummy) { ?>
                    <input type="hidden" name="characters[<?php echo $character_id; ?>]" value="on">
                        <?php } ?>
                <td colspan="2"><select name="medal" style="width:100%">
                        <option value="">No medal</option>
            <?php while ($medal = mysql_fetch_array($medalres)) { ?>
                            <option value="<?php echo $medal['id']; ?>" <?php echo ($medal['id'] == $awardedmedal['medalid']) ? ("selected ") : (""); ?>><?php echo $medal['medal_name']; ?></option>			
            <?php } ?>
                    </select>
                </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Award medal"></td> <?php } ?>
        </form>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="colorfont">Foreign Medals</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" style="width:100%">Characters</td>
            <?php
            $charactersql = "SELECT c.id,c.forname,c.lastname
							FROM {$_SESSION['table_prefix']}characters c
							LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.character_id = c.id
							WHERE m.mission_id='{$_GET['mission']}'";
//							echo $charactersql;
            $characterres = mysql_query($charactersql);
            $medalsql = "SELECT id,medal_short,medal_name FROM {$_SESSION['table_prefix']}medal_names mn WHERE mn.foreign_medal='1' ORDER BY medal_glory";
            $medalres = mysql_query($medalsql);
            if ($_POST['characters']) {
                $awardedmedalssql = "SELECT mn.id as medalid,m.character_id  FROM {$_SESSION['table_prefix']}medal_names mn
							LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.medal_id=mn.id
							WHERE m.mission_id='{$_GET['mission']}' AND mn.foreign_medal='1'";
                $first = TRUE;
                foreach ($_POST['characters'] as $character_id => $on) {
                    if ($first) {
                        $awardedmedalssql = $awardedmedalssql . " AND (m.character_id='{$character_id}'";
                        $firstmedal = $character_id;
                        $first = FALSE;
                    } else {
                        $awardedmedalssql = $awardedmedalssql . " OR m.character_id='{$character_id}'";
                    }
                }
                $awardedmedalssql = $awardedmedalssql . ") ORDER BY mn.medal_glory DESC";
//			echo $awardedmedalssql;
                $awardedmedalsres = mysql_query($awardedmedalssql);
                $awardedmedal = mysql_fetch_array($awardedmedalsres);
            }
            ?>
        </tr>
        <form method="post" action="index.php?url=modify_mission.php&mission=<?php echo $_GET['mission']; ?>&what=commendations&selectedcharacters=true&foreign=true">
        <?php while ($character = mysql_fetch_array($characterres)) { ?>
                <tr>
                    <td><?php echo $character['forname'] . " " . $character['lastname']; ?>
                    </td>
                    <td><input type="checkbox" name="characters[<?php echo $character['id']; ?>]" <?php echo ($_POST['characters'][$character['id']]) ? ("checked ") : (""); ?>></td>
                </tr>
        <?php } ?>
            <tr>
                <td colspan="2"><input type="submit" value="Select characters"></td>
        </form>
        </tr>
        <tr>
            <td colspan="2" style="width:100%">Medals</td>
        </tr>
        <tr>
        <form method="post" action="mission.php?what=commendations&mission=<?php echo $_GET['mission']; ?>"> 
            <?php if ($_POST['characters']) { ?>
            <?php foreach ($_POST['characters'] as $character_id => $dummy) { ?>
                    <input type="hidden" name="characters[<?php echo $character_id; ?>]" value="on">
                        <?php } ?>
                <td colspan="2"><select name="medal" style="width:100%">
                        <option value="">No medal</option>
            <?php while ($medal = mysql_fetch_array($medalres)) { ?>
                            <option value="<?php echo $medal['id']; ?>" <?php echo ($medal['id'] == $awardedmedal['medalid']) ? ("selected ") : (""); ?>><?php echo $medal['medal_name']; ?></option>			
            <?php } ?>
                    </select>
                </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Award medal"></td> <?php } ?>
        </form>
        </tr>
    <?php } elseif ($_GET['what'] == "promotions") {
        ?>
        <?php
        $charactersql = "SELECT c.id,c.forname,c.lastname
							FROM {$_SESSION['table_prefix']}characters c
							LEFT JOIN {$_SESSION['table_prefix']}missions m ON m.character_id = c.id
							WHERE m.mission_id='{$_GET['mission']}'";
//							echo $charactersql;
        $characterres = mysql_query($charactersql);
        if ($_POST['character']) {
            $charactersql = "SELECT rank_id FROM {$_SESSION['table_prefix']}ranks WHERE character_id='{$_POST['character']}'";
            $characterrankres = mysql_query($charactersql);
            $characterrank = mysql_fetch_array($characterrankres);
            $ranksql = "SELECT id,rank_long FROM {$_SESSION['table_prefix']}rank_names WHERE id>=('{$characterrank['rank_id']}'+1) OR id=('{$characterrank['rank_id']}'-1)";
            $ranksres = mysql_query($ranksql);
            $missionsql = "SELECT rank_id FROM {$_SESSION['table_prefix']}missions WHERE character_id='{$_POST['character']}' AND mission_id='{$_GET['mission']}'";
            $missionpromotionres = mysql_query($missionsql);
            $missionpromotion = mysql_fetch_array($missionpromotionres);
//		echo $charactersql;
//		echo $ranksql;
//		echo $missionsql;
        }
        ?>
        <form method="post" action="index.php?url=modify_mission.php&mission=<?php echo $_GET['mission']; ?>&what=promotions&selectedcharacters=true">
            <tr>
                <td><select name="character">
        <?php while ($character = mysql_fetch_array($characterres)) { ?>
                            <option value="<?php echo $character['id']; ?>"><?php echo $character['forname'] . " " . $character['lastname']; ?></option>
        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" value="Select characters"></td>
            </tr></form>
        <tr>
            <td colspan="2" style="width:100%">Rank</td>
        </tr>
        <tr>
        <form method="post" action="mission.php?what=promotion&mission=<?php echo $_GET['mission']; ?>"> 
                    <?php if ($_POST['character']) { ?>
                <input type="hidden" name="character" value="<?php echo $_POST['character']; ?>">
                <td colspan="2"><select name="rank" style="width:100%">
                        <option value="">No promotion</option>
            <?php while ($rank = mysql_fetch_array($ranksres)) { ?>
                            <option value="<?php echo $rank['id']; ?>" <?php echo ($rank['id'] == $missionpromotion['rank_id']) ? ("selected ") : (""); ?>><?php echo $rank['rank_long']; ?></option>			
            <?php } ?>
                    </select>
                </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Award promotion"></td> <?php } ?>
        </form>
        </tr>
    <?php } ?>
    </table>
    <?php
} else {
    include("not_allowed.php");
}
?>