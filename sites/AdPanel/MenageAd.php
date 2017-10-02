<?php
if (isset($DATA['item']))	$Ad = oAd::withID($DATA['item']);
else						$Ad = oAd::_blank();
$file_type_string = '*.jpg, *.png, *.jpeg, *.gif';

$CanEdit = !$Ad->ID || IsAdmin();

$SIZES=  explode(';', GetSettings('ADS_SIZE_XY'));
$ADS_SIZES = array();
$SIZES_SELECT_OPT = '';
$SIZES_DIVS = '';



foreach ($SIZES as  $el){
	$temp = explode('x', $el);
	array_push($ADS_SIZES, array('label'=>$el, 'x'=>$temp[0], 'y'=>$temp[1]));

	$SIZES_DIVS.= '<div class=radio_div>'.$temp[0].'px x '.$temp[1].' px';
	if ($Ad->CheckDim($temp[0], $temp[1]))
		$SIZES_DIVS .='<img class="ok_icon_small" src="'.IMAGES.'ok.png">';
	$SIZES_DIVS .= '</div>';
	
	$SIZES_DIVS .= '';
	
}


// Zapisywanie
if (isset($_POST['SaveAd'])) {
	if (isset($_POST['ADid'])) $Ad = oAd::withID(intval($_POST['ADid']));
	
	if (isset($_POST['AdMode']))  $Ad->Mode = intval($_POST['AdMode']);
	if (isset($_POST['display_limited']))  $Ad->DisplayLimit = intval($_POST['display_limited']);
	if (isset($_POST['time_limited'])) $Ad->ExpiredTime =  strtotime($_POST['time_limited']);
	if (IsAdmin()) $Ad->Status = intval($_POST['AdStatus']);

	
	$Ad->Link = htmlspecialchars($_POST['ad_link']);
	
	
	if ($Ad->ID==0)
	{
		$sql = DBquery("INSERT INTO SiteAds(`ID`, `Description`, `DisplayLimit`, `AddTime`, `ExpiredTime`, `Mode`, `Status`, `Link`, `Advertiser`)
						VALUES(NULL, '', ".$Ad->DisplayLimit.", ".time(NULL).", ".$Ad->ExpiredTime.", ".$Ad->Mode.",0,'".$Ad->Link."', ".$User->ID." )");
		if ($sql==false)
			AddToMsgList("INTERNAL_ERROR #1","BAD");
		
		$Ad->ID = DBlastID();
	}
	else
	{
		$admin_where_line = '';
		if (IsAdmin()) $admin_where_line = ', Status='.$Ad->Status;
		if ($CanEdit)
			DBquery("UPDATE SiteAds SET ExpiredTime=".$Ad->ExpiredTime.", DisplayLimit=".$Ad->DisplayLimit.", Mode=".$Ad->Mode.", Status=".$Ad->Status.", Link='".$Ad->Link."' $admin_where_line WHERE ID=".$Ad->ID);
		else 
			DBquery("UPDATE SiteAds SET Link='".$Ad->Link."' WHERE ID=".$Ad->ID);
	}
	
	if (isset($_SESSION['upload_ad']['fileName']) &&  $_SESSION['upload_ad']['fileName']!='')
	{		
		$targetDir = 'upload/ads/'.$Ad->ID.'/';
		if (!file_exists($targetDir))
		{
			mkdir($targetDir, 0777, true);
			chmod($targetDir, 0777);
		}
		rename($_SESSION['upload_ad']['ServerFilePath'].'/'.$_SESSION['upload_ad']['fileName'], $targetDir.$_SESSION['upload_ad']['fileName']);
		$Ad->ImagePath = $targetDir.$_SESSION['upload_ad']['fileName'];
		$Ad->GetImgDim();
		DBquery("UPDATE SiteAds SET ImagePath='".$Ad->ImagePath."', Dimensions='".$Ad->ImageSX."x".$Ad->ImageSY."' WHERE ID=".$Ad->ID);
		unset($_SESSION['upload_ad']);
		$_SESSION['upload_ad'] = array();
		
		
	}
}
else
{
	unset($_SESSION['upload_ad']);
	$_SESSION['upload_ad'] = array();
}

?>

<div class='AdsToolbar'>
	<div>
		<form method="post" action="<?= BDIR."AdsPanel/"?>">	
		
			<img class="toolbar_img" id="NewAdBtn" src="<?= BDIR ?>images/arrow_left.png" alt="Add ads...">
		</form>
	</div>
	
	<div class="HeaderTitle">Edit Ad</div>
</div>

<script type="text/javascript" src="<?= BDIR ?>lib/dropzone.js"></script>
<script type="text/javascript">var img_sizes = <?= json_encode($ADS_SIZES) ?></script>
<form action="" method="post" id='post_form'>
	<div class="upload_drop_zone" id="drop_zone">
		<div class="after_drop">
			<div class="file_container">
				<div class="file_list_container">
					<img id="upload_image" src="<?= $Ad->ImagePath!='' ? BDIR.$Ad->ImagePath : '' ?>"><br>
					 <div class="dz-message" data-dz-message id="drop_title">Drop image here <p><span class="dz-message" >formats: <?= $file_type_string  ?></span></div> 
				
				</div>
			
			</div>
			<div class='info_container'>
				
				
				<div class="info_form">
					<div class="title">Status</div>		
					<div class="div_input">
						<?php if (IsAdmin()) {?>
						<select id="_Status" name="AdStatus">
							<option value='<?= AD_NOT_ACTIVE ?>' <?= $Ad->Status==AD_NOT_ACTIVE ? 'selected' : '' ?>><?= $STATUS_LABEL[AD_NOT_ACTIVE] ?></option>
							<option value='<?= AD_ACTIVE ?>' <?= $Ad->Status==AD_ACTIVE ? 'selected' : '' ?>><?= $STATUS_LABEL[AD_ACTIVE] ?></option>
							<option value='<?= AD_EXPIRED ?>' <?= $Ad->Status==AD_EXPIRED ? 'selected' : '' ?>><?= $STATUS_LABEL[AD_EXPIRED] ?></option>
							<option value='<?= AD_PENDING ?>' <?= $Ad->Status==AD_PENDING ? 'selected' : '' ?>><?= $STATUS_LABEL[AD_PENDING] ?></option>
							
						</select>
						<?php }
						else
							echo $STATUS_LABEL[$Ad->Status];
					
							?>
					</div>
						
					<input type="hidden" id="ADid" name="ADid" value='<?= $Ad->ID ?>'>
					<input type="hidden" name="uniqu" value='<?= md5(rand(0,10000000)) ?>'>
						
					<input type="hidden" id="marker" name="marker" value='<?= Encrypt(time(null)) ?>'>
						
					
				</div>
				
			
			
				<div class="info_form">
					<div class="title">Mode</div>		
					<div class="div_input">
						<?php if ($CanEdit) {?>
						<select id="_Mode" name="AdMode">
							<option value='0' <?= $Ad->Mode==0 ? 'selected' : '' ?>>Display limited</option>
							<option value='1' <?= $Ad->Mode==1 ? 'selected' : '' ?>>Time limited</option>
							<option value='2' <?= $Ad->Mode==2 ? 'selected' : '' ?>>Monthly subscription</option>
						</select>
						<?php }
						else
							echo $MODE_LABEL[$Ad->Mode];
							?>
					</div>
						
					<input type="hidden" id="ADid" name="ADid" value='<?= $Ad->ID ?>'>
					<input type="hidden" name="uniqu" value='<?= md5(rand(0,10000000)) ?>'>
						
					<input type="hidden" id="marker" name="marker" value='<?= Encrypt(time(null)) ?>'>
						
					
				</div>
				
				<div class="info_form <?= $Ad->Mode!=0 ? 'hide':'' ?>" _selVisible=0>
					<div class="title">Display limited</div>
					<div class="div_input">
					<?php if ($CanEdit) {?>
							<div class="radio_div"><input type="radio" value="10000" <?= $Ad->DisplayLimit==10000 ? 'checked':'' ?> name="display_limited"> 10000 views </div>
							<div class="radio_div"><input type="radio" value="50000" <?= $Ad->DisplayLimit==50000 ? 'checked':'' ?> name="display_limited"> 50000 views </div>
							<div class="radio_div"><input type="radio"  value="100000"<?= $Ad->DisplayLimit==100000 ? 'checked':'' ?> name="display_limited"> 100000 views </div>			
					<?php }
						else
							echo $Ad->Displayed."/".$Ad->DisplayLimit." views<br>".$Ad->Clicked." clicked";
							?>
					</div>
					
				</div>
				
				<div class="info_form <?= $Ad->Mode!=1 ? 'hide':'' ?>" _selVisible=1 >
					<div class="title">Time limited</div>
					<?php if ($CanEdit) {?>
					<input type="text" name="time_limited" id="time_limited" value="<?= date("Y-m-d", $Ad->ExpiredTime) ?>">
					<?php } else{
						
						echo "<div>".date("Y-m-d", $Ad->ExpiredTime)."</div>";
							
					}?>
					
				</div>
				
				<div class="info_form <?= $Ad->Mode!=2 ? 'hide':'' ?>" _selVisible=2>
					<div class="title">Monthly subscription</div>
					29 &#8364/month
				</div>
				
				
				
				
				<div class="info_form">
					<div class="title">Sizes</div>
					<div class="div_input">
					 <?= $SIZES_DIVS ?>
					 			
					</div>
				</div>
				
				<div class="info_form">
					<div class="title">Link</div>
					<div class="div_input">
							<input type="text" name="ad_link" id="post_title_input" value="<?= $Ad->Link ?>">
					</div>		
						
				</div>
				
				
				<div class="info_form">
					<div class="title">Progress loading file</div>
					<div class="div_prg hide"></div>
					<div class="div_prg_txt hide">0%</div>
					<div class="div_button">
						<input type="submit" value="Save" name='SaveAd' id="SaveFileTitle">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="comment_box">
<?php
// Wymiana komentarzy z Adminem na temat reklamy

$Comments = new oComments(ADS, $Ad->ID);
if (IsAuth()){
	// tworzenie klasy z komenarzami
	$Comments->PropagatePOST($_POST);
	?>

	<div class='addComent'><?= $Comments->InputPanel(); ?></div>
	<?php
}
$Comments->ShowComments();
?>
</div>

<script type="text/javascript">
// przypisujemy nazwę modułu do pola menu

// inicjalizacja dropzone
var myDropzone = new Dropzone(".file_list_container", { url: "<?= BDIR ?>uploadFile"});
myDropzone.autoDiscover = false;
myDropzone.autoProcessQueue = true;
myDropzone.uploadMultiple = false;

myDropzone.options.myAwesomeDropzone = false;
myDropzone.options.previewsContainer = false;
myDropzone.options.thumbnailWidth = null;
myDropzone.options.thumbnailHeight= null;
myDropzone.options.acceptedFiles = '.jpg,.jpeg,.gif,.png';
myDropzone.accept = function(file, done) {
	var accepted = false;
	
	myDropzone.on("thumbnail", function(file,dataUrl) {
		
		img_sizes.forEach(function(arr){
			if (arr.x==file.width && arr.y==file.height) accepted = true;
		});
		
		if (!accepted) done('Unknow file dimensions');		
		else done();
		

		
	});
	
	

}        

myDropzone.on("sending", function(file, xhr, formData) {
	
	var marker = '<?= Encrypt(time(NULL)) ?>';
	formData.append('division', 'Ad');

	  
});

myDropzone.on("thumbnail", function(file,dataUrl) {	

	var accepted = false;
	img_sizes.forEach(function(arr){
		if (arr.x==file.width && arr.y==file.height) accepted = true; 
	});

	
	if (accepted) {
		$('#upload_image').fadeOut(400).delay(1000).remove();
		$('.file_list_container').html('<img alt="Avatar" style="display:none" id="upload_image" src="'+dataUrl+'">');
		$('#upload_image').fadeIn();
		$('#drop_title').css('opacity','0');
	}
	else
		ShowDialogBox("Please, upload image with specific dimensions", "BAD");
	
	
	

});


myDropzone.on("addedfile", function(file) {
	console.log(file);
});


myDropzone.on("uploadprogress", function(file,progress, bytesSent) {

	$('.div_prg').progressbar({
	    value: progress
	  });	
	$('.div_prg_txt').text(progress+'%');
});

myDropzone.on("complete", function(file) {

	//$('#user_img_prg').hide();
	});

myDropzone.on('success', function(file,response) {
		var obj = JSON.parse(response);
		$('.up_fileName[file="'+file.name+'"]').text(obj.fileName);
		$('.del_tmp_file[file="'+file.name+'"]').attr('file', obj.fileName);
		
		$('.div_prg').progressbar({
		    value: 100
		  });	
		
	});
	
$('.div_prg').progressbar({
    value: 0
  });



</script>