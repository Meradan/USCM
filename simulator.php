<?php
$userController = new UserController();
$user = $userController->getCurrentUser();
?>
<strong>In progress..</strong>
<br />
<br />
<?php
if ($user->getId() > 0)
{
	$characterController = new CharacterController();
	$characters = $characterController->getUserCharacters($user->getId());
	
	
}

?>