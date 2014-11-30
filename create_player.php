<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
if ($user->isAdmin()) {
?>
<br><br>
<form method="post" action="player.php?what=create">
<table width="50%"  border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td>Forname</td>
    <td><input type="text" name="forname"></td>
  </tr>
  <tr>
    <td>Nickname</td>
    <td><input type="text" name="nickname"></td>
  </tr>
  <tr>
    <td>Use nickname instead of real name</td>
    <td><input type="radio" name="use_nickname" value="1">Yes
        <input type="radio" name="use_nickname" value="0">No
    </td>
  </tr>
  <tr>
    <td>Lastname</td>
    <td><input type="text" name="lastname"></td>
  </tr>
  <tr>
    <td>emailadress</td>
    <td><input type="text" name="emailadress"></td>
  </tr>
  <tr>
    <td>Platoon</td>
    <td><input type="text" name="platoon_id" ></td>
  </tr>
  <tr>
    <td>password</td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" value="Create Player"/></td>
</tr>
</table>
</form>
<?php }
else {
include("not_allowed.php");
}?>
