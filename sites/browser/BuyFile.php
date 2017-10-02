<?php
//AddToMsgList('Wywołanie '.$_POST['key'].' -','INFO');

// jeśli niezalogowany
if ((!IsAuth()) || (!isset($_POST)))
{
	StrangeEvent("Access denieded", "DOWNLOADFILE_PAGE");
	die("Unauthorized");
}

// jeśli błędny znacznik czasu

$PostID = intval(Decrypt($_POST['key']));
$Filename = htmlspecialchars($_POST['filename']);


$Post = oPost::withID($PostID);


$PointsCost = $User->Points->CheckCredit($Post->PointsCost);
$BTNtitle = 'Download'; // Domyślny tekst przycisku

?>

<div class="DPcontainer">
	<div class="DPTitle">FILE PURCHASE</div>
	<div class="DPFileName"><?= GetFileIcon($Post->Title).$Post->Title." (files count: ".count($Post->Files).")" ?> </div>
	
<?php if ($User->Points->CheckCredit($Post->PointsCost)==NOT_ENOUGH_POINTS) {
	?>
	<div class="DPFileCosts">
			This post cost <?= $Post->PointsCost ?>pkt. Unfortunately, you dont have enough points for this action :(
			But you can earn points!  Earn points by:
			<ul>
				<li>Upload your own file</li>
				<li>Writing at forum</li>
				<li>Or with support us !</li>	
			</ul>
			<center>
				<a href="<?= BDIR ?>Donate">
					<img alt="Donate" class="DPDonateIcon" src="<?= BDIR ?>images/donate.png">
				</a>
			</center>
		</div>
	<?php 
}
else
{
	if ((!$Post->InUserStock()) && !($User->CheckID($Post->Owner))) // jeśli plik nie został jeszcze "kupiony" przez użytkownika i nie jest plikiem uploadowanym przez użytkownika
	{
		$BTNtitle = 'PURCHASE';
		?>	
		<div class="DPFileCosts">
			Post cost: <?= $Post->PointsCost; ?>pkt<br>
			<ul>
				<li>From daily points: <?= $PointsCost['Regular'] ?>pkt</li>
				<li>From user points:    <?= $PointsCost['Points'] ?>pkt</li>
			</ul>
		</div>
	<?php } ?>
<?php if (!defined("DOWNLOAD_DISABLED"))
	{
		?>	
	<!--<div class="img_get_thumb">
	<?php /*
	if ($Filename != 'package')
	{
		$File = $Post->GetFileByName($Filename);
	*/?>
		<div class='downloaded_thumb'><img src="<?/*= BDIR.$File->ThumbDir.$File->Miniature */?>"></div>
	<?php /*}
	else {
		$File = new oPFile('package', '   ', $Post);
		$BTNtitle = 'Download package';
	}
	*/?>
		<div class='rest_of_img'>
		<?php /*
			for ($i=0; $i<count($Post->Files); $i++)
				if ($Post->Files[$i]->Filename != $File->Filename)
					echo "<img src=".BDIR.$Post->Files[$i]->ThumbDir.$Post->Files[$i]->Miniature.">";
		*/?>
		</div>
	</div>-->


	<div class="DPBtn">
		<form method="POST" action="<?= BDIR ?>GetFileDemo/?Post=<?= $Post->ID?>&File=<?= $File->Filename ?>&Mode=BUY">
			<input type="hidden" name="key" value="<?= Encrypt(time(NULL)) ?>">
			<input type="hidden" name="key2" value="<?= Encrypt($Post->Title) ?>">
			<input type="hidden" name="PostID" value="<?= $PostID ?>">
			
			<input type="submit" id="DPGetFile" value="<?= $BTNtitle ?>" >
		</form>
	</div>
<?php
	}
	else
	{
?>
	<div class="DPBtn">
	<b>Pobieranie zostało zablokowane</b><br>
	Najpierw oceń pliki które pobrałeś.<br>
	Sprawdź informacje systemowe.
	</div>
<?php 
	}
}
?>	

</div>