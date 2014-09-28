<?php 
		session_start();
		include("functions.php");
		if(login(1)){
			header("location:{$url_root}/index.php");		
		}
		else{
			header("location:{$url_root}/index.php");		
		}
		
?>	
