<?php
global $SysInfo;

$SQLSysInfo = array();
if (IsLogin())
{
	$sql = DBquery("SELECT * FROM MessagesSys WHERE UserID=".$User->ID()." ORDER BY SendTime DESC");
	
	while ($row=dbarray($sql))
	{
		array_push($SQLSysInfo, oSysInfo::WithArray($row));
	}
}

			RenderSysInfo($SQLSysInfo);
		?>

