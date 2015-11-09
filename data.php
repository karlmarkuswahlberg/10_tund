<?php
	require_once("functions.php");
	require_once("InterestManager.class.php");
	 
	if(!isset($_SESSION['user_id'])){
		header("Location: login.php");
		exit();
		
	}

	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: login.php"); 
		exit();
	}
	
	//*********
	//**HALDUS**
	//**********
	
	$InterestManager = new InterestManager($mysqli, $_SESSION['user_id']); //sisseloginud kasutaja id.

	if(isset($_GET["new_interest"])){ //kontrollib, kas adre real on selline asi. alt formist.
		
		$add_interest_response = $InterestManager->addInterest($_GET["new_interest"]); //käivitab fn'i
		
	}
	if(isset($_GET["dropdown_interest"])){
        $add_user_interest_response = $InterestManager->addUserInterest($_GET["dropdown_interest"]);
    }
  
?>

Tere, <?=$_SESSION['user_email'];?> <a href="?logout=1">Logi välja!</a>
<br>

<h2>Lisa huviala</h2>

<?php if(isset($add_interest_response->error)): ?>

<p style="color:red;">
    <?=$add_interest_response->error->message;?>
</p>

<?php elseif(isset($add_interest_response->success)): ?>

<p style="color:green;">
    <?=$add_interest_response->success->message;?>
</p>

<?php endif; ?> 

<form>
	<input name="new_interest"><br>
	<input type="submit">
</form>

<h2>Minu huvialad</h2>
<?php if(isset($add_user_interest_response->error)): ?>

<p style="color:red;">
    <?=$add_user_interest_response->error->message;?>
</p>

<?php elseif(isset($add_user_interest_response->success)): ?>

<p style="color:green;">
    <?=$add_user_interest_response->success->message;?>
</p>

<?php endif; ?>  
<form> 
    <?=$InterestManager->createDropdown();?>
    <input value="Lisa" type="submit">
</form>

<h2>Kasutaja huvide loetelu</h2>
	<?=$InterestManager->getUserInterests();?>

