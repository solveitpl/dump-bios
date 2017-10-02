<?php
if (!IsMod()) die();
?>
<style>
<?php include 'Revisions.css'; ?>
</style>
<script type="text/javascript">
<!-- Skrypt do obługi danych rewizji
//-->
<?php include 'Revisions.js'; ?>

</script>
<form id='rev_list' method="post">
<div class="RevisionsBlock">
<input type="button" value="Delete" id="DeleteChecked">
<input type="button" value="Delete all" id="DeleteAllRev">
<input type="button" value="Back to main" id="CloseRevList">
<input type="hidden" name="marker" value="<?= Encrypt($Info->Category."|".time(NULL))  ?>">
<input type="hidden" name="CategoryID" value="<?= $Info->Category?>">
<input type="hidden" id="marker" value="<?= Encrypt($Info->Category."|".time(NULL)) ?>">

	<div class="rev_list">
		
			<table>
			  <thead>
				<tr class='smooth search_row'>
			      <th colspan=16>Actual data</th>
			    </tr>
			    
			    <tr>
			      <th class="BtnCell"></th>
			      <th>CPU Manuf.</th>
			      <th>CLK Speed [GHz]</th>
			      <th>RAM Type</th>
			      <th>RAM Amm [MB]</th>
			      <th>Disp. type</th>
			      <th>Disp. size ['']</th>
			      <th>GPU Type</th>
			      <th>GPU Manuf</th>
			      <th>GPU Model</th>
			      <th>GPU Ram Type</th>
			      <th>GPU Ram Amm. [MB]</th>
			      <th>User</th>
			      <th>Change time</th>
			      <th>Action</th>
			    </tr>
			 
			 <tr class='smooth search_row'>
			      <th class="BtnCell">Origin</th>
			      <th><?= $Info->CPU->Manuf ?></th>
			      <th><?= $Info->CPU->ClockSpeed ?></th>
			      <th><?= $Info->RAM->MemType ?></th>
			      <th><?= $Info->RAM->MemAmmount ?></th>
			      <th><?= $Info->Disp->Type ?></th>
			      <th><?= $Info->Disp->DisplaySize ?></th>
			      <th><?= $GPUTypeLabel[$Info->GPU->Type] ?></th>
			      <th><?= $Info->GPU->Manuf ?></th>
			      <th><?= $Info->GPU->Model ?></th>
			      <th><?= $Info->GPU->MemType ?></th>
			      <th><?= $Info->GPU->MemAmmount ?></th>
			      <th colspan=3>INFO</th>
			   </tr>
			    
			  </thead>
			  <tbody>
			  <?php 
			
			  $sql = DBquery("SELECT  ModelInfoChanges.*, Users.ID AS UserID, Users.Nick as UserName FROM ModelInfoChanges INNER JOIN Users ON Users.ID=ModelInfoChanges.UserID
			  		WHERE CategoryID = ".intval($Info->Category)." ORDER BY ChangeTime DESC");
			  while($row=DBarray($sql)){
	
			  $rev = new oModelInfo($row)
			  ?>
			    <tr rev_id='<?= $rev->ID ?>'>
			      <td class="BtnCell"><input type="checkbox" value="<?= $rev->ID ?>" name="EntriesCheck[]"></td>
			      <td <?= $rev->CPU->Manuf!=$Info->CPU->Manuf ? 'class="DiffrentVal"' : '' ?>><?= $rev->CPU->Manuf; ?></td>
			      <td <?= $rev->CPU->ClockSpeed!=$Info->CPU->ClockSpeed ? 'class="DiffrentVal"' : '' ?>><?= $rev->CPU->ClockSpeed; ?></td>
			      <td <?= $rev->RAM->MemType!=$Info->RAM->MemType ? 'class="DiffrentVal"' : '' ?>><?= $rev->RAM->MemType ?></td>
			      <td <?= $rev->RAM->MemAmmount!=$Info->RAM->MemAmmount ? 'class="DiffrentVal"' : '' ?>><?= $rev->RAM->MemAmmount ?></td>
			      <td <?= $rev->Disp->Type!=$Info->Disp->Type ? 'class="DiffrentVal"' : '' ?>><?= $rev->Disp->Type ?></td>
			      <td <?= $rev->Disp->DisplaySize!=$Info->Disp->DisplaySize ? 'class="DiffrentVal"' : '' ?>><?= $rev->Disp->DisplaySize ?></td>
			      <td <?= $rev->GPU->Type!=$Info->GPU->Type ? 'class="DiffrentVal"' : '' ?>><?= $GPUTypeLabel[$rev->GPU->Type] ?></td>
			      <td <?= $rev->GPU->Manuf!=$Info->GPU->Manuf ? 'class="DiffrentVal"' : '' ?>><?= $rev->GPU->Manuf ?></td>
			      <td <?= $rev->GPU->Model!=$Info->GPU->Model ? 'class="DiffrentVal"' : '' ?>><?= $rev->GPU->Model ?></td>
			      <td <?= $rev->GPU->MemType!=$Info->GPU->MemType ? 'class="DiffrentVal"' : '' ?>><?= $rev->GPU->MemType ?></td>
			      <td <?= $rev->GPU->MemAmmount!=$Info->GPU->MemAmmount ? 'class="DiffrentVal"' : '' ?>><?= $rev->GPU->MemAmmount ?></td>
			      <td><?= $rev->User->UserNick() ?></td>
			      <td><?= date("Y-m-d H-i-s",$rev->ChangeTime) ?></td>
			      <td class="BtnCell">
			      		<img class="rev_accept_btn RevActBtn" src="<?= BDIR ?>images/ok.png">
			      </td>
			      
			    </tr>
			   <?php 
			  }
			   ?>
				
			  </tbody>
			</table>
		
	</div>

</div>

</form>