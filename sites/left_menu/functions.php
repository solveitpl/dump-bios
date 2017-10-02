<?php

define('MENU_DEPTH',6);

/*
 * Elementy najniższego stopnia menu
 * Standardowe
 * 
 */
global $entry_content;
$entry_content = array(
        // klient zrezygnowal z "INFO"
		// array("Name"=>"INFO",		"Division"=>"info",		"Page"=>"info/", 				"Count"=>0),
		array("Name"=>"IMAGES",		"Division"=>"browser",	"Page"=>"browser/images/", 		"Count"=>0),
		array("Name"=>"BIOS",		"Division"=>"browser",	"Page"=>"browser/bios/",		"Count"=>0),
		array("Name"=>"KBC-EC",		"Division"=>"browser",	"Page"=>"browser/KBC-EC/",		"Count"=>0),
		array("Name"=>"SCHEMATICS", "Division"=>"browser",	"Page"=>"browser/schematics/",	"Count"=>0),
		array("Name"=>"BOARDVIEW",  "Division"=>"images",	"Page"=>"browser/boardview/", "Count"=>0),
		array("Name"=>"SOLUTIONS",	"Division"=>"article",	"Page"=>"Article/solutions/",	"Count"=>0)

			/*array("Name"=>"SOFTWARE",	"Division"=>"downloads","Page"=>"downloads/Software/", "Count"=>0),
			array("Name"=>"OTHERS",		"Division"=>"article",	"Page"=>"Article/others/", "Count"=>0)*/
);

/*
 * Znajdowanie ścieżki menu
 */
function FindCatTree($ID,$var_name='expand_menu')
{
	$query_counter=0; // licznik zapytań rekursywnych
	$menu_expand_steps = array();
	$current = $ID;
	$lowestMenuName = '';
	$menu_entries = array();
	$menu_ids = array($ID);
	$level=0;
	$i=0;
	
	// Wypracowanie najniższego menu
	$sql = dbarray(DBquery("SELECT Categories.*, c1.ParentID AS Grandpa FROM Categories LEFT JOIN Categories AS c1 ON Categories.ParentID=c1.Id WHERE Categories.Id=$ID"));
	
	$ParentId_2 = $sql['ParentID'];
	// ustalanie poziomu menu
	do {
		$req = DBarray(DBquery("SELECT Id, ParentID FROM Categories WHERE Id=$ParentId_2"));
		$level++;
		$ParentId_2 = $req['ParentID'];
		array_insert($menu_ids, 0, $req['Id']);
	} while (($ParentId_2)&&($level<7));
	
	
	// jeśli to nagłębszy poziom menu
	if ($level==5){
		$details_raw = GetMenuDetails($ID);
		$details = array();
		for ($i=0; $i<count($details_raw); $i++)
			array_push($details, array(
					'name' => $details_raw[$i]['Name'],
					'link' => BDIR.$details_raw[$i]['Page'].$details_raw[$i]['link']
			));
		$menu_entries = $details;
			
	}
	else // jeśli to inny poziom menu
	{
		$menu_entries = array();
		$req = DBquery("SELECT * FROM Categories WHERE ParentID=".$sql['Id']);
		while ($row = DBarray($req)) {
			//	print_r($row);
			array_push($menu_entries, array(
					'name' => $row['Name'],
					'id' => 'cat_'.$row['Id'],
					'icon' => 'fa fa-desktop',
					'className' => 'sorting',
					'link' => '#',
					'items' => array(
							(object)array(
									'title' => $row['Name'],
									'icon'=> 'fa fa-desktop',
									'items' => array(
									(object)array(
										'name'=> $row['Name'],
										'icon'=> 'fa fa-phone-square',
										'link'=> '#'
										)
									
									)
							)
							
							)
						)
					);
			
		}
	}
	
	
	$lowestMenuName = $sql['Name'];


	
	
	// Przygotowanie do rekursywnego pobierania wyzszego menu
	$current = $sql['Id'];
	$ParentId = $sql['ParentID'];



	// generowanie wyższych pozycji
	// echo "<pre>".json_encode($menu_entries, JSON_PRETTY_PRINT)."</pre><br>";
	do
	{
		$sql = DBquery("SELECT Categories.*, c1.ParentID AS Grandpa FROM Categories LEFT JOIN Categories AS c1 ON Categories.ParentID=c1.Id WHERE Categories.ParentID=$ParentId");
		$menu_entries_temp = array();
		
		while ($row = DBarray($sql)) {	
		//	print_r($row);
			$menu_entry = array(
					'name' => $row['Name'],
					'id' => 'cat_'.$row['Id'],
					'icon' => 'fa fa-desktop',
					'link' => '#'
					);
			// echo "<br>to ja ! $current $ParentId ".$row['Id']."<br>";
			
			if (($row['Id']==$current)) // jeśli ten item to nasz item
			{	
				$menu_entry['items'] = array(array(
						'title' => $row['Name'],
						'link' => '#',
						'items'=>$menu_entries,
						'level'=>$level
						));
				$current = $row['ParentID'];
				$ParentId = $row['Grandpa'];
								
				
			}
			
			array_push($menu_entries_temp, $menu_entry);
			
		}

		
		$menu_entries = $menu_entries_temp;


		$query_counter++;
		
	}while (($ParentId)&&($query_counter<7));
	

	 	$menu_entries = array(
	 		'ParentID' => "catid_".$current,
	 		'items' => $menu_entries
	 	);
    $len = count($menu_ids);
    echo '<div class="hidden-cat-tree">[';
foreach ($menu_ids as $key=>$menu_id) {
    if($key == $len-1) echo '"'.$menu_id.'"';
    else echo '"'.$menu_id.'",';

}

echo ']</div>';




	echo "<script> var $var_name = ".json_encode($menu_entries)."; var MenuDepthCurr = ".intval($level)."; var lowestMenuName='$lowestMenuName';</script>";
	return $menu_ids;
}

/*
 * Znajdowanie ścieżki dla pól wyboru menu
 */
function FindSelectTree(array $arr, $arr_offset=2,$var_name='select_menu')
{

	$argument_offset=$arr_offset;
	$CatTree = array();
	$parent_line = '';
	
	do {
		$sql = DBquery("SELECT Id,Name, ParentID FROM Categories WHERE Name='".htmlspecialchars($arr[$argument_offset])."' $parent_line");
		if ($sql->num_rows !=0)
			{
			$row = DBarray($sql);
			array_push($CatTree, $row['Id']);
			$parent_line = "AND ParentID='".$row['Id']."'";
			}
		$argument_offset++;	
	} while (($sql->num_rows != 0) && ($argument_offset<(6+$arr_offset)));
	
    /*echo '<div class="hidden-cat-tree">'.json_encode($CatTree).'</div>';*/
	echo "<script> var $var_name = ".json_encode($CatTree)."</script>";
	
	return $row; // zwracamy tablicę docelowej kategorii
}

/*
 * Znajdowadnie ścieki dla pól wybory po ID najniższej kategorii
 */

/*
 * Znajdowanie ścieżki menu dla pola select
 */
function FindSelectTreeByID($ID,$var_name='select_menu',$ReturnLink=FALSE)
{
	$query_counter=0; // licznik zapytań rekursywnych
	$CatTree = array();

	$current = $ID;
	$link = '';
	//	array_push($menu_expand_steps, $current);
	do
	{
		$sql = dbarray(DBquery("SELECT Id, Name, ParentID FROM Categories WHERE Id=$current"));
		//array_push($menu_expand_steps, $sql['Id']);
		array_insert($CatTree, 0, $sql['Id']);
		$query_counter++;
		$current = $sql['ParentID'];
		$link = $sql['Name'].'/'.$link;
	}while (($current)&&($query_counter<10));

	
	if ($ReturnLink) return $link;
	else {
		echo "<script> var $var_name = ".json_encode($CatTree)."</script>";
		return $CatTree;
	}
}

function FindCatLinkByID($ID)
{
	$query_counter=0; // licznik zapytań rekursywnych
	$CatTree = array();
	$current = $ID;
	$link = '';
	//	array_push($menu_expand_steps, $current);
	do
	{
		$sql = dbarray(DBquery("SELECT Id, Name, ParentID FROM Categories WHERE Id=$current"));
		$query_counter++;
		$current = $sql['ParentID'];
		$link = $sql['Name'].'/'.$link;
	}while (($current)&&($query_counter<10));

	return $link;

}




function GetMenuDetails($ID, $filter=''){
	global $entry_content;
	$ID = intval($ID);
	$CatLink = FindSelectTreeByID($ID,'',TRUE);
	for ($i=0; $i<count($entry_content);$i++)
	{
		$entry_content[$i]['link'] = $CatLink;
	}
	
	if ($filter!='')
	{
		$temp_array=array();
		for ($i=0; $i<count($entry_content);$i++)
			if (in_array($entry_content[$i]['Division'],$_POST['filter']))
			{
				array_insert($temp_array, 0, $entry_content[$i]);
 			//	array_push($temp_array,$entry_content[$i]);
			}
		$entry_content = $temp_array;
	}
	return $entry_content;
}

// renderowanie bloku przy dodawaniu nowego modelu
function RenderField($Title="unnamed",$tip='', $field_name='unknown_field[]', $level=0, $Show='none', $NewAvaliable=TRUE, $Sel='', $Arrow = TRUE, $ONLY_NEW=FALSE){

	?>
	<div class="AddModelField" level='<?= $level ?>' style='display: <?= $Show ?>'>

	
		<div class="Title">
            <?php if ($Arrow) {?> <span class="addmodel-arrow"> >> </span><?php }?>
            <?= $Title ?><img class="loading_img" src='<?= BDIR ?>images/loading2.gif'>
         </div>
        <div class="new_model_input" <?php if ($ONLY_NEW) echo 'style="display:block"'; ?>>
			<input type="text" id="<?= $field_name ?>_input" level="<?= $level ?>" name="<?= $level ?>[new_input]">
			<span><?= $tip ?></span>
		</div>
		
		<?php 
				$ShowSelect = '';
			  if ($ONLY_NEW) // // jeśli jest opcja wybrania istniejacego elementu nową podkategorię; pokaż przycisk zapisz
				{
				$ShowSelect = 'display: none';
				?>
					<input type=submit name='AddNewModelSend' id='AddNewModelSend' value="Save" disabled='disabled'>
					<input type="hidden" value='1' id="<?= $field_name ?>_check" name="<?= $level ?>[new_check]">
					<input type="hidden" value='1' name="<?= $level ?>[select]">
					
				<?php 
				}
			  else 
			  {
			 
				?>
				<select class="AddSelect" level='<?= $level ?>' style='<?= $ShowSelect ?>' category='' <?= $Sel!='' ? 'sel="'.$Sel.'"' : '' ?> name="<?= $level?>[select]">
					<option value=-1>Choose..</option>
				</select>		
				
				<?php if ($NewAvaliable) { ?> 
					<div class="IsANewModel" style='<?= $ShowSelect ?>'>
						<div class="AddModelNewCheckBox">
							<input type="checkbox" value='1' id="<?= $field_name ?>_check" level="<?= $level ?>" name="<?= $level ?>[new_check]" />
								
						</div>
						<div>New</div>
					</div>
				
					
				<?php }
			  }
		
		
		
		 ?>

		
	
	</div>
    <div class="upload-model"></div>
	<?php 
}
?>