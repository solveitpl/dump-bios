<?php
// pobieramy dane do wyśœietlenie
$USER_MAX_ABSENT_TIME = GetSettings("USER_MAX_ABSENT_TIME");
$queries = array(
					"SELECT COUNT(*) AS users_total FROM Users WHERE ID>0",
					"SELECT COUNT(*) AS users_inactive FROM Users WHERE ID>0 AND Status=".USER_NOT_ACTIVATED,
					"SELECT COUNT(*) AS users_absent FROM ( SELECT (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(LastLoginTime))/(3600*24) AS InactiveTime FROM `Users` WHERE ID>0) AS t WHERE InactiveTime > ".$USER_MAX_ABSENT_TIME,
					"SELECT COUNT(*) AS ModelInfoChangesPropositions FROM `ModelInfoChanges`",
					"SELECT COUNT(*) AS ArticleQuan FROM Articles",
					"SELECT COUNT(*) AS FilesQuan FROM UploadedFile",
					"SELECT COUNT(*) AS PostsQuan FROM BrowserPosts",
					"SELECT COUNT(*) AS UniqCatQuan FROM `Categories` LEFT JOIN `Categories` AS `C1` ON C1.ParentID = Categories.Id WHERE C1.Id IS NULL",
					"SELECT COUNT(*) AS NewsletterCnt FROM Users WHERE WantsNewsletter=1 AND ID>0"
);

$data = array();
// ładujemy każde zapytanie i "wkładamy" do tablicy $data
foreach ($queries as $query){
	//echo $query."<br>";
	$sql = DBarray(DBquery($query));
	$data = array_merge($data, $sql);
}

//print_r($data)
?>

<fieldset>
  <legend>USER STATS</legend>

	<div class="properties">
		<div class="prop_name">Zarejestrowanych użytkowników:</div>
		<div class="prop_value"><?= $data['users_total'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Newsletters:</div>
		<div class="prop_value"><?= $data['NewsletterCnt'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Niepotwierdzonych:</div>
		<div class="prop_value"><?= $data['users_inactive'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Oczekujących na akceptację:</div>
		<div class="prop_value"><?= $data['users_inactive'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Nieobecni od <?= $USER_MAX_ABSENT_TIME ?> dni:</div>
		<div class="prop_value"><?= $data['users_absent'] ?></div>
	</div>	

</fieldset>

<fieldset>
  <legend>TO DO</legend>

	<div class="properties">
		<div class="prop_name">Artykuły do zaakceptowania</div>
		<div class="prop_value"><?= $data['users_total'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Artykuły do usunięcia:</div>
		<div class="prop_value"><?= $data['users_inactive'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Pliki do zaakceptowania:</div>
		<div class="prop_value"><?= $data['users_absent'] ?></div>
	</div>	
	
	<div class="properties">
		<div class="prop_name">Pliki do usunięcia:</div>
		<div class="prop_value"><?= $data['users_absent'] ?></div>
	</div>	
	
	<div class="properties">
		<div class="prop_name">ModelInfo prop. zmian:</div>
		<div class="prop_value"><?= $data['ModelInfoChangesPropositions'] ?></div>
	</div>	
	
</fieldset>

<fieldset>
  <legend>BASE</legend>
	<div class="properties">
		<div class="prop_name">Artykułów</div>
		<div class="prop_value"><?= $data['ArticleQuan'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Plików:</div>
		<div class="prop_value"><?= $data['FilesQuan'] ?></div>
	</div>
	
	<div class="properties">
		<div class="prop_name">Postów:</div>
		<div class="prop_value"><?= $data['PostsQuan'] ?></div>
	</div>
	
	
	<div class="properties">
		<div class="prop_name">Unikalnych kategorii:</div>
		<div class="prop_value"><?= $data['UniqCatQuan'] ?></div>
	</div>	
	
	<div class="properties">
		<div class="prop_name">Odwiedzin:</div>
		<div class="prop_value"><?= "---" ?></div>
	</div>	
</fieldset>

