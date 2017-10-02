<?php

?>

<div class="Repo_edit_box">
	<div class="RepoTabs">
			<div class="RepoTab active" tab='RepoSearching'><div>SEARCHING CONTENT</div></div>
			<div class="RepoTab" tab='RepoTopUsers'><div>TOP USERS</div></div>
			<div class="RepoTab" tab='Repo_PHP'><div>POINTS</div></div>
			
	</div>
	
	<div class="Repo_tabs_container_box">
		<img src='<?= BDIR ?>images/loading2.gif' class='load_Repo_data'>
		<?php 
		$sql = DBquery("SELECT SUM(Quantity) AS Quan, GROUP_CONCAT(Users.Nick), KEYWORD FROM `SearchWords` 
						LEFT JOIN Users ON SearchWords.UserID=Users.ID GROUP BY KEYWORD ORDER BY Quan DESC LIMIT 10");
		$names = array();
		$values = array();
		while ($row = DBarray($sql)){
			array_push($names, $row['KEYWORD']);
			array_push($values, $row['Quan']);
				
		}
		?>
		<script type="text/javascript">
			var names = <?= json_encode($names) ?>;
			var values = <?= json_encode($values) ?>;
			
		</script>
		
		<!-- ############## SEARCHED WORDS ############## -->	
		<div style="display:block" class='Repo_tab_container' id='RepoSearching'>
		MOST SEARCHING CONTENT
				<canvas id="myChart" width="20" height="10"></canvas>
		</div>
		
		<!-- ############## TOP USER ############## -->	
		<div style="display:none" class='Repo_tab_container' id='RepoSearching'>
            MOST SEARCHING CONTENT
				<canvas id="myChart" width="20" height="10"></canvas>
		</div>
		
		
	</div>
		
		
		
		
			
		
		
</div>