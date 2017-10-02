<?php

/*
 * Pobieramy info o ostatnio dodanych materiałach
 * 
 */

if (isset($_POST['LAST_ADDED_CONTENT'])){
	require_once 'include.php';
	if (isset($_POST['LAST_ADDED_CONTENT']))
	{
		switch($_POST['LAST_ADDED_CONTENT']){
			case 'image':	$LastAddCategorySQL="WHERE DIVISION='IMAGES'"; break;
			case 'bios':	$LastAddCategorySQL="WHERE DIVISION='BIOS'"; break;
			case 'kbc':		$LastAddCategorySQL="WHERE DIVISION='KBC-EC'"; break;
			case 'sch':		$LastAddCategorySQL="WHERE DIVISION='SCHEMATICS'"; break;
			case 'boa':		$LastAddCategorySQL="WHERE DIVISION='BOARDVIEW'"; break;
			case 'sol':		$LastAddCategorySQL="WHERE DIVISION='ART' AND Category <> 0"; break;
			case 'tot':		$LastAddCategorySQL="WHERE DIVISION='ART' AND Category = 0"; break;
		}
	}
	else
		$LastAddCategorySQL = '';
	
		$items = array();
		
		$sql = DBquery("SELECT * FROM
				(
				SELECT Articles.ID, Title, Category, AddDateTime, 'ART' AS DIVISION, link,
				t1.Name AS Name0, t2.Name AS Name1, t3.Name AS Name2, t4.Name AS Name3, t5.Name AS Name4 FROM Articles
				LEFT JOIN Categories as t1 ON Category = t1.Id
				LEFT JOIN Categories as t2 ON t1.ParentID = t2.Id
				LEFT JOIN Categories as t3 ON t2.ParentID = t3.Id
				LEFT JOIN Categories as t4 ON t3.ParentID = t4.Id
				LEFT JOIN Categories as t5 ON t4.ParentID = t5.Id
				UNION ALL
					
				SELECT BrowserPosts.ID, Title, Category, FROM_UNIXTIME(SendTime) AS AddDateTime, MODULE AS DIVISION, BrowserPosts.ID as link,
				t1.Name AS Name0, t2.Name AS Name1, t3.Name AS Name2, t4.Name AS Name3, t5.Name AS Name4 FROM BrowserPosts
				LEFT JOIN Categories as t1 ON Category = t1.Id
				LEFT JOIN Categories as t2 ON t1.ParentID = t2.Id
				LEFT JOIN Categories as t3 ON t2.ParentID = t3.Id
				LEFT JOIN Categories as t4 ON t3.ParentID = t4.Id
				LEFT JOIN Categories as t5 ON t4.ParentID = t5.Id
				) AS TmpView
				$LastAddCategorySQL
				ORDER BY AddDateTime DESC
				");
		
		while ($row=dbarray($sql)){
			$Item = new oLastAdded($row);
			array_push($items, $Item);
		}
		
		die(json_encode(array("result"=>"SUCCESS", "items"=>$items, "item_count"=>count($items))));

		
}


die(json_encode(array("result"=>"NO-DATA")));
