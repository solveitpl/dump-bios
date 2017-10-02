<?php
// jeśli niezalogowany
if ((!IsAuth()) || (!isset($_POST)))
{
	StrangeEvent("Unauthorized access", "DOWNLOADFILE_PAGE", array($User, $_SESSION, $_SERVER));
	_die("Unauthorized access");
}


// jeśli błędny znacznik czasu
if (isset($_POST['key']))
{
	if (!CheckMarker($_POST['key']))
	{
		StrangeEvent("KEY_EXPIRED", "DOWNLOADFILE_PAGE");
		_die("Please reload website");
	}
}
else
{
	_die("Nieprawidłowy link");
}
$FileID = intval($_POST['FileID']);

$File = oDFile::withID($FileID);

$PointsCost = $User->Points->CheckCredit($File->PointsCost());

?>
<div class="DP-header">
    <div class="DPTitle">Download file</div>
</div>
<div class="DPcontainer">

	<div class="DPFileName"><?= GetFileIcon($File->GetRealFileName()) ?><?= $File->FileDesc() ?></div>
<?php if ($User->Points->CheckCredit($File->PointsCost())==NOT_ENOUGH_POINTS) {
	?>
	<div class="DPFileCosts">
			This post cost <?= $Post->PointsCost ?>pkt. Unfortunately, you dont have enough points for this action :(
			But you can earn points!  Earn points by:
			<ul>
				<li>Upload your own file</li>
				<li>Writing at forum</li>
				<li>Or with support us !</li>	
			</ul>
			<div style="text-align: center">
				<a href="<?= BDIR ?>Donate">
					<img alt="Donate" class="DPDonateIcon" src="<?= BDIR ?>images/donate.png">
				</a>
			</div>
		</div>
	<?php 
}
else
{
	if ((!$File->InUserStock()) && !($User->CheckID($File->UploaderID()))) // jeśli plik nie został jeszcze "kupiony" przez użytkownika i nie jest plikiem uploadowanym przez użytkownika
	{
	
		?>	
		<div class="DPFileCosts">
			Plik kosztuje: <?= $File->PointsCost(); ?>pkt<br>
			Z Twojego konta zostanie pobrane:
			<ul>
				<li>Z codziennego przydziału: <?= $PointsCost['Regular'] ?>pkt</li>
				<li>Z posiadanych punktów:    <?= $PointsCost['Points'] ?>pkt</li>
			</ul>
		</div>
	<?php } ?>
<?php if (!defined("DOWNLOAD_DISABLED"))
	{?>	
	<div class="DPBtn">
		<form method="POST" action="<?= BDIR ?>GetFile">
			<input type="hidden" name="key" value="<?= Encrypt(time(NULL)) ?>">
			<input type="hidden" name="key2" value="<?= Encrypt($File->GetRealFileName()) ?>">
			<input type="hidden" name="FileID" value="<?= $FileID ?>">
			
			<input type="submit" class="DPGetFile" value="DOWNLOAD" >
		</form>
        <a href="<?= BDIR ?>downloads">
        <input type="submit" class="DPGetFile" value="BACK">
        </a>
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