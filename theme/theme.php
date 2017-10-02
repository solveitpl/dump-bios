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
	    <title>Dump BIOS</title>
	    
	    <meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
        <link rel="icon" type="image/gif" href="/images/favi.jpg">
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
        <!-- Zwiększający się padding po zalogow -->
        			      <?php 
			if (IsLogin()) {
		?>
                <style>
                    .cd-sidebar
                    {
                        padding-top: 40px;
                    }




                </style>

                        <?php 
                }
                ?>
        		      <?php 
			if (!IsAdmin()) {
		?>

                        <?php 
                }
                ?>
	     <!-- JS GENERATED -->
	    <script type="text/javascript">
			<?= $_ENV['JS'] ?>

		</script>	    
	    
	</head>
	
	<body>
    <h2 class="outline">DUMP BIOS</h2>
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
         <h1 class="outline">FOOTER</h1>
        <nav>
            <ul>
                <li>
                    <a href="<?= BDIR ?>AdsPanel"><h2>ADD ADVERT</h2></a>
                </li>                
                <li>
                    <a href="/#"><h2>REGULATIONS</h2></a>
                </li>                
                <li>
                    <a href="/#"><h2>POINTS</h2></a>
                </li>                
                <li>
                    <a href="<?= BDIR ?>Contact"><h2>CONTACT</h2></a>
                </li>
            </ul>
        </nav>
         <div class="img-right">
         
         </div>
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
			<form method="POST" class="search" action="<?= BDIR ?>Search">
				<input type="search" placeholder="SEARCH" name="SearchingWord" id="search_now" >
                <input type="hidden" name="marker" value="<?= Encrypt(time(NULL)) ?>">
                <button type="submit"><i class="material-icons">search</i></button> 
			</form>
		</div>

        <div class="buttons">
        <?php
			if (!IsLogin()) {
		?>
            <div class="cd-search is-hidden">
                <a href="#0" class="btn" id="LogInBox" OnClick="login.ShowForm()">LOG IN</a>
            </div>
            <div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="Register">SIGN UP</a>
            </div>
            <?php
			}
			else 
			{
				if (IsAdmin()){
			?>
            
			<div class="cd-search is-hidden">
               	<a href="#" class="btn" action="Navigate" arg="MenagePanel">ADMIN</a>
            </div>
            
			<?php }?>
			<div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="member/settings">SETTINGS</a>
            </div>

            <div class="cd-search is-hidden">
                <a href="#0" class="btn" action="Navigate" arg="logout">LOG OUT</a>
       		 </div>
			
	
		
			<?php
			}
            ?>
        </div>

        <?php
        if(IsLogin()) {
            ?>

            <div class="hello_user">
                <div style="color:white;">Hello,<span style="color: #6fd8d4;"> <?= $User->UserNick(); ?></span></div>

            </div>
            <?php if (($User->Status() == USER_NOT_CONFIRMD) || ($User->Status() == USER_NOT_ACTIVATED)) {
                AddSysInfo("No activated", "Your account isnt active. You can't use that website...");
                ?>
                <img class="user_blocked_img" alt="User isnt active" src="<?= IMAGES ?>block_icon.png"
                     style="margin-left:5px;">
            <?php } ?>

            <?php
        }

        ?>
        <?php
        if(IsLogin()) {
            ?>
            <div class="img-right">
                <img class="king-crown" src="<?= BDIR ?>images/coin2.svg">
                <div class="daily-points"><?= ($User->Points->RegularPoints()) ?></div>
                <img class="king-crown-gold" src="<?= BDIR ?>images/coin1.svg">
                <div class="regular-points"><?= ($User->Points->MainPoints()) ?></div>
            </div>

            <div class="notify_msg" id="MailBox">
                <input type="hidden" id="last_message_date" value="2016-10-19-00:01:03">
                <img class="notify_icon" id="UserMSG" src="<?= IMAGES.'envelope8bitwhite.png' ?>">
                <div class='notify_msg_list'>
                    <div class="msg_list_header">User messages</div>
                    <?php
                    include $Notifier->HTML_SITE['Notify'];

                    ?>
                </div>
            </div>


            <div class="notify_msg" id="SysInfo">
                <img class="notify_icon" id="skull_icon" src="<?= IMAGES.'skull.png' ?>">
                <div class='notify_msg_list' id="SysInfoMsgs">
                    <div class="msg_list_header">System messages</div>
                    <?php
                    include $Notifier->HTML_SITE['SysInfo'];
                    ?>
                </div>
            </div>



            <?php
        }?>



    </header> <!-- .cd-main-header -->
	
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
