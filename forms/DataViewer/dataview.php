<?php

function RenderDataBlock(){
	$html = '';
	
	$html .= '<ul class="odlDataList">';
	$html .= '</ul>';
	
}

/*
 * <ul class="odlDataList">
				<li><input type="checkbox" id="odl_level_1"/><label for="odl_level_1" class="odlItemTitle">Element 1</label></li>
				<li><input type="checkbox" id="odl_level_2"/><label for="odl_level_2" class="odlItemTitle">Element 2</label></li>
				<li>
					<input type="checkbox" id="odl_level_3"/>
					<label for="odl_level_3" class="odlItemTitle">Element 3</label>
					<ul class="odlDataList">
							<li><input type="checkbox" id="odl_level_2_1"/><label for="odl_level_2_1" class="odlItemTitle">Element 2_1</label>0</li>
							<li><input type="checkbox" id="odl_level_2_2"/><label for="odl_level_2_2" class="odlItemTitle">Element 2_2</label>1</li>
					</ul>
					
				</li>
				<li><input type="checkbox" id="odl_level_4"/><label for="odl_level_4" class="odlItemTitle">Element 4</label>4</li>
				<li><input type="checkbox" id="odl_level_5"/><label for="odl_level_5" class="odlItemTitle">Element 5</label>jakis_tekst</li>
				
				
				
			</ul>
 */
?>

<div class="o_DataViewer_layer">
	<div class="odlWrapper"></div>
	<div class="odlDataView">
		<div class="odlHeader">DataViewer</div>
		<div id="DataViewerBody" class="odlBody">
			
		</div>
		<div class="odlFotter">
			<input type="button" id="CloseOdlDataView" value="Zamknij">
		</div>
	</div>

</div>