<?php
/// DEFINIOWANIE KLASY DO OBSŁUGI OSTATNIO DODANYCH ELEMENTÓW
define("ITEM_REJECTED", -1);
define("ITEM_NEW", 0);
define("ITEM_ACCEPTED", 1);
define("ITEM_VERIFIED", 10);

class oLastAdded{
	public $ID;
	public $DIVISION;
	public $ModelName;
	public $link;
	public $PointGood;
	public $PointBad;
	public $Specs;
	public $Category;
	public $Status;
	public $ClassName;

	
	private $RAW;
	
	function __construct($SQLarr){
		if (!is_array($SQLarr)) exit;
		
		$this->RAW = $SQLarr;
		
		$this->Category = $SQLarr['Category'];
		//$this->DIVISION = $SQLarr['DIVISION'];
		$this->Specs = "<p>".MakeItShort($SQLarr['Title'],5)."</p>";
		
		// DECYZJA O TYM KTÓRA TO DYWIZJA
		switch($SQLarr['DIVISION']){
			case 'ART':
					require_once SITES.'article/header.php';
					$this->DIVISION = $this->Category==0 ? 'TUT' : 'SOL';
					$this->ClassName = $this->Category==0 ? 'tot' : 'sol';
					$this->link = "article/view/".$SQLarr['link'];
					$this->Status = $SQLarr['Status'];
					$art = oArticle::withLink($SQLarr['link']);
					$art->LoadVoteInfo();
					$this->PointBad = abs($art->GetBadPoints());
					$this->PointGood = $art->GetGoodPoints();		
				break;
				
			case 'BIOS':
					require_once SITES.'browser/header.php';
					$Post = oPost::withID($SQLarr['ID']);
					$this->PointBad = $Post->Points->Bad;
					$this->PointGood = $Post->Points->Good;
					$this->DIVISION = 'BIOS';
					$this->ClassName = 'bios';
					$this->link = "Browser/GoTo/".$SQLarr['ID'];
					$this->Status = $SQLarr['Status'];
					break;
					
			case 'KBC-EC':
					require_once SITES.'browser/header.php';
					$Post = oPost::withID($SQLarr['ID']);
					$this->PointBad = $Post->Points->Bad;
					$this->PointGood = $Post->Points->Good;	
					$this->DIVISION = 'KBC EC';
					$this->ClassName = 'kbc';
					$this->link = "Browser/GoTo/".$SQLarr['ID'];
					$this->Status = $SQLarr['Status'];
					break;
					
			case 'SCHEMATICS':
					require_once SITES.'browser/header.php';
					$Post = oPost::withID($SQLarr['ID']);
					$this->PointBad = $Post->Points->Bad;
					$this->PointGood = $Post->Points->Good;		
					$this->DIVISION = 'SCH';
					$this->ClassName = 'sch';
					$this->link = "Browser/GoTo/".$SQLarr['ID'];
					$this->Status = $SQLarr['Status'];
					break;
					
			case 'BOARDVIEW':
					require_once SITES.'browser/header.php';
					$Post = oPost::withID($SQLarr['ID']);
					$this->PointBad = $Post->Points->Bad;
					$this->PointGood = $Post->Points->Good;	
					$this->DIVISION = 'BOA';
					$this->ClassName = 'boa';
					$this->Status = $SQLarr['Status'];
					$this->link = "Browser/GoTo/".$SQLarr['ID'];
					break;

            case 'IMAGES':
                require_once SITES.'browser/header.php';
                $Post = oPost::withID($SQLarr['ID']);
                $this->PointBad = $Post->Points->Bad;
                $this->PointGood = $Post->Points->Good;
                $this->DIVISION = 'IMG';
                $this->ClassName = 'images';
                $this->Status = $SQLarr['Status'];
                $this->link = "Browser/GoTo/".$SQLarr['ID'];
                break;
					
							
						
			default:
				$this->link = "Browser/GoTo/".$SQLarr['ID'];
		}
	
	}

}


?>