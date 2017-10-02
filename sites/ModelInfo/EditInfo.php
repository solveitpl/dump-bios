<?php
if (isset($_POST['marker'])) $marker = Decrypt($_POST['marker']);
else _die('Bad input data... #0');
$Input =  explode('|',$marker);
$ID = intval($Input[0]);
$time = intval($Input[1]);
$Info = oModelInfo::withCategory($ID);

FindCatTree($ID,'PreparedMenu');

if ($Info==false)
	$Info = oModelInfo::createBlank($ID);

if ($ID==0) _die("Bad input data..... #1");
if (!IsLogin()) _die('You need to login...');
if (!IsAuth()) _die('Not authorized...');
if (!CheckMarker($time,FALSE)) _die("Bad input data..... #1");
$JS_OBJ = $Info;
$JS_OBJ->User = '';



if (isset($_POST['SaveForm'])){
	$InfoProposal = new oModelInfo($_POST);
	$InfoProposal->Category = $ID;
	$InfoProposal->User = $User; 
	
	?>
	<div class="ModelInfoMsg">
	<?php 
	if (!IsMod()) // of User is typical user save data as proposition
	{
		if ($InfoProposal->SaveAsProposition())
		{
			$CatLink = FindCatLinkByID($ID);
			?>
			<span class="MsgHeader">Thanks ! Now Admin need to see this. </span>
			<span class="Msgsubtitle">Now redirecting...</span>
			<script type="text/javascript">
				setTimeout(
						function(){window.location.href = BDIR+'info/<?= $CatLink ?>'},
						2000);
			</script>
			<?php 
		}
		else
		{	
			?>
			<span class="MsgHeader">Oh no... something went wrong...</span>
			<span class="Msgsubtitle">Wait a while and try again...</span>
			<script type="text/javascript">
				setTimeout(
						function(){window.location.href = BDIR+'info/<?= $CatLink ?>'},
						2000);
			</script>
			<?php
		}
	}
	else 
	{
		$InfoProposal->Accept();
		$CatLink = FindCatLinkByID($ID);
		?>
					<span class="MsgHeader">Changes has been saved.</span>
					<span class="Msgsubtitle">Now redirecting...</span>
					<script type="text/javascript">
						setTimeout(
								function(){window.location.href = BDIR+'info/<?= $CatLink ?>'},
								2000);
					</script>
					<?php 
			
	}
	?>
	</div>
	<?php 
	
}

else {

?>
<script>
var InfoObj = <?= json_encode($JS_OBJ) ?>
// CPU manuf autocomplete
var acCPU = ["Intel", "AMD"];
var acGPU = ["Radeon", "Nvidia"];
var acRAM = ["DDR", "DDR2", "DDR3", "DDR4", "DDR5"];
var acLCDType = ["LED", "LED SLIM", "LED SLIM TOUCH"];
var acLCDSize = ["15.6","17","19"];

$(window).ready(function(){
	$('[name="CPU"]').autocomplete({source: acCPU, minLength:0}).focus(function(){     
        //Use the below line instead of triggering keydown
        $(this).autocomplete("search");
    });

	$('[name="GPUManuf"]').autocomplete({source: acGPU, minLength:0}).focus(function(){     
        //Use the below line instead of triggering keydown
        $(this).autocomplete("search");
    });

	$('[RAMfield]').autocomplete({source: acRAM, minLength:0}).focus(function(){     
        //Use the below line instead of triggering keydown
        $(this).autocomplete("search");
    });

	$('[name="DisplayType"]').autocomplete({source: acLCDType, minLength:0}).focus(function(){     
        //Use the below line instead of triggering keydown
        $(this).autocomplete("search");
    });

	$('[name="DisplaySize"]').autocomplete({source: acLCDSize, minLength:0}).focus(function(){     
        //Use the below line instead of triggering keydown
        $(this).autocomplete("search");
    });
    
    
});


</script>
<form method="post" class="EditInfo" action="<?= BDIR ?>info/edit">
			
<div class="info_topbar">
			<div class='toolbar_btn'>
					<input type="hidden" name="marker" value="<?=Encrypt($ID.'|'.time(NULL)) ?>">
					<input type="hidden" name="SaveForm" value="1">
					<img class="save_btn" src="<?= BDIR ?>images/save_icon.png">
			</div>
			
			<div class="DIVISION_NAME">INFO</div>
</div>

<fieldset>
<legend>GENERAL</legend>
	<div class="ModelInfoBlock">
		<div class="PropTitle">CPU Manufactrer</div>
		<div class="PropVal"><input type="text" name="CPU" Orivalue="<?= $Info->CPU->Manuf ?>" value="<?= $Info->CPU->Manuf ?>"></div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">CPU clock speed [GHz]</div>
		<div class="PropVal"><input type="text" name="CPUClockSpeed" Orivalue="<?= $Info->CPU->ClockSpeed ?>" value="<?= $Info->CPU->ClockSpeed ?>"></div>
	</div>

	<div class="ModelInfoBlock">
		<div class="PropTitle">RAM Type</div>
		<div class="PropVal"><input type="text" name="RAMType" RAMfield=1 Orivalue="<?= $Info->RAM->MemType ?>" value="<?= $Info->RAM->MemType ?>"></div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">RAM Ammount</div>
		<div class="PropVal"><input type="text" name="RAMAmmount" Orivalue="<?= $Info->RAM->MemAmmount ?>" value="<?= $Info->RAM->MemAmmount ?>"></div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">Display type</div>
		<div class="PropVal"><input type="text" name="DisplayType" Orivalue="<?= $Info->Disp->Type ?>" value="<?= $Info->Disp->Type ?>"></div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">Display size</div>
		<div class="PropVal"><input type="text" name="DisplaySize" Orivalue="<?= $Info->Disp->DisplaySize ?>" value="<?= $Info->Disp->DisplaySize ?>"> ''</div>
	</div>
	


</fieldset>


<fieldset>
<legend>GPU</legend>
	<div class="ModelInfoBlock">
		<div class="PropTitle">GPU Type</div>
		<div class="PropVal">
			<select name="GPUType" ID="GPUTypeSel" Orivalue="<?= $Info->GPU->Type ?>">
				<option value='<?= GPU_INTEGRATED ?>' <?= $Info->GPU->Type==GPU_INTEGRATED ? 'selected' : '' ?>><?= $GPUTypeLabel[GPU_INTEGRATED] ?></option>
				<option value='<?= GPU_DEDICATED ?>' <?= $Info->GPU->Type==GPU_DEDICATED ? 'selected' : '' ?>><?= $GPUTypeLabel[GPU_DEDICATED] ?></option>
			
			</select>
		</div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">Manufacturer</div>
		<div class="PropVal"><input type="text" name="GPUManuf" Orivalue="<?= $Info->GPU->Manuf ?>" value="<?= $Info->GPU->Manuf ?>"></div>
	</div>
	
	<div class="ModelInfoBlock">
		<div class="PropTitle">Model</div>
		<div class="PropVal"><input type="text" name="GPUModel" Orivalue="<?= $Info->GPU->Model ?>" value="<?= $Info->GPU->Model ?>"></div>
	</div>
	
	<?php if ($Info->GPU->Type==GPU_INTEGRATED) $Visible = 'display:none'; else $Visible = ''?>
	
	<div class="ModelInfoBlock" HideWhenGPUIntegrated=1 style="<?= $Visible ?>">
		<div class="PropTitle">Memory type</div>
		<div class="PropVal"><input type="text" name="GPUMemType" RAMfield=1 Orivalue="<?= $Info->GPU->MemType ?>" value="<?= $Info->GPU->MemType ?>"></div>
	</div>
	
	<div class="ModelInfoBlock" HideWhenGPUIntegrated=1 style="<?= $Visible ?>">
		<div class="PropTitle">Memory ammount [MB]</div>
		<div class="PropVal"><input type="text" name="GPUMemAmmount" Orivalue="<?= $Info->GPU->MemAmmount ?>" value="<?= $Info->GPU->MemAmmount ?>"></div>
	</div>
	
		
</fieldset>



</form>
<?php }?>