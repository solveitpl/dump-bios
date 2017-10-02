<?php
include 'header.php';


if (!isset($ARG[1])) $ARG[1] = '';
if (!isset($ARG[2])) $ARG[2] = '';

$_OWNER = 0;    // zmienna przechowuje właściciela artykułu
$ArticleStyle = '';
switch ($ARG[1]) {
    case "view":

        // jeśli usuwamy
        if (isset($_POST['DelArt']) && IsMod()) {
            $MarkerData = explode('|', Decrypt($_POST['marker']));
            $ArtID = $MarkerData[0];
            if (!CheckMarker($MarkerData[1], FALSE)) {
                StrangeEvent("Bad marker" . "ARTICLE", array($_POST, $_SESSION));
                _die("Error. Wrong marker.Please try again");
            } else {
                DBquery("DELETE FROM Articles WHERE ID=" . intval($ArtID));
                AddToMsgList("Artykuł usuniety", "INFO");

            }

        } else
            require_once 'ArticleView.php';

        break;

    case "edit":
        require 'ArticleEditor.php';
        break;

    default:    // Domyślnie wyświetlana jest lista artykułów

        // Pobranie list artykułów

        if (count($ARG) > 5) // jeśli ścieżka adresowa posiada wiecej niż 5 argumentów oznacza, że wybrano konrketną kategorię
        {
            $Category = FindSelectTree($ARG);
            $subcategory = htmlspecialchars($ARG[1]);
            $CatID = $Category['Id'];
            FindCatTree($CatID, 'PreparedMenu');
            $CatLink = $subcategory . "/" . FindCatLinkByID($CatID);
            if (empty($Category)) _die("Bad path");
            $condition = "SubcategoryTitle='$subcategory' AND Category=$CatID";
        } else // w przeciwnym razie do kategoria ogólna
        {
            $CatLink = '';
            $subcategory = 'TUTORIALS';
            $CatID = 0;
            $condition = 'Category=0';
        }

        if (IsAdmin()) {
            $StatusCondition = "";
        } else {
            $StatusCondition = " AND Articles.Status > " . ARTICLE_NEW;
            if (IsLogin()) $StatusCondition .= " OR AuthorID=" . $User->ID;
        }

        // paginacja !

        $sql = DBarray(DBquery("SELECT COUNT(*) AS Total FROM Articles WHERE $condition $StatusCondition
				ORDER BY ID DESC"));
        $ArtQuant = $sql['Total'];

        // jeśli w adresie zdefiniowany konkretny artykuł do wyróżnienia
        if (isset($DATA['item'])) {

            $item = oArticle::withLink($DATA['item']);
            $sql = dbarray(dbquery("select count(*) AS oneLess FROM Articles WHERE 
					ID > '" . $item->GetID() . "' AND $condition $StatusCondition ORDER BY ID DESC"));
            $oneLess = $sql['oneLess'];
            $page_no = floor($oneLess / ARTICLES_PAGINATION) + 1;

            $page = $page_no;
            $anchor_link = $item->GetLink();

        } else {
            if (isset($DATA['page'])) $page = intval($DATA['page']); else $page = 1;
            if ($page < 1) $page = 1;
            $anchor_link = '';
        }


        ?>
        <div class="cd-breadcrumps">
            <ul class="breadcrumb">

            </ul>
            <ul>
                <h1><li class="DIVISION_NAME <?= $subcategory ?>"><?= $subcategory ?><?php if ($subcategory != "TUTORIALS") { ?>
                        <span>/</span><?php } ?></li></h1>
            </ul>

        </div>


        <div class="add-article-btn">
            <?php if (IsLogin()) { ?>
                <form action="<?= BDIR . $ARG[0] ?>/edit/new" method="POST">
                    <input type="hidden" name="Category" value='<?= $CatID ?>'>
                    <input type="hidden" name="SubCategory" value='<?= $ARG[1] ?>'>
                    <div class="add-art-btn">
                        <input type="submit" value="ADD NEW">
                        <span>+</span>
                    </div>
                </form>
            <?php } ?>
        </div>


        <?php


        $sql_art = DBquery("SELECT Articles.*, Users.Nick AS AuthorNick FROM Articles 
				INNER JOIN Users ON Articles.AuthorID=Users.ID WHERE $condition $StatusCondition
				ORDER BY ID DESC LIMIT " . (($page - 1) * ARTICLES_PAGINATION) . "," . ARTICLES_PAGINATION);
        //echo "page: ".$item->GetID()." $oneLess ".(($page-1)*ARTICLES_PAGINATION).",".ARTICLES_PAGINATION;
        while ($row = dbarray($sql_art)) {
            $article = new oArticle($row);

            $ArticleStyle = '';
            if ($anchor_link == $article->GetLink()) $ArticleStyle .= ' promoted';
            if ($article->GetStatus(ARTICLE_REJECTED) || $article->GetStatus(ARTICLE_NEW)) $ArticleStyle .= ' not_for_public';

            $StatusStr = '';
            if (!$article->GetStatus(ARTICLE_ACCEPTED) && array_key_exists($article->GetStatus(), $STATUS_LABEL)) $StatusStr = $STATUS_LABEL[$article->GetStatus()];

            ?>

            <div class="article_min<?= $ArticleStyle ?>" article_id="<?= $article->GetLink() ?>" no="">
                <!--<div class="art_status_str"><?/*= $StatusStr */
                ?></div>-->
                <div class='article_min_img'><!--<img src='<?/*= BDIR */
                    ?>images/bios.png'>-->
                    <div class="tut">TUT</div>
                </div>
                <div class='article_desc'>
                    <?php if ($anchor_link == $article->GetLink()) {
                        echo "<div id='ScrollHere'></div>";

                    } ?>


                    <h2 class='article_min_title'><?= strip_tags($article->GetTitle()) ?></h2>
                    <div class='article_min_content'><?= MakeItShort(strip_tags(htmlspecialchars_decode($article->GetContent(), 50))) ?></div>
                    <div class='article_min_degree'>> READ MORE</div>
                    <div class='article_min_footer'>

                        <div class="footer-content">by <?= $article->GetAuthorNick() ?>
                            : <?= $article->GetAddDate() ?></div>


                    </div>

                </div>
            </div>


            <?php
            unset($article);
        }


        $PagesQuant = floor($ArtQuant / ARTICLES_PAGINATION);
        $CategoryLink = BDIR . "article/" . $CatLink;

        ?>
        <div class="pagination">
            <?php

            $FromPage = $page - ARTICLES_PAGINATION_LINK_SPACING;
            if ($FromPage < 1) $FromPage = 1;

            $ToPage = $page + ARTICLES_PAGINATION_LINK_SPACING;
            if ($ToPage > $PagesQuant) $ToPage = $PagesQuant;

            if ($FromPage > 1) echo RenderPaginationLink(1, $CategoryLink, $page);
            if ($FromPage > 2) echo RenderPaginationLink(0, '', $page); // przerwywnik


            for ($i = $FromPage; $i <= ($ToPage); $i++)
                echo RenderPaginationLink($i, $CategoryLink, $page);


            if ($ToPage < ($PagesQuant)) {
                if ($ToPage < ($PagesQuant - 1)) echo RenderPaginationLink(0, '', $page); // przerwywnik
                echo RenderPaginationLink($PagesQuant, $CategoryLink, $page);
            }

            ?>

        </div>
        </div>
        <?php
}
?>