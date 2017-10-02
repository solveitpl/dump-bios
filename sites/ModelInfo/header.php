<?php

define("GPU_INTEGRATED",0);
define("GPU_DEDICATED", 1);


define("CMP_NO_CHANGE",10);
$GPUTypeLabel = array(GPU_INTEGRATED=>'Integrated', GPU_DEDICATED=>'Dedicated');
		

// typy pamięci;
define("DDR", "DDR");
define("DDR2", "DDR2");
define("DDR3", "DDR3");
define("DDR4", "DDR4");
define("DDR5", "DDR5");

class oCPU {
	public $Manuf = '';
	public $ClockSpeed; // GHz
}

class oGPU {
	public $Type=GPU_INTEGRATED;
	public $Manuf='';
	public $Model='';
	public $MemType;
	public $MemAmmount=0; // in MB
}

class oRAM{
	public $MemType;
	public $MemAmmount = 0; // in MB
}

class oDisplay {
	public $Type = '';
	public $DisplaySize = ""; // inches ''
}


class oModelInfo{
	// Główne atrybuty artykułu
	public $ID;
	public $Category;
	public $CPU;
	public $GPU;
	public $RAM;
	public $Disp;
	
	// tylko w przypadku gdy obiekt jest formą zmiany oryginału
	public $User; // autor zmian
	public $ChangeTime;
	
	// konstruktor
	function __construct($sql_arr){
		if (is_array($sql_arr))	// jeżeli nie przekazono tablicy tworzymy pustą klasę
			$this->ReFill($sql_arr);
	}

	function ReFill($ARRAY){
		
		$this->CPU = new oCPU();
		$this->GPU = new oGPU();
		$this->RAM = new oRAM();
		$this->Disp = new oDisplay();
		$this->User = oUser::CreateBlank();
		
		if (isset($ARRAY['ID'])) $this->ID = intval($ARRAY['ID']);
		if (isset($ARRAY['CategoryID'])) $this->Category = intval($ARRAY['CategoryID']);
		if (isset($ARRAY['CPU'])) $this->CPU->Manuf = htmlspecialchars($ARRAY['CPU']);
		if (isset($ARRAY['CPUClockSpeed'])) $this->CPU->ClockSpeed = htmlspecialchars($ARRAY['CPUClockSpeed']);
		if (isset($ARRAY['GPUType'])) $this->GPU->Type = intval($ARRAY['GPUType']);
		if (isset($ARRAY['GPUManuf'])) $this->GPU->Manuf = htmlspecialchars($ARRAY['GPUManuf']);
		if (isset($ARRAY['GPUModel'])) $this->GPU->Model = htmlspecialchars($ARRAY['GPUModel']);
		if (isset($ARRAY['GPUMemType'])) $this->GPU->MemType = htmlspecialchars($ARRAY['GPUMemType']);
		if (isset($ARRAY['GPUMemAmmount'])) $this->GPU->MemAmmount = intval($ARRAY['GPUMemAmmount']);
		if (isset($ARRAY['RAMType'])) $this->RAM->MemType = htmlspecialchars($ARRAY['RAMType']);
		if (isset($ARRAY['RAMAmmount'])) $this->RAM->MemAmmount = intval($ARRAY['RAMAmmount']);
		if (isset($ARRAY['DisplayType'])) $this->Disp->Type = htmlspecialchars($ARRAY['DisplayType']);
		if (isset($ARRAY['DisplaySize'])) $this->Disp->DisplaySize = htmlspecialchars($ARRAY['DisplaySize']);
		if (isset($ARRAY['ChangeTime'])) $this->ChangeTime = intval($ARRAY['ChangeTime']); else $this->ChangeTime = time(NULL);		
		if (isset($ARRAY['UserID'])) $this->User->ID = intval($ARRAY['UserID']);
		if (isset($ARRAY['UserName'])) $this->User->Nick = htmlspecialchars($ARRAY['UserName']);
		
	}


	function Accept(){
		$sql = DBquery("INSERT INTO `ModelInfo`(`ID`, `CategoryID`, `CPU`, `CPUClockSpeed`, `GPUType`, `GPUManuf`, `GPUModel`, `GPUMemType`, `GPUMemAmmount`, `RAMType`, `RAMAmmount`, `DisplayType`, `DisplaySize`)
				VALUES(NULL, ".$this->Category.", '".$this->CPU->Manuf."','".$this->CPU->ClockSpeed."',".$this->GPU->Type.",'".$this->GPU->Manuf."','".$this->GPU->Model."','".$this->GPU->MemType."',".$this->GPU->MemAmmount.",
				'".$this->RAM->MemType."', ".$this->RAM->MemAmmount.", '".$this->Disp->Type."', '".$this->Disp->DisplaySize."')");
		return 0;
	}
	
	function DeleteRev(){
		$sql = DBquery("DELETE FROM ModelInfoChanges WHERE ID=".$this->ID);
		return $sql;
	}
	
	function Compare(oModelInfo $ToCmp){
		
		if (
				($this->Category == $ToCmp->Category) &&
				($this->CPU == $ToCmp->CPU) &&
				($this->Disp == $ToCmp->Disp) &&
				($this->GPU == $ToCmp->GPU) &&
				($this->RAM == $ToCmp->RAM)
			) return true;
		else  return false;
	}
	
	function SaveAsProposition(){
		global $User;
		$info = DBarray(DBquery("SELECT * FROM ModelInfo WHERE CategoryID=".$this->Category));
		$OriginInfo = new self($info);
		
		if (($this->Compare($OriginInfo)))
			return CMP_NO_CHANGE;
		else {
		$sql = DBquery("INSERT INTO ModelInfoChanges(`ID`, `CategoryID`, `CPU`, `CPUClockSpeed`, `GPUType`, `GPUManuf`, `GPUModel`, `GPUMemType`, `GPUMemAmmount`, `RAMType`, `RAMAmmount`,
				`DisplayType`, `DisplaySize`, `ChangeTime`, `UserID`)
				VALUES(NULL, ".$this->Category.", '".$this->CPU->Manuf."', '".$this->CPU->ClockSpeed."', ".$this->GPU->Type.", '".$this->GPU->Manuf."',
				'".$this->GPU->Model."', '".$this->GPU->MemType."', ".$this->GPU->MemAmmount.", '".$this->RAM->MemType."', ".$this->RAM->MemAmmount.",
				'".$this->Disp->Type."', '".$this->Disp->DisplaySize."', ".time(NULL).", ".$User->ID.")
				");
		return $sql;
		}
	}
	
	

	// tworzenie pustej struktury
	public static function createBlank($ID)
	{
		global $User;
		$instance = new self('');
		$instance->Category = $ID;
		$instance->CPU = new oCPU();
		$instance->GPU = new oGPU();
		$instance->RAM = new oRAM();
		$instance->Disp = new oDisplay();
		$instance->User = $User;
		return $instance;
		
	}

	// ładowanie artykułu poprzez nazwę link
	public static function withCategory($ID)
	{
	
		if (empty($ID)){
			AddToLog("MODELIFNO_OBJECT", "Błędna kategoria przy pobraniu obiektu [ID = $ID]");
			return NULL;
		}

		$sql_arr = dbarray(dbquery("SELECT * FROM ModelInfo WHERE CategoryID=".$ID));
			
		if (empty($sql_arr))
		{
			AddToLog("MODELINFO_OBJECT", "Brak danych dla kategorii o numerze $ID");
			return false;
		}
		else
		{
				
			$instance = new self($sql_arr);
				
			return $instance;
		}
	}

	public static function withRevID($ID)
	{
	
		if (empty($ID)){
			AddToLog("MODELIFNO_OBJECT", "Błędna kategoria przy pobraniu obiektu [ID = $ID]");
			return NULL;
		}
	
		$sql_arr = dbarray(dbquery("SELECT * FROM ModelInfoChanges WHERE ID=".$ID));
			
		if (empty($sql_arr))
		{
			AddToLog("MODELINFO_OBJECT", "Brak danych dla rewizji ID=$ID");
			return false;
		}
		else
		{
	
			$instance = new self($sql_arr);
	
			return $instance;
		}
	}


}





?>
