<?php
$E_CSS = GetFileContent(SITES . 'left_menu/css/AddModel.css');
$E_JS = GetFileContent(SITES . 'left_menu/js/AddModel.js');

require_once 'functions.php';

// Jeśli nie jest zalogowany
if (!IsAuth())
    _die("Only authorized users can add category");

$ShowForm = 'block'; //  domyślnie formularz dodawania jest pokazany

// analiza linku

$menu_id = intval($ARG[1]);


/*echo "<script>$()</script>";*/
//$menu_pieces = FindSelectTreeByID($menu_id,'PreparedMenu2');

$menu_pieces = FindCatTree($menu_id, 'PreparedMenu');

for ($i = 0; $i < MENU_DEPTH; $i++)
    if (!isset($menu_pieces[$i])) $menu_pieces[$i] = '';
?>
<div class="cd-breadcrumps">
    <ul class="breadcrumb">

    </ul>
    <h1>Add new model</h1>
</div>

<?php


if (isset($_POST['AddNewModelSend'])) {

    $model_arr = array();
    $checked = FALSE;
    $ParentID = 0;
    // szybki test czy pola są w ogóle wypełnione
    for ($i = 0; $i <= 5; $i++) {
        if (($_POST[$i]['new_input'] == '') && ($_POST[$i]['select'] == -1)) _die("Błąd danych #0");
    }

    for ($i = 0; $i <= 5; $i++) {
        if (isset($_POST[$i]['new_check'])) $checked = TRUE;

        if ($checked == FALSE) // jeśli nie przetwarzamy nowego ciągu kontrolujemy czy ciągłość kategorii jest zachowana
        {
            $CatID = intval($_POST[$i]['select']);
            if ($CatID == 0) _die("Bad data #2");
            $sql = DBarray(DBquery("SELECT * FROM Categories WHERE Id=$CatID AND ParentID=$ParentID"));
            if (empty($sql)) // jeśli brak wyniku zapytania
                _die('Error of data input #3');
            else {
                $ParentID = $sql['Id']; // przypisz $ParenID dla następnego kroku
                array_push($model_arr, $ParentID);
            }
        } else // jeśli jednak zaczyna się ciąg nowych wpisów
        {
            if ($i == 0) _die("Błąd danych #1"); // nie może być nowego wpisu na pierwszym poziomie
            $InputVal = htmlspecialchars($_POST[$i]['new_input']);
            if ($InputVal == '') _die("Bad data. Empty value."); // jeżeli pole okazało się być puste
            // test czy nazwa o takiej kategorii już czasem istnieje dla tego rodzica
            $sql = DBarray(DBquery("SELECT * FROM Categories WHERE Name='" . strtolower($InputVal) . "' AND ParentID = $ParentID"));
            if (empty($sql)) {
                $sql = DBquery("INSERT INTO Categories(`Id`, `Name`, `CreatorID`, `ParentID`) VALUES(NULL, '$InputVal', " . $User->ID() . ",$ParentID)");
                if ($sql == FALSE) _die("Internal error. Sorry");
                $ParentID = DBlastID();
            } else {
                $ParentID = $sql['Id'];
                AddToLog("ADD_MODEL", "Category $InputVal exist !");
            }
            array_push($model_arr, $ParentID);
        }


    }

    $ShowForm = 'none'; // nie pokazujemy formularza dodawania
    // komunikat z gratulacjami
    ?>
    <div class="add-model-success">


        <h1>Congratulations ! You added new model.</h1>
        <span>Use menu to add data about your model</span>
    </div>
    <script type="text/javascript">select_menu = [<?= join(', ', $model_arr) ?>]</script>
    <?php
}
//
//

?>
<style>
    <?= $E_CSS ?>
</style>

<script type="text/javascript">
    <?= $E_JS ?>
</script>


<form action='' method='post' style='display: <?= $ShowForm ?>'>
    <div class="addmodel-header">

    </div>
    <input type='hidden' id='AddMenuTrigger' class='AddSelect' level='-1' value='0'>
    <?php

    RenderField('Category', '', 'MAIN_CAT', 0, 'block', FALSE, $menu_pieces[0], FALSE);
    RenderField('Producer', 'ex. ASUS, Lenovo, Dell', 'NewProducer', 1, 'none', TRUE, $menu_pieces[1]);
    RenderField('Model', 'ex. K50', 'NewModel', 2, 'none', TRUE, $menu_pieces[2]);
    RenderField('Name of model', 'ex. K50-AJK', 'NewModelName', 3, 'none', TRUE, $menu_pieces[3]);
    RenderField('Motherboard', 'ex. LA-5023', 'NewBoard', 4, 'none', TRUE, $menu_pieces[4]);
    RenderField('Revers', 'ex 1.1', 'NewRevers', 5, 'none', TRUE, $menu_pieces[5], TRUE, TRUE);

    ?>
</form>