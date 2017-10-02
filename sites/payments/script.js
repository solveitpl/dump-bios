$(window).ready(function(){
	
	// auto calc when Ammount field has been modificated
	$('[name="amount"]').keyup(function(){
		$('#points_for_donate').val(PointPerEur*parseFloat($(this).val()));
	});
	
});