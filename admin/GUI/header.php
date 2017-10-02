<?php

function CreateTable($header, $content){
	$header_line = '';
	// tworzenie nagłówków
	if (is_array($header))
		foreach ($header as $row){
			$header_line .= '<tr>';
			if (is_array($row))
				foreach ($row as $col)
				{
					$header_line .= '<th>'.$col.'</th>';
				}
			$header_line .= '</tr>';
		}

	// tworzenie zawartości
	$content_line = '';
	if (is_array($content))
		foreach ($content as $row){
			$content_line .= '<tr>';
			if (is_array($row))
				foreach ($row as $col)
				{
					$content_line .= '<td>'.$col.'</td>';
				}
			$content_line .= '</tr>';
		}


	$table = "<table class='modern_table'><thead>$header_line</thead><tbody>$content_line</tbody></table>";

	return $table;



}

?>
