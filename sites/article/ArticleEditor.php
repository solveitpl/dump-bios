<?php

if (!IsAuth()) _die("Brak uprawnień do tej zawartości.", "ARTICLE");

switch ($ARG[2])
{
	case 'new':
		$article = new oArticle('');
		if (isset($_POST['sendArticle'])) break;
		
			$data = array(
			'Category' => intval($_POST['Category']),
			'SubcategoryTitle' => htmlspecialchars($_POST['SubCategory'])
			);
		$article->ReFill($data);

		
		break;
	default:
		// Wyciągniecie z bazdy danych o artykule
		$article_name = htmlspecialchars($ARG[2]);
		$article = oArticle::withLink($article_name);
		if (!$article->UserCanEdit())
			_die("Błąd !","ARTICLES");
}


if (isset($_POST['sendArticle']))
{
	// jeśli to nowy artykuł
	if (empty($article->GetID()))
	{
		$_POST['AuthorID'] = $User->ID();
	}
	$_POST['Category'] = Decrypt($_POST['Category']);
	$_POST['SubcategoryTitle'] = Decrypt($_POST['SubcategoryTitle']);
	$_POST['ArticleLink'] = Decrypt($_POST['ArticleLink']);
	$article->ReFill($_POST);
	$article->PutToDB();
}


//FindExpandTree($article->GetCategory());

if ($article->GetCategory()>0)
	{
	$back_link = FindSelectTreeByID($article->GetCategory(),'select_menu', TRUE);
	$back_link = BDIR."Article/".$article->GetSubCategory()."/".$back_link."item/".$article->GetLink();
	FindCatTree($article->GetCategory(),'PreparedMenu');
	
	}
else 

	$back_link = BDIR."Article/item/".$article->GetLink();

?>
<script>$('#_ModulName').val('<?= $article->GetSubCategory() ?>');</script>
<form method="POST" ACTION="">
	<input type="hidden" id="ArticleLink" name="ArticleLink" value="<?= Encrypt($article->GetLink()) ?>">
	<input type="hidden" id="CategoryPointer" name="Category" value="<?= Encrypt($article->GetCategory()) ?>">
	<input type="hidden" id="SubcategoryTitle" name="SubcategoryTitle" value="<?= Encrypt($article->GetSubCategory()) ?>">
	<div class="article_editor_container">
		<div class="article_editor_link">
			Article: <a href="<?= BDIR."Article/view/".$article->GetLink() ?>"> <?= "/Article/view/".$article->GetLink() ?></a>
			<!--w category--> <span class='subcategory_title'><?= $article->GetSubCategory() ?></span>

		</div>
		<div id="article_editor_title"><input name="Title" placeholder="ARTICLE TITLE" type="text" value="<?= strip_tags($article->GetTitle()) ?>"></div>
		<div class="article_editor"><textarea name='Content' id='article_editor'><?= $article->GetContent() ?></textarea></div>
		<div class="article_editor_btn">
            <a class="article-back" href="<?= $back_link  ?>">BACK</a>
            <input type="button" value="Review">
            <input type="submit" name="sendArticle" value="Save">
        </div>

	</div>


<script src="<?= BDIR ?>lib/ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.config.height = 300;
  CKEDITOR.config.width = 1000;
  var uploadURL = '';
  

  CKEDITOR.replace('article_editor',{
	  uploadUrl : '<?= BDIR ?>uploadFile',
	  extraPlugins : 'uploadwidget,uploadimage'
		  
			 
			  });
  
  CKEDITOR.on('instanceReady', function(e) {
	    // the real listener
	    e.editor.on( 'simpleuploads.startUpload' , function(ev) {
	        var data = ev.data;
	        // the context property provides info about where the upload is being used
	        // var context = data.context;

	        // Check if there's a dialog open:
	        var dialog = CKEDITOR.dialog.getCurrent();
	        if (dialog)  {
	            var name = dialog.getName();
	            if (name == 'image') {
	                // Get the value of our new checkbox and if it's checked add it as a GET parameter to the URL
	                var value = dialog.getValueOf('Upload', 'chkCustom');
	                if (value)
	                    ev.data.url += '&checked=on';
	            }
	        }
	        var extraFields = ev.data.extraFields || {};

	        CKEDITOR.tools.extend(extraFields, {
	            'Action' : CKEDITOR.config.action,
	            'FormID' : CKEDITOR.config.formID
	        });

	        ev.data.extraFields = extraFields;

	        // And send a new custom HTTP header in the request
	        var extraHeaders = {};
	        extraHeaders[ Core.Get('SessionName') ] = Core.Get('SessionID');
	        ev.data.extraHeaders = extraHeaders;
	    
	    });
	});
	

</script>

</form>
