<?php

$Category = FindSelectTree($ARG);

FindCatTree($Category['Id'],'PreparedMenu');

$data = dbarray(dbquery("SELECT * FROM ModelInfo WHERE CategoryID=".$Category['Id']));

if (empty($data)){

	$Info = oModelInfo::createBlank($Category['Id']);
}
else
	$Info = new oModelInfo($data);

?>

<div class="info_topbar">
			<div class='toolbar_btn'>
				<form method="post" action="<?= BDIR ?>info/edit">
					<input type="hidden" name="marker" value="<?=Encrypt($Category['Id'].'|'.time(NULL)) ?>">
					
					<img class="edit_btn" src="<?= BDIR ?>images/edit_icon.png">
				</form>
			</div>
			
			<?php if (IsMod()) {?>
			<div class='toolbar_btn'>	
					<img class="CheckRevisions" alt="Revisions" src="<?= BDIR ?>images/revision.png">
			</div>
			<?php }?>
			<div class="DIVISION_NAME">INFO</div>
</div>

<div class="CurrentData">
	<fieldset>
	<legend>GENERAL</legend>
		<div class="ModelInfoBlock">
			<div class="PropTitle">CPU Manufactrer</div>
			<div class="PropVal"><?= $Info->CPU->Manuf ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">CPU clock speed [GHz]</div>
			<div class="PropVal"><?= $Info->CPU->ClockSpeed ?></div>
		</div>
	
		<div class="ModelInfoBlock">
			<div class="PropTitle">RAM Type</div>
			<div class="PropVal"><?= $Info->RAM->MemType ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">RAM Ammount</div>
			<div class="PropVal"><?= $Info->RAM->MemAmmount ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Display type</div>
			<div class="PropVal"><?= $Info->Disp->Type ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Display size</div>
			<div class="PropVal"><?= $Info->Disp->DisplaySize ?> ''</div>
		</div>
		
	
	
	</fieldset>
	
	
	
	
	<fieldset>
	<legend>GPU</legend>
		<div class="ModelInfoBlock">
			<div class="PropTitle">GPU Type</div>
			<div class="PropVal"><?= $GPUTypeLabel[$Info->GPU->Type] ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Manufacturer</div>
			<div class="PropVal"><?= $Info->GPU->Manuf ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Model</div>
			<div class="PropVal"><?= $Info->GPU->Model ?></div>
		</div>
		
		<?php if ($Info->GPU->Type==GPU_DEDICATED){?>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Memory type</div>
			<div class="PropVal"><?= $Info->GPU->MemType ?></div>
		</div>
		
		<div class="ModelInfoBlock">
			<div class="PropTitle">Memory ammount [MB]</div>
			<div class="PropVal"><?= $Info->GPU->MemAmmount ?></div>
		</div>
		
		<?php }?>	
	</fieldset>
</div>
<?php 
if (IsMod()) include 'Revisions.php';

?>

