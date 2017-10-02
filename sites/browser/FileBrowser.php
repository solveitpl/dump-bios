<?php


$DIVISION = strtolower($ARG[1]);

switch ($DIVISION) {
    case "images":
        break;
    case "schematics":
        break;
    case "bios":
        break;
    case "kbc-ec":
        break;
    case "boardview":
        break;

    default:
        _die("Błąd w przekierowaniu");
}


if (!isset($ARG[2])) $ARG[2] = '';

$Category = FindSelectTree($ARG);
//FindCatTree($Category['Id']);
FindCatTree($Category['Id'], 'PreparedMenu');

$PostQuant = GetPostCount($Category['Id'], $DIVISION);


if (!IsAdmin()) $Condition = 'AND (BrowserPosts.Status >= ' . POST_ACCEPTED . ")"; else $Condition = '';

if (isset($DATA['item'])) {

    $item = intval($DATA['item']);

    $sql = dbarray(dbquery("select count(*) AS oneLess FROM `BrowserPosts` WHERE 
			Category = " . $Category['Id'] . " AND MODULE = '$DIVISION' $Condition 
			AND id < $item
			ORDER BY ID"));
    $oneLess = $sql['oneLess'];

    $page_no = floor($oneLess / POST_PAGINATION) + 1;

    $page = $page_no;

} else {
    $item = 0;
    if (isset($DATA['page'])) $page = intval($DATA['page']); else $page = 1;
    if ($page < 1) $page = 1;

}


$CountOfPost = 0; // Ilość faktycznie wyświetlonych postów


$model = htmlspecialchars($Category['Name']);
if (empty($Category)) _die('Błąd przekierowania', "BROWSER");

?>
<div class="cd-breadcrumps">
    <ul class="breadcrumb">

    </ul>
    <ul>
        <li class="DIVISION_NAME"><?= $DIVISION ?><span>/</span></li>
    </ul>

    <div class="sort_block2">
        <div class="sort_block2_title">SORT BY</div>
        <button class="SortBy2"></button>
        <div class="sort-select-block">
            <div>VERIFIED</div>
            <div>ACCEPTED</div>
            <div>LAST_ADDED</div>
            <div>REJECTED</div>
            <div>NEED_TO_ACCEPT</div>

        </div>


    </div>


</div>
<div class='UploadToolbar'>

    <div>
        <form method="post" action="<?= BDIR . "browser/Add" ?>">
            <div id="addFileBtn" class="browser-add-file">ADD NEW
                <span>+</span>
            </div>
            <input type="hidden" name="CategoryID" id="CategoryID" value="<?= $Category['Id'] ?>">
            <input type="hidden" name="module" value="<?= $DIVISION ?>">
            <input type="hidden" name="Filename" value="">

        </form>

    </div>


</div>


<div class="browser_container">
    <?php
    if (IsLogin()) $UserID = $User->ID();
    else $UserID = 0;


    $sql = DBquery("SELECT BrowserPosts.*, a.PointsGood, b.PointsBad, t_Points.InUserStock, t_Votes.UserVoted, t_Points.Points, Users.Nick AS UploaderNick, t_files.* 
		FROM `BrowserPosts`
		LEFT JOIN Users ON Users.ID = BrowserPosts.UserID
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM BrowserPoints WHERE Points>0 GROUP BY PostID) as a ON BrowserPosts.ID=a.PostID 
		LEFT JOIN (SELECT PostID, SUM(Points) AS PointsBad FROM BrowserPoints WHERE Points<0 GROUP BY PostID) as b ON BrowserPosts.ID=b.PostID 
		LEFT JOIN (SELECT ElementID, UserID AS InUserStock, Points FROM Points WHERE UserID=" . $UserID . " AND MODULE='$DIVISION') AS t_Points ON t_Points.ElementID=BrowserPosts.ID 
		LEFT JOIN (SELECT Points AS UserVoted, PostID FROM BrowserPoints WHERE UserID=" . $UserID . ") AS t_Votes ON BrowserPosts.ID=t_Votes.PostID
		LEFT JOIN (SELECT GROUP_CONCAT(RealFileName SEPARATOR '|') AS Files, GROUP_CONCAT(ServerFilePath SEPARATOR '|') AS Paths, PostID FROM BrowserFiles GROUP BY PostID) as t_files ON t_files.PostID = BrowserPosts.ID
		WHERE BrowserPosts.Category = " . $Category['Id'] . " AND BrowserPosts.MODULE = '$DIVISION' $Condition
		ORDER BY " . $_SESSION['BROWSER_ORDER'] . "
		LIMIT " . (($page - 1) * POST_PAGINATION) . "," . POST_PAGINATION . "
");


    while ($row = DBarray($sql)) {

        $item_class = '';
        $status_msg = '';
        $Post = new oPost($row);


        if (($Post->Status == POST_REJECTED)) {    // rejected file is visible only for admin
            if (!IsAdmin()) continue; else $item_class = 'rejected';


        } elseif (($Post->Status == POST_NEW)) {
            if ((IsAdmin()) || ($User->CheckID($Post->Owner->ID()))) $item_class = 'need_to_accept'; else continue;


        }

        $CountOfPost++;
        $CanEdit = false;
        $CanEdit = (IsAdmin() || ($Post->Owner->CheckID($User->ID) || $Post->Status < POST_VERIFIED));

        if ($Post->ID == $item) {
            echo '<div id="ScrollHere"></div>';
            $item_class .= ' this_is_what_you_looking_at';
        }
        ?>
        <div class="browser-element-container" style="display: flex">
            <div class="browser_item <?= $item_class ?>" item="<?= $Post->ID ?>" takethis="<?= $CanEdit ?>"
                 PointsCost="<?= $Post->PointsCost ?>" key="<?= Encrypt($Post->ID) ?>"
                 InStock="<?= $Post->InUserStock() ?>" action="<?= $DIV_SETTINGS[$DIVISION]['action'] ?>">


                <?php if ($Post->InUserStock()) {
                } ?>


                <div class="browser_item_img">

                    <?php for ($i = 0; $i </*count($Post->Files)*/    //only one file for post
                    1; $i++) {

                        ?>
                        <div class="browser-<?= substr($Post->Files[$i]->Miniature, 0, 3) ?>">
                            <img class="<?= $Post->Module ?>" alt="Obraz"
                                 action="<?= $DIV_SETTINGS[$DIVISION]['action'] ?>"
                                 originFile="<?= $Post->Files[$i]->Filename ?>"
                                 medIMG="<?= BDIR . $Post->Files[$i]->ThumbDir . $Post->Files[$i]->Preview ?>"
                                 src="<?= BDIR . $Post->Files[$i]->ThumbDir . $Post->Files[$i]->Miniature ?>"
                                 onload="this.style.opacity='1';">


                            <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>

                            <?php } ?>

                            <div class="browser_title_val"><?= $Post->Title ?></div>
                            <div class="download-cost">
                                <p>COST: <span><?= $Post->PointsCost ?></span> point<?php
                                    if ($Post->PointsCost != 1) echo 's';
                                    ?>
                                </p>


                            </div>


                            <?php

                            switch ($Post->Status) {
                                case POST_ACCEPTED:

                                    ?>
                                    <div class="post-accept">accepted</div>
                                    <?php

                                    break;

                                case POST_VERIFIED:
                                    ?>
                                    <div class="post-accept">verified</div>
                                    <?php
                                    break;


                                case POST_REJECTED:
                                    ?>
                                    <div class="post-accept">post rejected</div>
                                    <?php

                                    break;

                                case POST_NEW:
                                    ?>
                                    <div class="post-accept">need to accept</div>

                                    <?php
                                    break;

                            } ?>
                            <div class="browser-download-button"
                                 action="<?= $DIV_SETTINGS[$DIVISION]['action'] ?>"
                                 originFile="<?= $Post->Files[0]->Filename ?>"

                            >download
                            </div>


                        </div>
                    <?php } ?>
                </div>

                <div class="browser_item_desc <?= $Post->Module ?>">
                    <?php
                    // echo trigger_error(print_r($Post));
                    $file_extension = pathinfo($Post->Files[0]->Filename, PATHINFO_EXTENSION);

                    ?>
                    <div class="browser-desc-info">
                        <div class="info-row">
                            <p class="desc-info-item">Size of:<span>4MB</span></p>
                            <p class="desc-info-item">Author:<span><?= $Post->Owner->UserNick() ?></span></p>
                        </div>

                        <div class="info-row">
                            <p class="desc-info-item">File extension:<span><?= strtoupper($file_extension) ?></span></p>
                            <p class="desc-info-item">Date:<span><?= date("Y-m-d", $Post->SendTime) ?></span></p>
                        </div>

                        <div class="info-row">
                            <p class="desc-info-item">Description:
                                <span>in voluptate velit esse cillum dolore eu f</span>


                            </p>

                        </div>


                    </div>


                    <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>


                        <div class="browser_item_edit change_title">EDIT</div>

                    <?php } ?>


                    <div class="browser_item_title">


                        <?/*= GetFileIcon($DIVISION) */
                        ?>

                    </div>

                    <?php if (IsAdmin()) { ?>
                        <div class="votes-container">

                            <div point="1" <?= $Post->UserVoted == 1 ? "class='voted'" : '' ?>>
                                <i class="material-icons">check</i>

                                <span><?= $Post->Points->Good ?></span></div>
                            <div point="-1" <?= $Post->UserVoted == -1 ? "class='voted'" : '' ?>>

                                <i class="material-icons">close</i>


                                <span><?= abs($Post->Points->Bad) ?></span></div>
                        </div>
                    <?php } ?>


                    <div class="browser_item_points">

                        <?php

                        switch ($Post->Status) {
                            case POST_ACCEPTED:

                                ?>


                                <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>
                                <div class="trash-delete-buttons">

                                    <a href="#" class="delete_post" marker="<?= Encrypt($Post->ID) ?>">DELETE</a>
                                </div>
                                <div class="trash-delete-buttons">
                                    <a href='#' class='set_post_status' status='-1'>TO TRASH</a>
                                </div>

                            <?php } ?>

                                <?php

                                break;

                            case POST_VERIFIED:
                                ?>


                                <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>
                                <div class="trash-delete-buttons">
                                    <a href="#" class="delete_post" marker="<?= Encrypt($Post->ID) ?>">DELETE</a>
                                </div>
                                <div class="trash-delete-buttons">
                                    <a href='#' class="set_post_status" status='-1'>TO TRASH</a>
                                </div>

                            <?php } ?>

                                <?php
                                break;


                            case POST_REJECTED:
                                ?>


                                <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>
                                <div class="trash-delete-buttons">
                                    <a href="#" class="delete_post" marker="<?= Encrypt($Post->ID) ?>">DELETE</a>
                                </div>
                                <div class="trash-delete-buttons">
                                    <a href='#' class="set_post_status" status='1'>ACCEPT</a>
                                </div>


                            <?php } ?>

                                <?php

                                break;

                            case POST_NEW:
                                ?>
                                <?php if (IsAdmin() || ($Post->Owner->CheckID($User->ID) && $Post->Status < POST_VERIFIED)) { ?>
                                <div class="trash-delete-buttons">
                                    <a href="#" class="delete_post" marker="<?= Encrypt($Post->ID) ?>">DELETE</a>
                                </div>
                                <div class="trash-delete-buttons">
                                    <a href='#' class="set_post_status" status='1'>ACCEPT</a>
                                </div>


                            <?php } ?>

                                <?php
                                break;

                        }
                        ?>
                    </div>

                    <?php if (IsAdmin()) { ?>
                        <div class="verified-button set_post_status" status="2">VERIFIED</div>
                    <?php } ?>

                    <?php if ($Post->Module == 'IMAGES') { ?>

                        <div class="desc-images-img-container">
                            <img class="desc-images-img"
                                 src="<?= BDIR ?>/upload/thumb/24/IMAGES/<?= $Post->Files[0]->Preview ?>">
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        unset($Post);
    }

    if ($CountOfPost == 0) {
        ?>
        <div class="no_content">There's no content for this category</div>
        <?php
    }

    /*
    for ($i=4; $i<100; $i++){
        dbquery("INSERT INTO BrowserPosts VALUES(NULL, 'Post nr $i', 24, 'SCHEMATICS', 1483033376, 3, 1,1,1)");
    }
    */

    $PagesQuant = floor($PostQuant / POST_PAGINATION);
    $CategoryLink = BDIR . "browser/" . $DIVISION . "/" . FindCatLinkByID($Category['Id']);
    ?>
    <!--<div class="pagination">
		<?php
    /*
            $FromPage = $page-POSTS_PAGINATION_LINK_SPACING;
            if ($FromPage<1) $FromPage=1;

            $ToPage = $page+POSTS_PAGINATION_LINK_SPACING;
            if ($ToPage>$PagesQuant) $ToPage=$PagesQuant;

            if ($FromPage>1) echo RenderPaginationLink(1, $CategoryLink, $page);
            if ($FromPage>2 ) echo RenderPaginationLink(0, '', $page); // przerwywnik


            for($i=$FromPage; $i<=($ToPage);$i++)
                echo RenderPaginationLink($i, $CategoryLink, $page);


            if ($ToPage<($PagesQuant)){
                if ($ToPage<($PagesQuant-1)) echo RenderPaginationLink(0, '', $page); // przerwywnik
                echo RenderPaginationLink($PagesQuant, $CategoryLink, $page);
            }

            */ ?>
		
	</div>-->
</div>




