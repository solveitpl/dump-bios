<?php
/* Ładowanie potrzebnych plików CSS*/
$_ENV['CSS'] .= GetFileContent(SITES."left_menu/css/left_menu.css", "LEFT_MENU");
$_ENV['CSS'] .= GetFileContent(SITES."left_menu/css/jquery.multilevelpushmenu_red.css", "LEFT_MENU");
$_ENV['CSS'] .= GetFileContent(SITES."last_added/style.css", "LAST_ADDED");

//$_ENV['CSS'] .= GetFileContent(SITES."left_menu/css/font-awesome.min.css", "LEFT_MENU");
//$_ENV['CSS'] .= GetFileContent(SITES."left_menu/css/font.css", "LEFT_MENU");

$_ENV['JS'] .= GetFileContent(SITES."left_menu/js/left_menu.js", "LEFT_MENU");
$_ENV['JS'] .= GetFileContent(SITES."left_menu/js/modernizr.min.js", "LEFT_MENU_MOD");
$_ENV['JS'] .= GetFileContent(SITES."left_menu/js/jquery.multilevelpushmenu.min.js", "LEFT_MENU_MOD");
$_ENV['JS'] .= GetFileContent(SITES."left_menu/js/basicjs.js", "LEFT_MENU_BASIC");
$_ENV['JS'] .= GetFileContent(SITES."left_menu/js/bootstrap.min.js", "LEFT_MENU_BASIC");
$_ENV['JS'] .= GetFileContent(SITES."last_added/script.js", "LAST_ADDED");

/*
 * Pliki includowane tylko w przypadku odpowiednich uprawnień
 */
if (IsAdmin()){
	$_ENV['JS'] .= GetFileContent("admin/GUI/global_admin.js", "LEFT_MENU_BASIC");
	
}


/* Funkcje tworzenia elementów wizualnych strony */

function RenderHeader(){ // Druk początkowych bloków HTML
	
	
	?>
	<!doctype html>
	<html lang="en" class="no-js">
	<head>
	    <title> Dump BIOS  </title>
	    
	    <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>

		<link rel="stylesheet" href="<?php echo BDIR;?>theme/reset.css"> <!-- CSS reset -->
		<link rel="stylesheet" href="<?php echo BDIR;?>theme/style.css"> <!-- Resource style -->
		<script src="<?php echo BDIR;?>lib/modernizr.js"></script> <!-- Modernizr -->
  	
	    <link rel="stylesheet" href="<?php echo BDIR;?>lib/jquery-ui/jquery-ui.css">
	    <link rel="stylesheet" type="text/css" href="<?= BDIR ?>theme/master.css" media="all">
	  
	    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <script type="text/javascript">var BDIR = '<?= BDIR ?>';</script>
	    <script type="text/javascript" src="<?php echo BDIR;?>lib/jquery-1.7.js"></script> 
		<script type="text/javascript" src="<?php echo BDIR;?>theme/master.js"></script> 
		<script type="text/javascript" src="<?php echo BDIR;?>lib/jquery-ui/jquery-ui.js"></script>

		
	    <!-- CSS GENERATED -->
	    <style>
	    	<?= $_ENV['CSS'] ?>	
	    
	    </style>
	    
	     <!-- JS GENERATED -->
	    <script type="text/javascript">
			<?= $_ENV['JS'] ?>

		</script>
		
	

	    
	    
	</head>
	
	<body>
	
	<?php 
	// ładowanie plików zadeklarowanych przez FORMS
	while( list($key, $value) = each($_ENV['HTML']))
		require_once $value;
	
	
		

}

function RenderFooter(){ // Druk końcowych bloków HTML
	global $DebugBox;
	global $SysInfo;
	?>
	 <footer>
        <nav>
            <ul>
                <li>
                    <a href="/#">Dodaj reklame</a>
                </li>                
                <li>
                    <a href="/#">Regulamin</a>
                </li>                
                <li>
                    <a href="/#">Donate</a>
                </li>                
                <li>
                    <a href="/#">Kontakt</a>
                </li>
            </ul>
        </nav>
        <p>Copyrights <span>Dump Bios</span></p>
    </footer>
		
		<div class="NoNeedToBeSeen">
			<div id="NNTBS_SysInfo"><?php RenderSysInfo($SysInfo); ?></div>
		</div>
		
		
	</body>
</html> 
	<?php 
}

function RenderTopBar()
{
	global $User;
	global $Notifier;
	?>
	<header class="cd-main-header">
		<div class="cd-logo-box">
            <a href="<?= BDIR ?>" class="cd-logo"><p class="blink">Dump Bios </p></a>
        </div>
		<div class="cd-search is-hidden">
			<form method="POST" action="<?= BDIR ?>Search">
				<input type="search" placeholder="Search" name="SearchingWord" id="search_now">
                <input type="hidden" name="marker" value="<?= Encrypt(time(NULL)) ?>">
                <button type="submit"><i class="material-icons">search</i></button> 
			</form>
		</div>
        <div class="cd-search is-hidden">
			<input type=button value="Dodaj plik" class="add_btn" action="Navigate" arg="Downloads/Add">
			
		</div> <!-- cd-search -->
        
        <div class="buttons">
        <?php 
			if (!IsLogin()) {
		?>
            <div class="cd-search is-hidden">
                <a href="#0" class="btn" id="LogInBox" OnClick="login.ShowForm()">Logowanie</a>
            </div>
            <div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="Register">Rejestracja</a>
            </div>
            <?php 
			}
			else 
			{
				if (IsAdmin()){
			?>
			<div class="cd-search is-hidden">
               	<a href="#" class="btn" action="Navigate" arg="MenagePanel">Admin</a>
            </div>
            
			<?php }?>
			<div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="member/settings">Ustawienia</a>
            </div>
            
            <div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="logout">Wyloguj</a>
       		 </div>
			
	
		
			<?php 	
			}
            ?>
        </div>
	</header> <!-- .cd-main-header -->
	<!-- 
	
	<div class="top_bar">
		
	<?php 
	if (IsLogin()){
			?>
		

		
		
		### DEPRECATED
		
		<div class="user_bar">
		
			<div>
			Witaj, <?= $User->UserNick(); ?>
			<?php if (($User->Status()==USER_NOT_CONFIRMD) || ($User->Status()==USER_NOT_ACTIVATED)) {
					AddSysInfo("Brak aktywacji", "Twoje konto nie jest aktywowane. Nie bedziesz mógł w pełni korzystać z serwisu...");
				?>
				<img class="user_blocked_img" alt="Użytkownik nie jest aktywny" src="<?= IMAGES ?>block_icon.png">
			<?php }?> 
			
			</div>
		</div>
		<?php if (!IsAdmin()){?>

				<div class="user_bar">
					<div>
						<div class='tooltip'>
					 		<img src="<?= BDIR ?>images/main_points.png" class="point_icon" alt="Twoje punkty">
			 				<span style=""><?= ($User->Points->MainPoints()) ?></span>
			 				<span class="tooltiptext">Twoje punkty</span>
			 			</div>
			 			<div class='tooltip'>
			 				<img src="<?= BDIR ?>images/regular_points.png" class="point_icon" alt="Limit codzienny">
			 				<span style=""><?= ($User->Points->RegularPoints()) ?></span>
			 				<span class="tooltiptext">Limit codzienny</span>
			 			</div>
		 			</div>
				<!-- 
					<div> </div>
					<div>Limit dzienny:<?= $User->Points->RegularPoints() ?></div>
					
				</div>
		
	<?php 		}
			}
	
			?>
		
		
	</div> -->	
	<?php 
	
	
}

function RenderLeftSideMenu(){
	global $ARG;
	
	require_once SITES."/left_menu/slide_box.php";
		

	
}

function StartBodyContainer(){
	?>
	<main class="cd-main-content">
	<?php 
}

function EndBodyContainer(){
	?>
	</main>
	<?php 
}

function StartContent(){
	?>
	<div class="content">
	<?php 
}

function EndContent(){
	?>
	</div>
	<?php 
}


?>