<?php
$_OWNER = 0 ;
define("ARTICLE_REJECTED", -1);
define("ARTICLE_NEW", 0);
define("ARTICLE_ACCEPTED", 1);
define("ARTICLE_VERIFIED", 10);

$STATUS_LABEL = array(ARTICLE_REJECTED =>'Rejected', ARTICLE_NEW=>'New', ARTICLE_ACCEPTED=>'Accepted', ARTICLE_VERIFIED=>'Verified by users');

define("ARTICLES_PAGINATION",6);
define("ARTICLES_PAGINATION_LINK_SPACING",2);


class oArticle{
	// Główne atrybuty artykułu
	private $ID;
	private $Title;
	private $Author;
	private $AuthorNick;
	private $ContentShort;
	private $Content;
	private $AddDate;
	private $UpdateDate;
	private $Status;
	private $Category;
	private $SubCategory;
	private $Link;
	
	// Punkty
	private $Points;
	private $GoodPoints;
	private $BadPoints;
	
	private $VotesCount;
	public $UserVoted;
	
	// konstruktor
	function __construct($sql_arr){
		if (!is_array($sql_arr)) return;	// jeżeli nie przekazono tablicy tworzymy pustą klasę
		$this->ID = $sql_arr['ID'];
		$this->Title = $sql_arr['Title'];
		$this->Author = $sql_arr['AuthorID'];
		$this->ContentShort = $sql_arr['ContentShort'];
		$this->Content = $sql_arr['Content'];
		$this->AddDate = $sql_arr['AddDateTime'];
		$this->UpdateDate = $sql_arr['UpdateDateTime'];
		$this->Status = $sql_arr['Status'];
		$this->Category = $sql_arr['Category'];
		$this->SubCategory = $sql_arr['SubcategoryTitle'];
		$this->Link = $sql_arr['link'];
		
		if (isset($sql_arr['AuthorNick'])) $this->AuthorNick = $sql_arr['AuthorNick'];
	}
	
	function ReFill($ARRAY){
		if (isset($ARRAY['ID'])) $this->ID = intval($ARRAY['ID']);
		if (isset($ARRAY['Title'])) $this->Title = htmlspecialchars(strip_tags($ARRAY['Title']));
		if (isset($ARRAY['AuthorID'])) $this->Author = intval($ARRAY['AuthorID']);
		if (isset($ARRAY['Content'])) $this->Content = htmlspecialchars($ARRAY['Content']);
		if (isset($ARRAY['SubcategoryTitle'])) $this->SubCategory = htmlspecialchars($ARRAY['SubcategoryTitle']);	
		if (isset($ARRAY['Category'])) $this->Category = intval($ARRAY['Category']);
		
	}
	
	function PutToDB(){
		if ($this->ID==0) $this->ID = 'NULL';
		$this->Link = MakeLink($this->Title);
		DBquery("INSERT INTO Articles (`ID`, `link`, `Title`, `Content`, `AuthorID`, `AddDateTime`,`UpdateDateTime`, `Category`, `SubcategoryTitle`) 
				       VALUES($this->ID,'".$this->Link."', '".$this->Title."', '".$this->Content."', '".$this->Author."', CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,
							  ".$this->Category.", '".$this->SubCategory."') 
				ON DUPLICATE KEY UPDATE link='".$this->Link."', Title='".$this->Title."', Content='$this->Content', Category=$this->Category, UpdateDateTime=CURRENT_TIMESTAMP,
				SubcategoryTitle='$this->SubCategory'"
				
				);
	}
	
	// Pobranie danych
	function CheckStatus($status){return ($status==$this->Status);}
	function GetID(){return $this->ID;}
	function GetTitle(){return $this->Title;}
	function GetAuthorID() {return $this->Author;}
	function GetAuthorNick() {return $this->AuthorNick;}
	function GetContent() {return $this->Content;}
	function GetShortContent() {return $this->ContentShort;}
	function GetStatus($Compare=FALSE) {
		if ($Compare===FALSE)
			return $this->Status;
		else
			if ($Compare==$this->Status) return true; else return false;
	}
	function GetCategory() {return $this->Category;}
	function GetAddDate() {return $this->AddDate;}
	function GetUpdateDate(){return $this->UpdateDate;}
	function GetSubCategory(){return $this->SubCategory;}
	function GetLink(){return $this->Link;}
	
	function GetGoodPoints(){return $this->GoodPoints;}
	function GetBadPoints(){return $this->BadPoints;}
	function GetVotesCount(){return $this->VotesCount;}
	
	// czy użytkownik może edytować dany artykuł
	function UserCanEdit() {
		global $User;
		return (IsAdmin()||($User->CheckID($this->ID)));
	}
	
	// pobranie informacji o punktach
	function LoadVoteInfo(){
		global $User;
		$Points = dbarray(DBquery("SELECT SUM(Points) AS Points, COUNT(*) AS Votes FROM ArticlesPoints WHERE ArticleID=".$this->ID));
		$Points = DBarray(DBquery("SELECT a.GoodPoints, a.CountPoints, b.BadPoints FROM Articles
	LEFT JOIN (SELECT ArticleID, SUM(Points) AS GoodPoints, Count(*) AS CountPoints FROM ArticlesPoints WHERE Points>0 GROUP BY ArticleID) as a ON Articles.ID = a.ArticleID
	LEFT JOIN (SELECT ArticleID, SUM(Points) AS BadPoints FROM ArticlesPoints WHERE Points<0 GROUP BY ArticleID) as b ON Articles.ID=b.ArticleID
			 	 WHERE ID = ".$this->ID));
		
		if (empty($Points)){
			$this->GoodPoints = 0;
			$this->BadPoints = 0;
			$this->VotesCount = 0;
			}
		else
			{
			$this->VotesCount = intval($Points['CountPoints']);
			$this->GoodPoints = intval($Points['GoodPoints']);
			$this->BadPoints = intval($Points['BadPoints']);
			}
			
		// czy użytkownik głosował już na ten artykuł
			if (IsLogin()) {
				$user_vote = dbarray(DBquery("SELECT Points FROM ArticlesPoints WHERE ArticleID=".$this->ID." AND UserID=".$User->ID()));
				if (!empty($user_vote)) $this->UserVoted = $user_vote['Points'];
				else $this->UserVoted = 0;
			}	
	}
	
	// ładowanie artykułu przez ID
	function GetDataByID($ID){
		if (!intval($ID)){
			AddToLog("ARTICLE_OBJECT", "Błędne ID artykułu przy próbie pobrania danych");
			return NULL;
		}
		$sql = dbarray(DBquery("SELECT Articles.*, Users.Nick AS AuthorNick FROM Articles INNER JOIN Users ON Articles.AuthorID=Users.ID WHERE Articles.ID=".$ID));
		$this->__construct($sql);
	}
	
	// ładowanie artykułu poprzez nazwę link
	public static function withLink($link)
	{

		if (empty($link)){
			AddToLog("ARTICLE_OBJECT", "Błędny LINK artykułu przy próbie pobrania danych");
			return NULL;
		}
		
		$sql_arr = dbarray(DBquery("SELECT Articles.*, Users.Nick AS AuthorNick FROM Articles INNER JOIN Users ON Articles.AuthorID=Users.ID WHERE Articles.link='".$link."'"));
	
		if (empty($sql_arr))
		{
			AddToLog("ARTICLE_OBJECT", "Błędny Artykułu dla tego linku");
			return NULL;
		}
		else
		{
			$instance = new self($sql_arr);
			
			return $instance;
		}
	}
	
	
	
}

?>