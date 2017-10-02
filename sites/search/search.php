<?php

function highlight($text, $words) {
	preg_match_all('~\w+~', $words, $m);
	if(!$m)
		return $text;
		$re = '/\b(' . implode('|', $m[0]) . ')\b/i';
		return preg_replace($re, '<span class="highlight">$0</span>', $text);
}

if (isset($_POST['SearchingWord']))
{
	$searching_word = htmlspecialchars($_POST['SearchingWord']);
	$marker = intval(Decrypt($_POST['marker']));
	if ((time(NULL) - $marker) > 36000)
		unset($searching_word);
	else
	{
		$words = explode(' ', $_POST['SearchingWord']);
		$sql = dbquery("SELECT Articles.ID, Title, Content, CONCAT('Article/view/',link) as link, UNIX_TIMESTAMP(AddDateTime) AS AddDateTime, 'Article' AS DIVISION,
								Articles.SubcategoryTitle AS Module, parent.Name AS Category
								FROM Articles
								LEFT JOIN Categories AS parent ON parent.ID=Articles.Category 
								WHERE Title LIKE '%$searching_word%' OR Content LIKE '%$searching_word%'
						UNION ALL 
						(SELECT UploadedFile.ID, FileDesc AS Title, FileDescExt AS Content, 
								CONCAT('downloads/item/', UploadedFile.ID) AS link, UploadedFile.FileUploaded AS AddDateTime, 
								'Download' AS DIVISION, Module, parent.Name AS Category FROM UploadedFile LEFT JOIN Categories AS parent ON parent.ID=UploadedFile.Category
								WHERE FileDesc LIKE '%$searching_word%' OR FileDescExt LIKE '%$searching_word%')
						UNION ALL 
						(SELECT BrowserPosts.ID, Title, '' AS Content, 
								CONCAT('browser/', Module,'/%sitem/', BrowserPosts.ID) AS link, SendTime AS AddDateTime, 
								'Browser' AS DIVISION, Module, parent.Id AS Category FROM BrowserPosts LEFT JOIN Categories AS parent ON parent.ID=BrowserPosts.Category
								WHERE Title LIKE '%$searching_word%')
				
						ORDER BY AddDateTime DESC");
		
		$values = array();
		
		if (IsLogin()) $UserID = $User->ID;
		else $UserID = 0;
		
		for ($i=0; $i<count($words); $i++)
		{
			array_push($values, "(1, '".$words[$i]."', $UserID)");
		}
		
		if (count($values)){
			$values_str = implode(',', $values);	
			DBquery("INSERT INTO SearchWords(`Quantity`, `KEYWORD`, `UserID`) VALUES $values_str ON DUPLICATE KEY UPDATE `Quantity` = `Quantity`+1");
			
		}
		
	}
	
}
?>
<div class="search-bar">
<form method="POST" action="">
	<div class="search_input">
		<div class="input_box">
			<input name="SearchingWord" type=text id="SearchingWord" value="<?php if (isset($searching_word)) echo $searching_word; ?>">
			<input type=submit class="searching_btn" value="Search">
			<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" name="marker">
		</div>
		<!--<div class="searching_hint">Write your words which you want find at Dump Bios</div>-->
	</div> 
</form>
</div>
<?php if (isset($searching_word)) {?>
		<div class="searching_result">
			<input type="hidden" value="tekst" id="SearchedText">
			<div class="searching_word"> Searching word: "<i style="font-weight: bold"><?= $searching_word ?></i>"</div>
			<div class="searching_result_count">Searching result: <?= $sql->num_rows ?></div>
				<?php while ($row=DBarray($sql)) {
					
					// w zależności od DIVISION wprowadzamy pewne zmiany:
					switch($row['DIVISION']){
						case 'Browser':
							$row['Category'] =  FindCatLinkByID($row['Category']);
							$row['link'] = preg_replace('/%s/', $row['Category'], $row['link']);
							break;
					}
					
					
					// zaznaczanie wyników w tekście
					$ContentPrepare = strip_tags(htmlspecialchars_decode($row['Content']));
					$Content = highlight($ContentPrepare, $searching_word);
					?>
					<div class="found_item">
						<div class="found_title"><a href="<?= BDIR.$row['link'] ?>"><?= highlight($row['Title'], $searching_word) ?></a></div>
						<div class="found_content">
							<div class="found_img"><img src="<?= IMAGES ?>vote_count2.png"></div>
							<div class="found_txt">
								<?= $Content ?>
							</div>
						</div>
						<div class="found_info">
							<div class="found_add_date">Add date: <?= date("Y-m-d H:i", $row['AddDateTime']) ?></div> 
							<div class="found_source"><?= $row['DIVISION']=='Article' ? 'Artykuły' : 'Download' ?></div> 
							<div class="found_category">Module: <?= strtoupper($row['Module']) ?> in category <?= $row['Category'] ?></div> 
							
						</div>
					</div>
		<?php }?>
		
		</div>

<?php }?>