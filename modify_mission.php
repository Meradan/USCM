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
  <h1 class="heading heading-h1">Modify mission</h1>
  <h2 class="heading heading-h2"><?php echo $mission->getShortName(); ?></h2>

    <?php if ($_GET['what'] == "names") { ?>
      <form class="form" method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
        <label for="mission_name_short">
          Mission
          <input type="text" id="mission_name_short" name="mission_name_short" value="<?php echo $mission->getShortName(); ?>">
        </label>

        <label for="mission_name">
          Name
          <input type="text" id="mission_name" name="mission_name" value="<?php echo $mission->getName(); ?>">
        </label>

        <label for="date">
          Date
          <input type="text" id="date" name="date" value="<?php echo $mission->getDate(); ?>">
        </label>

        <label for="platoon_id">
          Platoon
          <select id="platoon_id" name="platoon_id">
                            <?php foreach ($platoons as $platoon) {
                            ?>
                                <option value="<?php echo $platoon->getId(); ?>" <?php echo ($platoon->getId() == $mission->getPlatoonId()) ? ("selected") : (""); ?> ><?php echo $platoon->getName(); ?></option>
        <?php } ?></select>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

      <?php
        } elseif ($_GET['what'] == "gm") {
      ?>

      <form class="form" method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
        <label for="gm">
          Game master
          <select id="gm" name="gm">
                            <option value="" <?php echo ($mission->getGmId() == "") ? ("selected ") : (""); ?> ></option>
                            <?php
                            $gms = $playerController->getGms();
                            foreach ($gms as $gm) {
                                ?>
                                <option value="<?php echo $gm->getId(); ?>" <?php echo ($mission->getGmId() == $gm->getId()) ? ("selected ") : (""); ?> ><?php echo $gm->getName(); ?></option>
        <?php } ?>
                        </select>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

    <?php } elseif ($_GET['what'] == "briefing") { ?>

      <form class="form" method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
        <label for="briefing">
          Briefing<br>
          Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning
          <textarea id="briefing" name="briefing" rows="20"><?php echo print_text_without_br($mission->getBriefing()); ?></textarea>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

    <?php } elseif ($_GET['what'] == "debriefing") {
        ?>

      <form class="form" method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
        <label for="briefing">
          Debriefing<br>
          Alla radbrytningar kommer bytas ut till en radbrytning och html-tecknet för radbrytning
          <textarea id="debriefing" name="debriefing" rows="20"><?php echo print_text_without_br($mission->getDebriefing()); ?></textarea>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

    <?php } elseif ($_GET['what'] == "characters") {
        ?>

      <form class="form" method="post" action="mission.php?what=<?php echo $_GET['what']; ?>&mission=<?php echo $missionId; ?>">
        <label for="characters">
          Characters

          <select id="characters" name="characters[]" size="24" multiple><?php
                            $characters = $characterController->getActiveCharacters();
                            $withOnMission = $characterController->getCharacterIdsOnMission($mission);
                            foreach ($characters as $character) {
                              ?>
                                <option value="<?php echo $character->getId(); ?>" <?php
                                echo (array_key_exists($character->getId(), $withOnMission)) ? ("selected ") : (""); ?> ><?php
                                echo $character->getName(); ?></option>
                          <?php } ?>
                        </select>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

            <?php } elseif ($_GET['what'] == "commendations") {
                ?>

      <form class="form" method="post" action="index.php?url=modify_mission.php&mission=<?php
              echo $missionId; ?>&what=commendations&selectedcharacters=true&foreign=false">

        För tillfället går det ej att tilldela en medalj från USCM och en nationsmedalj, utan de skriver bara över varandra om man försöker.

        <fieldset>
          <legend>Characters</legend>

        <?php
        $characters = $characterController->getCharactersOnMission($mission);
        foreach ($characters as $character) {
          ?>
          <label for="character_<?php echo $character->getId(); ?>">
            <?php echo $character->getName(); ?>
            <input
              type="checkbox"
              id="character_<?php echo $character->getId(); ?>"
              name="characters[<?php echo $character->getId(); ?>]"
              <?php echo (array_key_exists($character->getId(), $postCharacters)) ? ("checked ") : (""); ?>
            >
          </label>
        <?php } ?>
        </fieldset>

        <input class="button" type="submit" value="Modify Mission">
      </form>

      <form class="form" method="post" action="mission.php?what=commendations&mission=<?php echo $missionId; ?>">
            <?php if ($postCharacters) { ?>
            <?php foreach ($postCharacters as $character_id => $dummy) { ?>
                    <input type="hidden" name="characters[<?php echo $character_id; ?>]" value="on">
                        <?php } ?>

          <label for="character">
            Medal
            <select id="medal" name="medal">
                        <option value="">No medal</option>
            <?php
            $awardedMedalId = $missionController->getHighestAwardedUscmMedalOnMissionForCharacterIds($mission, $postCharacters);
            $medals = $medalController->getMedals();
            foreach ($medals as $medal) { ?>
                            <option value="<?php echo $medal->getId(); ?>" <?php echo ($medal->getId() == $awardedMedalId) ? ("selected ") : (""); ?>><?php echo $medal->getName(); ?></option>
            <?php } ?>
                    </select>
          </label>

          <input class="button" type="submit" value="Modify Mission">
                    <?php } ?>
      </form>

    <?php } elseif ($_GET['what'] == "promotions") {
        ?>

      <form class="form" method="post" action="index.php?url=modify_mission.php&mission=<?php echo $missionId; ?>&what=promotions&selectedcharacters=true">
        <label for="character">
          Character
          <select id="character" name="character"><?php
        $characters = $characterController->getCharactersOnMission($mission);
        foreach ($characters as $character) {
          ?>
                            <option value="<?php echo $character->getId(); ?>" <?php echo ($character->getId() == $postCharacter) ? ("selected ") : (""); ?>><?php echo $character->getName(); ?></option>
        <?php } ?>
                    </select>
        </label>

        <input class="button" type="submit" value="Modify Mission">
      </form>

      <form class="form" method="post" action="mission.php?what=promotion&mission=<?php echo $missionId; ?>"><?php
        if ($postCharacter) { ?>

                <input type="hidden" name="character" value="<?php echo $postCharacter; ?>">

        <label for="rank">
          Rank
          <select id="rank" name="rank">
                        <option value="">No promotion</option><?php
          $character = $characterController->getCharacter($postCharacter);
          $availableRanks = $rankController->getPromotableRanksForCharacter($character);
          $promotionRankId = $missionController->getPromotionForCharacterOnMission($character, $mission);
          foreach ($availableRanks as $rank) {
            ?>
                            <option value="<?php echo $rank->getId(); ?>" <?php echo ($rank->getId() == $promotionRankId) ? ("selected ") : (""); ?>><?php echo $rank->getName(); ?></option>
            <?php } ?>
                    </select>
        </label>

        <input class="button" type="submit" value="Modify Mission">
        <?php } ?>
      </form>
    <?php } ?>
    <?php

} else {
    include("not_allowed.php");
}
?>
