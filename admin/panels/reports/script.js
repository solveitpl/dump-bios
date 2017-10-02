$(window).ready(function(){
	
	$('.RepoTab').die('click');
	$('.RepoTab').live('click',function(){
		$('.Repo_tab_container').hide();
		$('.RepoTab').removeClass('active');
		$(this).addClass('active');
		$('#'+$(this).attr('tab')).delay(100).fadeIn();
		
	});
	
	$('[tab=RepoSearching]').die('click');
	$('[tab=RepoSearching]').live('click',function(){
		var ctx = $("#myChart");
		var myChart = new Chart(ctx, {
		    type: 'bar',
		   
		    data: {
		        labels: names,
		        datasets: [{
		        	backgroundColor: "rgba(26,110,150,0.7)",
		            label: 'Ilość zapytań',
		            data: values
		        }
		        ]
		        
		    },
		    options: {
		    	
		        scales: {
		            yAxes: [{
		                ticks: {
		                    beginAtZero:true
		                }
		            }]
		        }
		    }
		});

		
	});
	
	$('[tab=RepoSearching]').trigger('click');
	
	
});