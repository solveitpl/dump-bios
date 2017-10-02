<?php
//for ($i=0;$i<20;$i++)
//	DBquery("Zapanie $i");
?>
<div class='LogEntries'>
<input type="button" value="DELETE ALL" id="delAllLogs">
<input type="hidden" value="<?= Encrypt(time(NULL)) ?>" id="LogsMarker">
<div id="msg_loading_bar"><img src="<?= BDIR ?>images/loading2.gif"></div>
	<table id='LogTable'>
	  <thead>
	    <tr>
	      <th>ID</th>
	      <th>Data</th>
	      <th>Zapytania</th>
	      <th>Wynik</th>

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