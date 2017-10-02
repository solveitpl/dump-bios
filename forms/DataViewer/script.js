function isObject(elem){
	if( Object.prototype.toString.call(elem) === '[object Object]' || Object.prototype.toString.call(elem) === '[object Array]' ) {
		return true;
	}
	else return false;
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

var oDataViewer = {
		
		Container: '',
		Data: [],
		HTML: '',
		
		CloseBtn:'',
		
		'Init': function(ctx, CloseBtn){
			this.Container =  typeof ctx !== 'undefined' ? ctx : ''; 
			this.CloseBtn =  typeof CloseBtn !== 'undefined' ? CloseBtn : ''; 
			
			$(CloseBtn).click(function(){
				$('.o_DataViewer_layer').hide();
			});
		},
		
		'Assign': function(data){
			this.Data = data;
			//alert('Assign');
			
		},
		
		'GenHTML': function(_data, level){
			//alert('GenHTML');
			
			var HTML = '';
			var itemClass = 'l_normal'
			var i=0;
			var level = typeof level !== 'undefined' ? level : 0;
			HTML += '<ul class="odlDataList">';
			for (key in _data){
				HTML += '<li>'+
					'<input type="checkbox" id="odl_'+level+'_'+i+'"/>';
				if (!isObject(_data[key])) itemClass = 'l_normal'; else itemClass='l_extended';	
				HTML +=	'<label for="odl_'+level+'_'+i+'" class="odlItemTitle '+itemClass+'">'+key+'</label>'+_data[key];
				if (isObject(_data[key])) HTML += this.GenHTML(_data[key],parseInt(level+1));
				HTML +=	'</li>';
				i++;
			}
				
			
			HTML += '</ul>';
			
			return HTML;
		},
		
		'Build': function(){
			this.HTML = this.GenHTML(this.Data);
			//alert(this.Container);
			$(this.Container).html(this.HTML);
			
		},
		
		'Show': function (){
			this.Build();
			$('.o_DataViewer_layer').show();
		},
		
		'FromString': function(data){
			if (!IsJsonString(data)) {
				alert('No data !');
				return 0;
			} 
			this.Data = JSON.parse(data);
			return 1;
		},

		
		
}


$(window).ready(function(){
	oDataViewer.Init('#DataViewerBody', '#CloseOdlDataView');
	

	
});