<?php
?>

<div class="debug_content">
	
	<div class="debug_list"><?php 
	//print_r($Debug);
	//while( list($key, $value) = each($Debug) )
	for ($i=count($Debug)-1;$i>0; $i--)
	{
		$value = $Debug[$i];
		echo "<div class='debug_entry'><span>Czas: ".$value->Time.", ".$value->Source."</span><br>".$value->Msg."</div>";
	}
	
	?></div>
	<input id="CloseDebugContent" type='button' value="Zamknij">
</div>