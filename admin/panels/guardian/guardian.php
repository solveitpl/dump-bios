<?php

?>

<div class="Logs_edit_box">
	<div class="LogsTabs">
			<div class="LogsTab active" tab='Logs_Guard'><div>Portal</div></div>
			<div class="LogsTab" tab='Logs_SQL'><div>SQL</div></div>
			<div class="LogsTab" tab='Logs_PHP'><div>PHP</div></div>
			<div class="LogsTab" tab='Logs_SYS'><div>SYSTEM</div></div>
			
	</div>
	
	<div class="Logs_tabs_container_box">
		<img src='<?= BDIR ?>images/loading2.gif' class='load_Logs_data'>
		
		
		<!-- ############## Guard ############## -->	
		<div style="display:block" class='Logs_tab_container'  id='Logs_Guard'>
			<input type="button" value="DELETE ALL" id="delAllAlerts">
			<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" id="GuardMarker">
				<table id="AlertTab">
				  <thead>
				    <tr>
				      <th>lp</th>
				      <th>message</th>
				      <th>Date</th>
				      <th>Module</th>
				      <th>IP</th>
				      <th>user</th>
				      <th>data</th>
				    </tr>
				 
				  </thead>
				  <tbody>
				  <?php 
				
				  $sql = DBquery("SELECT StrangeEvents.*, Users.Nick AS UserNick FROM StrangeEvents LEFT JOIN Users on StrangeEvents.User = Users.ID ORDER BY Date DESC LIMIT 0,20");
				  $lp = 0;
				  while($row=DBarray($sql)){
				  	$lp++;
				  ?>
				    <tr>
				      <td><?= $row['ID'] ?></td>
				      <td><?= $row['MSG'] ?></td>
				      <td><?= date('Y-m-d H:i:s',$row['Date']) ?></td>
				      <td><?= $row['MODULE'] ?></td>
				      <td><?= $row['IP_ADDR'] ?></td>
				      <td><a href="<?= BDIR ?>member/<?= $row['UserNick'] ?>"><?= $row['UserNick'] ?></a></td>
				      
				    </tr>
				   <?php 
				  }
				   ?>
					
				  </tbody>
				</table>
			
		</div>
		
		<!-- ############## SQL ############## -->	
		<div class='Logs_tab_container' id='Logs_SQL'>
			<div class='LogEntries'>
				<input type="button" value="DELETE ALL" id="delAllLogs">
				<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" id="LogsMarker">
				<div id="msg_loading_bar"><img src="<?= BDIR ?>images/loading2.gif"></div>
					<table id='TLogs_SQL'>
					  <thead>
					    <tr>
					      <th>ID</th>
					      <th>Data</th>
					      <th>queries</th>
					      <th>result</th>
					      <th>DATA</th>
				
					    </tr>
					 
					  </thead>
					  <tbody>
					  <?php 
					
					  $sql = DBquery("SELECT * FROM internal_log ORDER BY date_of DESC LIMIT 0,20");
					  $lp = 0;
					  while($row=DBarray($sql)){
					  	$lp++;
					  ?>
					    <tr>
					      <td><?= $row['id'] ?></td>
					      <td><?= $row['date_of'] ?></td>
					      <td><?= $row['query'] ?></td>
					      <td><?= $row['result'] ?></td>
				
					    </tr>
					   <?php 
					  }
					  
					  DBquery("UPDATE internal_log SET new=0 WHERE new=1");
					   ?>
						
					  </tbody>
					</table>
			</div>
		</div>
		
		<!-- ############## PHP ############## -->	
		<div class='Logs_tab_container' id='Logs_PHP'>
			<div class='PHPLogs'>
				<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" id="PHPLogsMarker">
				<input type="button" value="DELETE ALL" id="delAllPHPLogs">
				<div id="msg_loading_bar"><img src="<?= BDIR ?>images/loading2.gif"></div>
					<table id='TLogs_PHP'>
					  <thead>
					    <tr>
					      <th>ID</th>
					      <th>Data</th>
					      <th>Message</th>
					      <th>FILE</th>
					      <th>LINE</th>
					      <th>CODE</th>
					      <th>UserID</th>
					   	  <th>DANE</th>
				
					    </tr>
					 
					  </thead>
					  <tbody>
					  <?php 
					
					  $sql = DBquery("SELECT PHPErrorsLogs.*, Users.Nick FROM PHPErrorsLogs LEFT JOIN Users ON UserID = Users.ID ORDER BY date_of DESC LIMIT 0,20");
					  $lp = 0;
					  while($row=DBarray($sql)){
					  	$lp++;
					  ?>
					    <tr>
					      <td><?= $row['id'] ?></td>
					      <td><?= $row['date_of'] ?></td>
					      <td><?= $row['string'] ?></td>
					      <td><?= $row['file'] ?></td>
					      <td><?= $row['line'] ?></td>
					      <td><?= $row['code'] ?></td>
					      <td><?= $row['UserID'] ?></td>
					      <td>W budowie</td>
				
					    </tr>
					   <?php 
					  }
					  
					  DBquery("UPDATE internal_log SET new=0 WHERE new=1");
					   ?>
						
					  </tbody>
					</table>
			</div>	
		</div>
		
		<!-- ############## SYSTEM ############## -->	
		<div class='Logs_tab_container' id='Logs_SYS'>
		UNDER CONSTRUCTION
		</div>
		
	</div>
		
		
		
		
			
		
		
</div>
	

