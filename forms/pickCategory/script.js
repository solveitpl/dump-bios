var CatPickUp = {
		CallBack: false,
		PickUp: '',
		PickedID: 0,
		ItemID : 0,
		CallBackFun: null,
		
		'GetMenu': function(){
			alert('Pobieramy menu');
		},
		
		'Start': function(ItemID, CallBck){
			
			this.ItemID = parseInt(ItemID);
			this.CallBack = CallBck!=null;
			this.CallBackFun = CallBck;
			$(this.PickUp).fadeIn();
			this.GetPickUpMenuLevel(0,1,$('.pick_menu_items[level=1]'),null);
			
		},
		
		'Init': function(){
			this.PickUp = '.pickCategoryBox';
			$master = this;
			
			// ##### Close btn
			$(this.PickUp).find('.CatPickClose').click(function(){
				$($master.PickUp).fadeOut();
			})
			
			$(this.PickUp).find('.CatPickChoose').click(function(){
				$($master.PickUp).fadeOut();
				
				if ($master.CallBack){
					$master.CallBackFun($master.PickedID);
				}
			})
			
			// ##### Category btn
			$(this.PickUp + ' .pick_menu_category').die('click');
			$(this.PickUp + ' .pick_menu_category').live('click',function(){
				var ParentID = $(this).attr('cat_id');
				var Level = parseInt($(this).attr('menu_level'));
				var $Dest = $('.pick_menu_items[level='+Level+']');
				
				
				$master.GetPickUpMenuLevel(ParentID, Level, $Dest, $(this));
			})
			
		},
		

		'GetPickUpMenuLevel':function(ParentID,Level,$DestContainer,$this){
			
			
			$('.pick_menu_level[level]').each(function(){
				if ($(this).attr('level')>Level) $(this).find('.pick_menu_items').html('');
			});
			
			$DestContainer.html('<img class="load_img" src="'+BDIR+'images/loading2.gif">');
			
			var data_level = parseInt(Level)+1;

			if (data_level <= 7){
				var query_line = 'GET_MENU='+ParentID+'&step='+Level;
				xhr = $.post(BDIR + 'query/menu',
						query_line,
						function(data) {
							$('.load_img').hide();
							var str = '';
							var desc='';
							
							for (var i=0; i<data.length; i++)
							{
								// jeśli dana kategoria jest przechowywana w tymczasowej tabeli 
								var a_class = '';	
								$in_tmp_table = $(".menu_items[level='-1'").find('li[cat_id='+data[i].id+']');
								
								if ($in_tmp_table.length) a_class='category_in_tmp';
									
							
								
								if ((data[i].subQuan)==1) desc='category';
								else desc="categories";
								
								desc = (data[i].subQuan/1)+' ' +desc;
								if (data_level>=5) desc='';
								str+='<li class="pick_menu_category '+a_class+'" cat_id="'+data[i].id+'" menu_level="'+data_level+'">'+
										'<div class="menu_title">'+data[i].Name+'</div>'+
										'<div class="menu_subtitle">'+desc+'</div>'+
										
									'</li>';
							}
							$DestContainer.attr('parent_id',ParentID);
							$DestContainer.html(str);
							
							
							var zindex=800;
						
							
						}, 'json');
				this.PickedID = -1;
				$(this.PickUp + ' .CatPickChoose').prop('disabled',true);
				}
			else
				{
					$this.parent().children().removeClass('picked_menu');
					$this.addClass('picked_menu');
					this.PickedID = ParentID;
					$(this.PickUp + ' .CatPickChoose').prop('disabled',false);
					

				
				
				}
								
		}
		
		
		
}



$(window).ready(function(){
// ### DEMO
	CatPickUp.Init();
/*	
	CatPickUp.Start(12, function(picked_cat){
		
		xhr = $.post(BDIR + 'query/admin', {
			"CHANGE_ITEM_CATEGORY" : CatPickUp.ItemID,
			"NEW_CATEGORY" : picked_cat
			},
				function(data) {
					switch(data.result){
						case 'SUCCESS':
							ShowDialogBox("Post has been moved","GOOD");
							break;
						
						case 'ERROR':
							ShowDialogBox("Error has occurd.<br>MSG: "+data.msg,"BAD");
							break;
						
						default:
							ShowDialogBox("Some problems....","BAD");
							
					}
				
				}, 'json');

	});
*/	
});