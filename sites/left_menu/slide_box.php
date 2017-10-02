<?php
require_once 'functions.php';

if (!isset($ARG[1])) $ARG[1] = '';
$Module_name = htmlspecialchars($ARG[1]);
?>
<input type='hidden' value='<?= $Module_name ?>'  id="_ModulName">
<style>
#menu
    {
        background: #474747;
    }
</style>
<div id="menu">
    <h2 class="outline">MENU</h2>
	<!-- MENU CONTENT GENERATED WITH JS -->
    
   <a href="http://dump.all4it.pl/AddModel/NOTEBOOK" class="add_new_btn">Add new
       <span>+</span>
   </a>
</div>



