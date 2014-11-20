<?php
$admin = ($_SESSION['level'] == 3) ? (TRUE) : (FALSE);
$gm = ($_SESSION['level'] == 2) ? (TRUE) : (FALSE);
if ($admin || $gm) {
    $missionId = $_GET['mission'];
    $missionController = new MissionController();
    $platoonController = new PlatoonController();
    $playerController = new PlayerController();
    $characterController = new CharacterController();
    $rankController = new RankController();
    $medalController = new MedalController();
    $mission = $missionController->getMission($missionId);
    $platoons = $platoonController->getPlatoons();
    if (array_key_exists('characters', $_POST)) {
      $postCharacters = $_POST['characters'];
    } else {
      $postCharacters = array();
    }
    if (array_key_exists('character', $_POST)) {
      $postCharacter = $_POST['character'];
    } else {
      $postCharacter = array();
    }
    ?>
    <br><br>

    <table width="50%"  border="0">
    <?php if ($_GET['what'] == "names") { ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
                <tr>
                    <td>Mission</td>
                    <td><input type="text" name="mission_name_short" value="<?php echo $mission->getShortName(); ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><input type="text" name="mission_name" value="<?php echo $mission->getName(); ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td><input type="text" name="date" value="<?php echo $mission->getDate(); ?>" style="width:200;" ></td>
                </tr>
                <tr>
                    <td>Platoon</td>
                    <td><select name="platoon_id" style="width:200;">
                            <?php foreach ($platoons as $platoon) {
                            ?>
                                <option value="<?php echo $platoon->getId(); ?>" <?php echo ($platoon->getId() == $mission->getPlatoonId()) ? ("selected") : (""); ?> ><?php echo $platoon->getName(); ?></option>
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
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
                <tr>
                    <td>Game master</td>
                    <td>
                        <select name="gm">
                            <option value="" <?php echo ($mission->getGmId() == "") ? ("selected ") : (""); ?> ></option>
                            <?php
                            $gms = $playerController->getGms();
                            foreach ($gms as $gm) {
                                ?>
                                <option value="<?php echo $gm->getId(); ?>" <?php echo ($mission->getGmId() == $gm->getId()) ? ("selected ") : (""); ?> ><?php echo $gm->getName(); ?></option>
        <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "briefing") { ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
                <tr>
                    <td>Briefing<br><br>Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning</td>
                </tr>
                <tr>
                    <td><textarea name="briefing" cols="80" rows="40"><?php echo print_text_without_br($mission->getBriefing()); ?></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "debriefing") {
        ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
                <tr>
                    <td>Debriefing<br><br>Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning</td>
                </tr>
                <tr>
                    <td><textarea name="debriefing" cols="80" rows="40"><?php echo print_text_without_br($mission->getDebriefing()); ?></textarea></td>
                </tr>
                <tr>
                    <td><input type="submit" value="Change"></td>
                </tr>
            </form>
    <?php } elseif ($_GET['what'] == "characters") {
        ?>
            <form method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
                <tr>
                    <td>Characters</td>
                </tr>
                <tr>
                    <td>
                        <select name="characters[]" size="24" multiple><?php
                            $characters = $characterController->getActiveCharacters();
                            $withOnMission = $characterController->getCharacterIdsOnMission($mission);
                            foreach ($characters as $character) {
                              ?>
                                <option value="<?php echo $character->getId(); ?>" <?php
                                echo (array_key_exists($character->getId(), $withOnMission)) ? ("selected ") : (""); ?> ><?php
                                echo $character->getName(); ?></option>
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
                <td colspan="2" style="width:100%"><br/><br/><br/>För tillfället går det ej att tilldela en medalj från USCM och en nationsmedalj, utan de skriver bara över varandra om man försöker.<br/><br/><br/><br/><br/>Characters</td>
                <?php
                ?>
            </tr>
            <form method="post" action="index.php?url=modify_mission.php&mission=<?php
              echo $missionId; ?>&what=commendations&selectedcharacters=true&foreign=false">
        <?php
        $characters = $characterController->getCharactersOnMission($mission);
        foreach ($characters as $character) {
          ?>
                    <tr>
                        <td><?php echo $character->getName(); ?>
                        </td>
                        <td><input type="checkbox" name="characters[<?php
                          echo $character->getId(); ?>]" <?php
                            echo (array_key_exists($character->getId(), $postCharacters)) ? ("checked ") : (""); ?>></td>
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
        <form method="post" action="mission.php?what=commendations&mission=<?php echo $missionId; ?>">
            <?php if ($postCharacters) { ?>
            <?php foreach ($postCharacters as $character_id => $dummy) { ?>
                    <input type="hidden" name="characters[<?php echo $character_id; ?>]" value="on">
                        <?php } ?>
                <td colspan="2"><select name="medal" style="width:100%">
                        <option value="">No medal</option>
            <?php

            $awardedMedalId = $missionController->getHighestAwardedUscmMedalOnMissionForCharacterIds($mission, $postCharacters);
            $medals = $medalController->getMedals();
            foreach ($medals as $medal) { ?>
                            <option value="<?php echo $medal->getId(); ?>" <?php echo ($medal->getId() == $awardedMedalId) ? ("selected ") : (""); ?>><?php echo $medal->getName(); ?></option>
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
        if ($postCharacter) {
        }
        ?>
        <form method="post" action="index.php?url=modify_mission.php&mission=<?php echo $missionId; ?>&what=promotions&selectedcharacters=true">
            <tr>
                <td><select name="character"><?php
        $characters = $characterController->getCharactersOnMission($mission);
        foreach ($characters as $character) {
          ?>
                            <option value="<?php echo $character->getId(); ?>" <?php echo ($character->getId() == $postCharacter) ? ("selected ") : (""); ?>><?php echo $character->getName(); ?></option>
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
        <form method="post" action="mission.php?what=promotion&mission=<?php echo $missionId; ?>"><?php
        if ($postCharacter) { ?>
                <input type="hidden" name="character" value="<?php echo $postCharacter; ?>">
                <td colspan="2"><select name="rank" style="width:100%">
                        <option value="">No promotion</option><?php
          $character = $characterController->getCharacter($postCharacter);
          $availableRanks = $rankController->getPromotableRanksForCharacter($character);
          $promotionRankId = $missionController->getPromotionForCharacterOnMission($character, $mission);
          foreach ($availableRanks as $rank) {
            ?>
                            <option value="<?php echo $rank->getId(); ?>" <?php echo ($rank->getId() == $promotionRankId) ? ("selected ") : (""); ?>><?php echo $rank->getName(); ?></option>
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
