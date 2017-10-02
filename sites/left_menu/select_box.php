<?php
require_once 'functions.php';

if (!isset($ARG[1])) $ARG[1] = '';
$Module_name = htmlspecialchars($ARG[1]);
?>
<input type='hidden' value='<?= $Module_name ?>'  id="_ModulName">
<div class="MENU_ADD_SUBCATEGORY_DIALOG">
	<div class='HEADER'>Adding new file in category <span id="sub_category_name">None</span></div>
	<div class='INPUT_BOX'><input type="text" id="new_subcategory_input" parent_id=5></div>
	<div class='BTN_BOX'>
		<input type="button" id="new_subcategory_save" value="Zapisz">
		<input type="button" id="new_subcategory_cancel" value="Anuluj">
	</div>
	
</div>

<div class="MENU_MAIN_BAR">
	<div class="MENU_MAIN_CATEGORY_0 active" cat_id=1>NOTEBOOK</div>
	<div class="MENU_MAIN_CATEGORY_0" cat_id=2>PC</div>
	
</div>
<div class="MENU_SUBCATEGORIES">
	<div>
				<div class="MenuSwitchItem optional">
					<input type='hidden' step='-1' id='MenuSwitchCatTrigger' class='MenuSwitchCategorySel'>
					<div class='MenuSwitchTitle'>Main category:</div>
					<div class='MenuSwitchValue'>
						<SELECT id='MenuSwitch_MAIN_CAT' step=0 class='MenuSwitchCategorySel'>
						<OPTION value=1>NOTEBOOK</OPTION>
						<OPTION value=2>PC</OPTION>
						
						</SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">
					</div>
				</div>
	
				<div class="MenuSwitchItem" >
					<div class='MenuSwitchTitle'>Producer:</div>
					<div class='MenuSwitchValue'>
						<SELECT step=1 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">
					</div>
				</div>
				
				<div class="MenuSwitchItem">
					<div class='MenuSwitchTitle'>Model:</div>
					<div class='MenuSwitchValue'>
						<SELECT name="CategoryID" id="selModel" step=2 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">
					</div>
				</div>
				
				<div class="MenuSwitchItem">
					<div class='MenuSwitchTitle'>Model Name:</div>
					<div class='MenuSwitchValue'>
						<SELECT name="CategoryID" id="selModelName" step=3 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">
					</div>
				</div>
				
				<div class="MenuSwitchItem" id="MenuBoardsSelect">
					<div class='MenuSwitchTitle'>MotherBoard:</div>
					<div class='MenuSwitchValue'>
						<SELECT  name="Module" id="selBoard" step=4 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">		
					</div>
				</div>
				
				<div class="MenuSwitchItem" id="MenuReversSelect">
					<div class='MenuSwitchTitle'>Revers:</div>
					<div class='MenuSwitchValue'>
						<SELECT  name="Module" id="selRevers" step=5 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">		
					</div>
				</div>
				
				<div class="MenuSwitchItem" id="ModuleSelect">
					<div class='MenuSwitchTitle'>Module:</div>
					<div class='MenuSwitchValue'>
						<SELECT  name="Module" listning id="selModule" step=6 class='MenuSwitchCategorySel'><OPTION value=0>...</OPTION></SELECT>
						<img class="loading_img" src="<?= BDIR ?>/images/loading2.gif">		
					</div>
				</div>
	</div>

	
		<div class='menu_big_button' href="<?= BDIR ?>AddModel">
			<div>Add model</div>
		</div>
	
	

		<div class='menu_big_button'  href="<?= BDIR ?>article">
			<div>TUTOTRIALS</div>
		</div>
	
	

		<div class='menu_big_button' href="<?= BDIR ?>downloads">
			<div>SOFTWARE</div>
		</div>
	
</div>
	
