<?php
?>
<style>
.DieScreen{
	width: 100%;
	text-align: center;

}

.DieScreen .header {
	font-size: 26px;
	font-weight: bold;
	margin: 30px 0 30px 0;
    font-family: 'PixelMix';


}

.ImageContainer {
	
}

.DieScreen .GetBack #GoBack {
    background-color: #1e1e1e;
    color: white;
    font-size: 20px;
    font-weight: bold;
    margin-left: 10px;
    width: 300px;
    height: 70px;
    margin-top: 30px;
    border: 1px solid #1e1e1e;
    cursor: pointer;
}
.DieScreen .GetBack #GoBack:hover {
    color: #6fd8d4;
    border: 1px solid #6fd8d4;
}

</style>

<!--<script>
$(window).ready(function(){
	setInterval(function(){
		$('#GoBack').effect( "shake", { times: 3}, "slow" );
	}, 3000);
});
</script>-->

<div class="DieScreen">
	<div class='header'>Too bad... It's not gonna make it...</div>
	<div class='ImageContainer'><img src="<?= IMAGES ?>NothingToDoHere.gif"></div>
	<div class='GetBack'><input id="GoBack" type=button action="Navigate" arg="" value="BACK TO MAIN PAGE"></div>
</div>