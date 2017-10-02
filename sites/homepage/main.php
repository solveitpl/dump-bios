<?php
require_once 'GetData.php';
require_once  SITES."AdPanel/header.php";
?>
 
<!--
<div class="contents">
	<div class="main_image">
		  
	</div>
	
	<div class="quick_menus">
	
		<div class="quick_block">
			<div class="quick_content_header">Latest articles</div>
			<div class="quick_content">
				<ul class="list_of_entries">
					<?php echo GetLastArticles(); ?>
				</ul>
			</div>
			<div class="scroll_down"><img src="<?= IMAGES ?>navigate-down.png"></div>
		</div>
	
		<div class="quick_block">
			<div class="quick_content_header">Popular software</div>
			<div class="quick_content">
				<ul class="list_of_entries">
					<?php echo GetPopularFiles(); ?>
				</ul>
			</div>
			<div class="scroll_down"><img src="<?= IMAGES ?>navigate-down.png"></div>
		</div>
		
		<div class="quick_block">
			<div class="quick_content_header">Latest post</div>
			<div class="quick_content">
				<ul class="list_of_entries">
					<?php echo GetLastPosts(); ?>
				</ul>
			</div>
			<div class="scroll_down"><img src="<?= IMAGES ?>navigate-down.png"></div>
		</div>
		
	</div>
</div>

<?php 
//$Ad = GetAd(100, 500);
?>

<div class='HomePageAD'>
	<a href=' $Ad->InternalLink()' target="_blank"><img alt="" src="BDIR.$Ad->ImagePath"></a>
</div>
 -->

<div class="cd-breadcrumps">
    <h2 class="outline">MAIN CONTENT</h2>
 <ul class="breadcrumb">

 </ul>
</div>

<div class="content-wrapper">
 <div class="adver-container">
    <div clas="adver-item">
        <img src="<?= BDIR ?>images/a_dvertisement/1.png">
        <div class="text">
            <h3>ZDOBYWAJ PUNKTY</h3>
            <p>Dodawaj pliki, artykuły, schematy, udzielaj się na forum - otrzymuj punty - pobieraj bez ograniczeń</p>
        </div>
    </div>
    <div clas="adver-item">
        <img src="<?= BDIR ?>images/a_dvertisement/2.png">
        <div class="text">
            <h3>ZAPISZ SIE DO NEWSLETTERA</h3>
            <p>(nie wysyłamy spamów)<br>otrzymasz 5 punktów w gratisie</p>
        </div>
    </div>
    <div clas="adver-item">
        <img src="<?= BDIR ?>images/a_dvertisement/3.png">
        <div class="text">
            <h3>2 PUNKTY DZIENNIE</h3>
            <p>do wykorzystania na pobranie pliku (punkty nie sumują się z poprzednich dni)<br>Zwiększaj ilość punktów na koncie
            dodając pliki, tutoriale, schematy itp.</p>
        </div>
    </div>
    <div clas="adver-item">
        <img src="<?= BDIR ?>images/a_dvertisement/4.png">
        <div class="text">
            <h3>DODAJ REKLAME NA FORUM</h3>
            <p>dotrzyj do osób z branży elektronicznej</p>
        </div>
    </div>

 </div>
</div> <!-- .content-wrapper -->
