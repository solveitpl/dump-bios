<?php
if (!IsAuth()) _die("You are not authorize to add a file. ");

if ( !(isset($_POST['CategoryID'])) && !(isset($_POST['module'])) ) _die('Please, try again...');

$Category = intval($_POST['CategoryID']);

$MODULE = htmlspecialchars($_POST['module']);

FindCatTree($Category,'PreparedMenu');
$table_content = '';
$post_title = '';


if (isset($_POST['SavePost'])) // jeśli kliknięto przycisk 'Zapisz'
	{
		

	// zabezpieczenie przed podwójnym wysłaniem tego samego formularza
	if (isset($_SESSION['submited'][$_POST['uniqu']])) {
		AddToMsgList("Double sending the same form !","BAD");
		exit(0);
	}
	else $_SESSION['submited'][$_POST['uniqu']] = 1;
	//print_r($_SESSION['upload_files']);
	if (!CheckMarker($_POST['marker'], TRUE, 7200)) {
		_die("No... no... no..", "ADD_BROWSER_FILE");
		StrangeEvent("Error at checking mark", "ADD_BROWSER_FILE");
	}
	
	$post_title = htmlspecialchars($_POST['post_title']);
	
	if (strlen($post_title) > 3 && count($_SESSION['upload_files']) > 0 && $Category > 0 && $MODULE != '' ) // jeśi długość nazwy jest większa od zera i ilośc plików jest większa od 0 d
	{
		$sql = DBquery("INSERT INTO BrowserPosts(`ID`, `Title`, `Category`, `MODULE`, `SendTime`, `UserID`, `Status`)
						VALUES(NULL, '$post_title', $Category, '".strtoupper($MODULE)."', ".time(NULL).", ".$User->ID().", ".POST_NEW.")");	
		
		if ($sql==false)
		{
			StrangeEvent("Error at adding file.","BROWSER_ADD_FILE");
			AddToMsgList("Internal error. Try again later","BAD");
			exit(0);
		}
		
		
		$post_id = DBlastID();
		
		// przenosimy pliku z folderu tymczasowego do docelowego
		$addr_link = FindCatLinkByID($Category);
		$targetDir = 'upload/UP_FILES/'.$addr_link.$MODULE;
		if (!file_exists($targetDir)) 
		{
			mkdir($targetDir, 0777, true);
			chmod($targetDir, 0777);
		}
		
		foreach ($_SESSION['upload_files'] as $file => $fileData) {
			$fileInfo = pathinfo($file);
			$name_suffix = '';
	
			while (file_exists($targetDir.'/'.FilenameWithSuffix($file, $name_suffix))) // jeśli plik o tej samej nazwie już istnieje w katalogu docelowym
			{
				if ($name_suffix=='') $name_suffix = 0;
				$name_suffix++;
			}
			$file_newname = FilenameWithSuffix($file, $name_suffix);
			
			// Kopiowanie plików do nowej lokalizacji 
			if (file_exists($fileData['ServerFilePath'].'/'.$file))
			{
				rename($fileData['ServerFilePath'].'/'.$file, $targetDir.'/'.$file_newname);
				$sql = DBquery("INSERT INTO BrowserFiles (`ID`, `RealFileName`, `ServerFilePath`, `PostID`)
					VALUES (NULL, '$file_newname', '$targetDir', $post_id)");
				
			}
			
		}
		
		if ($sql==false)
		{
			StrangeEvent("Error at adding file.","BROWSER_ADD_FILE");
			AddToMsgList("Internal error. Try again later","BAD");
			exit(0);
		}
		else
			AddToMsgList("Your post was added. Until accept by admin,this post will be visible only for you","INFO");
		
		$post_title = '';
		
		
	}
	else
	{
		if (strlen($post_title) < 3) AddToMsgList('Add post description');
		if (count($_SESSION['upload_files']) <= 0) AddToMsgList('No one file uploaded');
		if ($Category == 0) AddToMsgList('Bad category !');
		if ($MODULE == '') AddToMsgList('Bad module !');
	}
	
	$table_content = '';
	$i = 0;
	foreach ($_SESSION['upload_files'] as $file => $fileData) {
		if (file_exists($fileData['ServerFilePath'].'/'.$file))
			$table_content .= '<tr>
					<td>'.GetFileIcon($file,TRUE,'class="file_icon"').'</td>'.
					'<td>'.$file.'</td><td>'.round(filesize($fileData['ServerFilePath'].'/'.$file)/1024).' kB</td>'.
					'<td>-</td><td>-</td></tr>';
		$i++;
		}
	}
else
	{
	unset($_SESSION['upload_files']);
	$_SESSION['upload_files'] = array();
	}
	
// przygotowanie łańcucha z typami dozwolonych plików
$file_type_string = "*".implode(" *",explode(',', $DIV_SETTINGS[$MODULE]['file_type']));
?>
<div class="cd-breadcrumps">
    <ul class="breadcrumb">

    </ul>

</div>
		
		
<script type="text/javascript" src="<?= BDIR ?>lib/dropzone.js"></script>
<div class="upload_drop_zone" id="drop_zone">

	<div class="after_drop">
		<div class="file_container">
			<div class="file_list_container">
			
				<table class='file_list'>
				<thead>
					<tr><th>Lp.</th><th>File name</th><th>Size</th><th>Progress</th><th></th></tr>
				</thead>
				<tbody>
				<?= $table_content ?>
				</tbody>
			
				</table>
				
				 <span class="dz-message" data-dz-message id="drop_title">Drop here to upload file<p><span class="dz-message" >Allowed formats: <?= $file_type_string  ?></span></span> 
			
			</div>
		
		</div>
		<div class='info_container'>
			<div class="info_form">
				<form action="" method="post" id='post_form'>
					<div class="title">Post details</div>
					<div class="div_input">
						<input type="text" name="post_title" id="post_title_input" placeholder="TITLE" value="<?= $post_title ?>">
                        <textarea type="text" name="post_desc" id="post_desc_input" placeholder="DESCRIPTION" value="<?= $post_desc ?>"></textarea>
					</div>
					<input type="hidden" id="CategoryID" name="CategoryID" value='<?= $Category ?>'>
					<input type="hidden" id="module" name="module" value='<?= $MODULE ?>'>
					<input type="hidden" name="uniqu" value='<?= md5(rand(0,10000000)) ?>'>
					
					<input type="hidden" id="marker" name="marker" value='<?= Encrypt(time(null)) ?>'>
					<div class="div_button">
						<input type="submit" value="Save" name='SavePost' id="SaveFileTitle">

					</div>
				</form>
			</div>
			
			<div class="info_form">
				<div class="title">File Info</div>
				<div class="fileinfo"><div class="i_title">Summary size</div><div class="i_value" id="filesize">---</div></div>
				<div class="fileinfo"><div class="i_title">Module</div><div class="i_value" id="filesize"><?= strtoupper($MODULE) ?></div></div>
				
				
			</div>
			
			<div class="info_form">
				<div class="title">Progress of upload</div>
				<div class="div_prg"></div>
				<div class="div_prg_txt">0%</div>
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
// przypisujemy nazwę modułu do pola menu
$('#_ModulName').val('<?= $MODULE ?>');
// inicjalizacja dropzone
var myDropzone = new Dropzone(".file_list_container", { url: "<?= BDIR ?>uploadFile"});
myDropzone.autoDiscover = false;
myDropzone.autoProcessQueue = true;
myDropzone.uploadMultiple = true;

myDropzone.options.myAwesomeDropzone = false;
myDropzone.options.previewsContainer = false;
myDropzone.options.thumbnailWidth = null;
myDropzone.options.thumbnailHeight= null;
myDropzone.options.acceptedFiles = '<?= $DIV_SETTINGS[$MODULE]['file_type'] ?>';


//myDropzone.processQueue();

myDropzone.on("sending", function(file, xhr, formData) {
    console.log(file);
	var marker = '<?= Encrypt(time(NULL)) ?>';
	  formData.append('division', 'BrowserFile');
	  formData.append('category', '<?= $Category ?>');
	  formData.append('module', '<?= $MODULE ?>');
	  formData.append('marker', marker);
	  
	  
	  
});

myDropzone.on("addedfile", function(file) {
    if(myDropzone.files[1]){
        myDropzone.files[0] = myDropzone.files[1];
        myDropzone.files.pop();

    }
    $('.del_tmp_file').trigger('click');

    var FT = myDropzone.options.acceptedFiles.split(',');
    var file_ext = file.name.split('.').pop();
    var know_filetypes = ['exe', 'pdf', 'deb', 'raw', 'tiff', 'dwg', 'bin', 'rom'];
    var TotalSize = 0;

    if (!in_array('.' + file_ext.toLowerCase(), FT)) {
        ShowDialogBox("Bad format of file. In this part are allowed only file: *" + FT.join(" *"), "BAD");
        $("#upload_image").attr("src", '');
        return 0;
    }

    $('.dz-default.dz-message').hide();
    $('#user_img_prg').fadeIn();
    $('#filesize').text(Math.round(file.size / 1024) + ' kB');
    var dataUrl = BDIR + 'images/icon/' + file_ext.toLowerCase() + '.png';

    $('.file_list tbody').append('<tr> <td><img alt="Avatar" class="file_icon" id="upload_' + file.name +
        '" src="' + dataUrl + '"></td> <td class="up_fileName" file="' + file.name + '">' + file.name + '</td><td><span>' +
        Math.round(file.size / 1024) + ' kB</span></td>' +
        '<td><div class="prg_bar" file="' + file.name + '"></div></td>' +
        '<td><img class="del_tmp_file" file="' + file.name + '" src="' + BDIR + 'images/remove.png"></td></tr>');

    $('.prg_bar[file="' + file.name + '"]').progressbar({
        value: 0,
    });

    for (var i = 0; i < myDropzone.files.length; i++)
        TotalSize = myDropzone.files[0].size;


    $('#filesize').text(Math.round(TotalSize / 1024) + ' kB');

    if (in_array(file_ext.toLowerCase(), know_filetypes)) {
        dataUrl = BDIR + 'images/icon/' + file_ext.toLowerCase() + '.png';
        $('#upload_image').fadeOut(400).delay(1000).remove();
        $('#drop_title').hide();
    }


});


myDropzone.on("uploadprogress", function(file,progress, bytesSent) {
	$('.prg_bar[file="'+file.name+'"]').progressbar({value: Math.ceil(progress)});
	$('.prg_bar[file="'+file.name+'"] div').text(progress+'%');

	var PrgSum = 0;
	var FileCnt = 0;
	$('.prg_bar[file]').each(function(){
		FileCnt++;
		if (!isNaN(parseInt($(this).find('div').text())))
		{
			PrgSum += parseInt($(this).find('div').text());
			/*console.log($(this).attr('file')+' '+parseInt($(this).find('div').text()));*/
		}
		
	});
	
	PrgSum = PrgSum / FileCnt;
//	console.log(PrgSum);
	$('.div_prg').progressbar({value: Math.ceil(PrgSum)});
	$('.div_prg_txt').text(Math.ceil(PrgSum)+'%');
	
	//	$('#user_avatar').removeAttr("src").attr('arc',dataUrl);
});

myDropzone.on("complete", function(file) {
	$('.dz-default.dz-message').fadeIn();
	//$('#user_img_prg').hide();
	});

myDropzone.on('success', function(file,response) {
		var obj = JSON.parse(response);
		$('.up_fileName[file="'+file.name+'"]').text(obj.fileName);
		$('.del_tmp_file[file="'+file.name+'"]').attr('file', obj.fileName);

		
	});
	
$('.div_prg').progressbar({
    value: 0
  });



</script>