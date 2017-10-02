<div class="menu-level-container">
<?php
$sql = DBquery("SELECT * FROM Categories  
				LEFT JOIN (SELECT COUNT(*) AS subQuan, ParentID FROM Categories GROUP BY ParentID) AS t1 
				ON Categories.ID=t1.ParentID 
				WHERE Categories.ParentID=0");
?>
<div class="hide-scroll">
<div class="menu_level" level=0>
	<div class='level_title'>LEVEL 0</div>
	<ul class='menu_items' level=0>
	<?php 
	while($row=DBarray($sql)){
		?>
		<li class="menu_category" cat_id="<?= $row['Id'] ?>">
			<div class="menu_title"><?= $row['Name']?></div>
			<div class="menu_subtitle">
			<?php 
				$subQuan = $row['subQuan']/1;
				echo $subQuan." ";
				if (($subQuan)==1) echo 'ITEM';
				elseif (($subQuan < 5)&&($subQuan > 0)) echo "ITEMS";
				else echo "ITEMS";
			?>			
			</div>
		</li>
		<?php 
	}
	?>
	</ul>
</div>
</div>

    <div class="hide-scroll">
<div class="menu_level" level=1>
	<div class='level_title'>LEVEL 1</div>
	<ul class='menu_items' level=1></ul>
</div>
    </div>


    <div class="hide-scroll">
<div class="menu_level" level=2>
	<div class='level_title'>LEVEL 2</div>
	<ul class='menu_items' level=2></ul>
</div>
    </div>

    <div class="hide-scroll">
<div class="menu_level" level=3>
	<div class='level_title'>LEVEL 3</div>
	<ul class='menu_items'  level=3></ul>
</div>
    </div>

    <div class="hide-scroll">
<div class="menu_level" level=4>
	<div class='level_title'>LEVEL 4</div>
	<ul class='menu_items' level=4></ul>
</div>
    </div>
<!-- 
<div class="menu_level" level=5>
	<div class='level_title'>LEVEL 5</div>
	<ul class='menu_items' level=5></ul>
</div>
 -->
    <div class="hide-scroll">
<div class='menu_level' id='tmpTable' level='-1'>

	<ul class='menu_items' level=-1></ul>
</div>
    </div>
    <div class="hide-scroll">
<div class='menu_level' id='trashTable' level='-1'>
	<img class="menu_trash_icon" src="<?= IMAGES ?>trash_can2.png">
	<ul class='menu_items' level=-2></ul>
	
</div>
    </div>



</div>