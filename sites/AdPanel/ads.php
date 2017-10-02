<?php

if (isset($_POST['DelAd'])){
	$marker = Decrypt($_POST['marker']);
	$data = explode('|', $marker);
	
	if (intval($data[0])!=0 && CheckMarker($data[1],FALSE))
	{
		$WHERE_LINE = '';
		if (!IsAdmin()) $WHERE_LINE = 'AND Advertiser='.$User->ID;
		$sql = DBquery("DELETE FROM SiteAds WHERE ID=".$data[0]." $WHERE_LINE");
		if ($sql!=FALSE) AddToMsgList("Usunięto reklamę", "INFO");
		else AddToMsgList("Błąd przy usuwaniu reklamy", "BAD");
	}
}

GetAd(100, 500);

?>
<div class='AdsToolbar'>
	<div>
		<form method="post" action="<?= BDIR."AdsPanel/Edit/New"?>">
			
			<img class="toolbar_img" id="NewAdBtn" src="<?= BDIR ?>images/add.png" alt="Add ads...">
		</form>
	</div>
	
	<div class="HeaderTitle">Ads</div>
</div>

<div class="ads_list">
	<table>
		<thead>
				<tr>
				<th>Picture</th>
				<th>Create</th>
				<th>Limited by</th>
				<th class='short'>Views</th>
				<th class='short'>Link click</th>
				<?php if (IsAdmin()) {?><th>User</th> <?php }?>
				<th>Link</th>
				<th>Option</th>
				 
				</tr>
		</thead>
		<tbody>
		<?php
		 
		if (IsAdmin())	$WHERE_LINE = '1';
		else			$WHERE_LINE = 'Advertiser='.$User->ID();
		
		$sql = DBquery("SELECT * FROM SiteAds WHERE $WHERE_LINE");
		while($row=DBarray($sql)){
		
			$Ad = new oAd($row);
			?>
			    <tr user_id='<?= $Ad->ID ?>'>
			      <td><img class='ad_img' src="<?= BDIR.$Ad->ImagePath ?>"></td>
			      <td><?= date("Y-m-d H:i:s",$Ad->AddTime) ?></td>
			      <td>
			      	<?php
			      	switch ($Ad->Mode){
			      		case DISPLAY_LIMITED:
			      			echo $Ad->DisplayLimit." views";
			      			break;
			      		case TIME_LIMITED:
			      			echo date("Y-m-d H:i:s",$Ad->ExpiredTime);
			      			break;
			      		case MONTHLY_SUB:
			      			echo "Subscription";
			      			break;
			      	}
			      	
			      if ($Ad->isExpired()) echo "<img class='expired_icon' alt='Ad expired' src='".IMAGES."warning2.png'>";
			      
			      	?>
			      </td>
			      <td><?= $Ad->Displayed; ?></td>
			      <td><?= $Ad->Clicked ?></td>
			      <?php if (IsAdmin()) {?><td><a href="<?= BDIR."member/".$Ad->Advertiser->UserNick() ?>"><?= $Ad->Advertiser->UserNick() ?></a></td> <?php }?>
			      <td><a href="<?= $Ad->Link ?>" target="_blank"><?= $Ad->Link ?></a></td>
			      <td>
			      	<input item_id="<?= $Ad->ID ?>" type="button" class="EditAdBtn" value="Edit">
			      	<form method="post" class="DelForm" action="">
			      		<input type="hidden" name="marker" value="<?= Encrypt($Ad->ID."|".time(null)) ?>">
			      		<input item_id="<?= $Ad->ID ?>" name="DelAd" type="submit" class="DelAdBtn" value="Delete">
			      	</form>
			      </td>
			      
			    </tr>
			   <?php 
			  }
			   ?>
		</tbody>
	</table>

</div>