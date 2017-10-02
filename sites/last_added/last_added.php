<?php
require_once 'include.php';


function ShowLastAddedForm()
{

    if (isset($_POST['TypeOfLastAddedContent'])) {
        switch ($_POST['TypeOfLastAddedContent']) {
            case 'image':
                $LastAddCategorySQL = "WHERE DIVISION='IMAGES'";
                break;
            case 'bios':
                $LastAddCategorySQL = "WHERE DIVISION='BIOS'";
                break;
            case 'kbc':
                $LastAddCategorySQL = "WHERE DIVISION='KBC-EC'";
                break;
            case 'sch':
                $LastAddCategorySQL = "WHERE DIVISION='SCHEMATICS'";
                break;
            case 'boa':
                $LastAddCategorySQL = "WHERE DIVISION='BOARDVIEW'";
                break;
            case 'sol':
                $LastAddCategorySQL = "WHERE DIVISION='ART' AND Category <> 0";
                break;
            case 'tot':
                $LastAddCategorySQL = "WHERE DIVISION='ART' AND Category = 0";
                break;
        }
    } else
        $LastAddCategorySQL = '';


    $sql = DBquery("SELECT * FROM
						(
						SELECT Articles.ID, Title, Category, Articles.Status, AddDateTime, 'ART' AS DIVISION, link,
						t1.Name AS Name0, t2.Name AS Name1, t3.Name AS Name2, t4.Name AS Name3, t5.Name AS Name4 FROM Articles 
    					LEFT JOIN Categories as t1 ON Category = t1.Id 
	    				LEFT JOIN Categories as t2 ON t1.ParentID = t2.Id
    					LEFT JOIN Categories as t3 ON t2.ParentID = t3.Id
    					LEFT JOIN Categories as t4 ON t3.ParentID = t4.Id
					    LEFT JOIN Categories as t5 ON t4.ParentID = t5.Id
   						UNION ALL
					
						SELECT BrowserPosts.ID, Title, Category, BrowserPosts.Status, FROM_UNIXTIME(SendTime) AS AddDateTime, MODULE AS DIVISION, BrowserPosts.ID as link, 
						t1.Name AS Name0, t2.Name AS Name1, t3.Name AS Name2, t4.Name AS Name3, t5.Name AS Name4 FROM BrowserPosts 
					    LEFT JOIN Categories as t1 ON Category = t1.Id 
					    LEFT JOIN Categories as t2 ON t1.ParentID = t2.Id
					    LEFT JOIN Categories as t3 ON t2.ParentID = t3.Id
					    LEFT JOIN Categories as t4 ON t3.ParentID = t4.Id
					    LEFT JOIN Categories as t5 ON t4.ParentID = t5.Id
					    ) AS TmpView
					$LastAddCategorySQL 
					ORDER BY AddDateTime DESC
			");


    ?>
    <div class="cd-sidebar">
    <div id="selector_div" class="category-select-box">
        <p>Last added</p>

        <span class="all" id="LastAddTypeList"><a href="#" id="alltext" class="alltext">All Categories</a></span>
        <button class="category_selector"></button>
        <ul class="categories" id="LastAddTypeList">
            <!--  <li><a href="#" class="info">Info</a></li> -->
              <li><a href="#" class="image">Image</a></li>  <!--No API for that-->

            <li><a href="#" class="bios">Bios</a></li>
            <li><a href="#" class="kbc">KBC/EC</a></li>
            <li><a href="#" class="sch">Schematics</a></li>
            <li><a href="#" class="boa">Boardview</a></li>
            <li><a href="#" class="sol">Solution</a></li>
            <!--  <li><a href="#" class="oth">Others</a></li>  THERE'S NO SUBCATEGORY CALLED 'Others' -->
            <li><a href="#" class="tot">Tutorial</a></li>
            <!--  <li><a href="#" class="soft">Software</a></li> NEED TO BE SOLVE  -->
        </ul>
    </div>
    <div id='l-art' class="last-articles">
        <?php
        // *** Wczytywanie z bazy danych ****
        //    <?= $Item->DIVISION
        $index = 0;
        while ($row = DBarray($sql)) {
            $index++;
            $Item = new oLastAdded($row);
            ?>
            <div id="article<?= $index ?>" class="article-list">

                <div class="category-box <?= $Item->ClassName ?>"></div>
                <div class="specs">
                    <?= $Item->Specs ?>
                    <!--wpisy na dole tylko pogladowe-->

                    <?php if ($Item->Specs == '<p>very long (...)</p>') { ?>
                        some random text (...) <br>
                        GL702
                        <?php
                    }
                    ?>

                </div>
                <div class="likes">

                    <p class="article-status">
                        <?php
                        switch ($Item->Status) {
                            case -1:
                                echo "REJECTED";
                                break;
                            case 0:
                                echo "NEW";
                                break;
                            case 1:
                                echo "ACCEPTED";
                                break;
                            case 2:
                                echo "VERIFIED";
                                break;
                            default:
                                break;
                        }
                        ?></p>

                    <a href="<?= BDIR . $Item->link ?>" class="more">> Read more</a>
                </div>
            </div>
            <?php
        }
        ?>

    </div>

    <div id="pagination">
        <ul>

        </ul>
    </div>





    <?php
}

?>