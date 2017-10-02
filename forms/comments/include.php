<?php
// Definiowanie modułów
define('ARTICLE', 'ARTICLE');
define('ADS', 'ADS');



class oComments{
	private $ID;
	private $Module;
	
	
	function __construct($MODULE, $ID){
		$this->ID = $ID;
		$this->Module =$MODULE;
		
		if ((intval($this->ID)==0)||($this->Module=='')) 
		{
			AddToLog("COMMENTS", "Niepoprawna inicjalizacja klasy");
			unset($this);
			return;
		}
		
		AddToLog("COMMENTS", "Inicjalizacja komentarzy dla $this->Module o ID $this->ID");
	}
	
	function InputPanel(){
		?>
		<form method="post" action="">
			<input type="hidden" style='width:400px' name='key' value='<?= Encrypt(time(NULL)) ?>'>
				
			<div class="comment_input_body">
			<script src="//cdn.ckeditor.com/4.5.11/basic/ckeditor.js"></script>
			<textarea id="comment_input" name="comment"></textarea>
			<input type="submit" name="AddComment" Value="Add comment">
			</div>
			
			<script>
	  			CKEDITOR.config.height = 100;
	  			CKEDITOR.config.width = 500;
	  			CKEDITOR.config.autoParagraph = false;
	  			var uploadURL = '';
	
	  			CKEDITOR.replace('comment_input',{
	  				enterMode : CKEDITOR.ENTER_BR
		  			});
	
	
			</script>
		</form>
		<?php
	}
	
	function PropagatePOST($POST){
		if (empty($POST)) return;
		if (!(isset($POST['AddComment']) || isset($POST['CommDel']))) return;
		
		if (!IsAuth()) { AddToMsgList("Only authorized user can add comment"); return; }
		
		// Jeśli użytkownik komentował zbyt długo (>40min) lub czas pisania był podejrzanie krótki (<5s)
		$time = time(NULL)-Decrypt($POST['key']);
		if (($time>2400)) { AddToMsgList("Sorry.. Timeout.. Please Try again."); return; }
		
		if (isset($POST['AddComment'])){	
			$msg = strip_tags($POST['comment'],'<p><a><b><strong><i><br><li><ul>');
			// komentarz naturalnie musi zawiereać tekst
			if (empty($msg)) { AddToMsgList("Comment cannot be empty !");	return;}
			$this->AddComment($msg);
		}
		elseif (isset($POST['CommDel'])&&IsAdmin())
		{
			$this->DeleteComm(intval($POST['CommDel']));	
		}
		
	}
	
	function DeleteComm($ID)
	{
		$sql=DBquery("DELETE FROM Comments WHERE MODULE='$this->Module' AND ElementID=$this->ID AND ID=$ID");
		if ($sql==FALSE)
			AddToMsgList("Coś poszło nie tak :(");
	}
	
	function ShowComments($Order='DESC'){
		if ((intval($this->ID)==0)||($this->Module=='')) 
			{
			AddToLog("COMMENTS", "Klasa nie zainicjalizowana poprawnie (ID: $this->ID; MOD:$this->Module)");
			return 0;
			}
			
		$sql = DBquery("SELECT Comments.*, Users.Nick FROM Comments LEFT JOIN Users ON Comments.UserID = Users.ID WHERE MODULE='$this->Module' AND ElementID=$this->ID ORDER BY DateOF $Order");
		
		while ($row=dbarray($sql))
		{
			$Comm = new oComment($row);
			
			?>
			<div class='comment_body'>
				<div class='comment_header'>
					<div class='comment_info'><?= $Comm->AuthorNick() ?>, dnia <?= $Comm->DateOF() ?></div>
					<?php if (IsAdmin()) { ?>
						
					<div class='comment_option'>
						<form action="" method="post">
							<input type="hidden" style='width:400px' name='key' value='<?= Encrypt(time(NULL)) ?>'>
							<input type="hidden" name="CommDel" value="<?= $Comm->ID() ?>">
							<a class="deleteComm">X</a>
						</form>
					</div>
					<?php } ?>
				</div>
				
				<div class='comment_content'>
					<?= $Comm->Content() ?>
				</div>
			</div>
			
			<?php 
			
		}
		
	}
	
	function AddComment($Comment_content){
		global $User;
		$sql = dbquery("INSERT INTO Comments(`ID`, `MODULE`, `ElementID`, `DateOF`, `Content`, `UserID`)
				VALUES(NULL, '$this->Module', $this->ID, CURRENT_TIMESTAMP(), '$Comment_content', ".($User->ID()).")");
		if ($sql==FALSE)
			AddToMsgList("Something goes wrong:(");
	}
}

class oComment{
	private $ID;
	private $ElementID;
	private $DateOF;
	private $Content;
	private $AuthorID;
	private $AuthorNick;
	
	function __construct($comment){
		$this->ID = $comment['ID'];
		if (isset($comment['ElementID'])) $this->ElementID = $comment['ElementID'];
		if (isset($comment['DateOF'])) $this->DateOF = $comment['DateOF'];
		if (isset($comment['Content'])) $this->Content = $comment['Content'];
		if (isset($comment['UserID'])) $this->AuthorID = $comment['UserID'];
		if (isset($comment['Nick'])) $this->AuthorNick = $comment['Nick'];
		
		if ($this->AuthorNick()=='') $this->AuthorNick='User Deleted';
		
	}
	
	function ID(){return $this->ID;}
	function ElementID(){return $this->ElementID;}
	function DateOF(){return $this->DateOF;}
	function Content(){return $this->Content;}
	function AuthorID(){return $this->AuthorID;}
	function AuthorNick(){return $this->AuthorNick;}
	
	
}
?>