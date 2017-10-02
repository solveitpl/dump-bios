<?php

$ModelName = htmlspecialchars($ARG[2]);


// warunki do selekcji plików do wyświetlenia
if (IsAdmin()) {
    $Condition = " 1";
} else {
    $Condition = " Status > " . FILE_NEW;
    if (IsLogin()) $Condition .= " OR UploaderID=" . $User->ID;
}


$sql = DBarray(DBquery("SELECT COUNT(*) AS Total FROM UploadedFile WHERE $Condition
			ORDER BY UploadedFile.FileUploaded DESC"));
$FileQuant = $sql['Total'];
/*
for ($i=10; $i<50; $i++){
	DBquery("INSERT INTO `UploadedFile` (`ID`, `FileDesc`, `FileDescExt`, `RealFileName`, `ServerFilePath`, `Category`, `OS`, `OSBit`, `Manufacturer`, `License`, `UploaderID`, `FileUploaded`, `Status`, `DownloadCount`, `MODULE`, `PointsCost`) 
			VALUES (NULL, 'Plik #$i', 'Siema $i', 'plik$i.jpeg', '/path/to/file', '0', NULL, '', NULL, NULL, '3', '0', '1', '0', '', '1');");
}
*/
if (isset($DATA['item'])) {

    $item = intval($DATA['item']);
    $sql = dbarray(dbquery("select count(*) AS oneLess FROM `UploadedFile` WHERE id < $item ORDER BY UploadedFile.FileUploaded DESC"));
    $oneLess = $sql['oneLess'];
    $page_no = floor($oneLess / FILES_PAGINATION) + 1;
    $page = $page_no;
    $anchorID = $item;

} else {
    if (isset($DATA['page'])) $page = intval($DATA['page']); else $page = 1;
    if ($page < 1) $page = 1;
    $anchorID = 0;
}

if (IsLogin()) {
    $Files = DBquery("SELECT DISTINCT UploadedFile.*, Users.Nick AS UploaderNick, a.PointsGood, a.PointCount, b.PointsBad, t_Points.InUserStock, t_Votes.UserVoted FROM UploadedFile
				LEFT JOIN Users ON UploadedFile.UploaderID = Users.ID
				LEFT JOIN (SELECT FileID, SUM(Points) AS PointsGood, Count(*) AS PointCount FROM FilesPoints WHERE Points>0 GROUP BY FileID) as a ON UploadedFile.ID=a.FileID
				LEFT JOIN (SELECT FileID, SUM(Points) AS PointsBad FROM FilesPoints WHERE Points<0 GROUP BY FileID) as b ON UploadedFile.ID=b.FileID
				LEFT JOIN (SELECT ElementID, UserID AS InUserStock, Points FROM Points WHERE UserID=" . $User->ID() . ") AS t_Points ON t_Points.ElementID=UploadedFile.ID
				LEFT JOIN (SELECT Points AS UserVoted, FileID FROM FilesPoints WHERE UserID=" . $User->ID() . ") AS t_Votes ON UploadedFile.ID=t_Votes.FileID
				ORDER BY UploadedFile.FileUploaded DESC LIMIT " . (($page - 1) * FILES_PAGINATION) . "," . FILES_PAGINATION);


} else {
    $Files = DBquery("SELECT DISTINCT UploadedFile.*, Users.Nick AS UploaderNick FROM UploadedFile
				LEFT JOIN Users ON UploadedFile.UploaderID = Users.ID
				LEFT JOIN (SELECT FileID, SUM(Points) AS PointSUM, Count(*) AS PointCount FROM FilesPoints GROUP BY FileID) as a ON UploadedFile.ID=a.FileID
				ORDER BY UploadedFile.FileUploaded DESC LIMIT " . (($page - 1) * FILES_PAGINATION) . "," . FILES_PAGINATION);
}
?>

<div class="download_site">

    <div class='DownloadToolbar'>
        <div class="download-add-btn">
            <form method="post" action="<?= BDIR . "downloads/Add" ?>">
                <div class="toolbar_addfile" id="addFileBtn">ADD NEW</div>
                <span>+</span>

            </form>
        </div>

        <h2 class="HeaderTitle">SOFTWARE</h2>
    </div>
    <?php
    if ($Files->num_rows == 0) {
        ?>
        <div class="no_content">No content in that page. Add file by button !</div>
        <?php
    }

    ?>
    <div class="list_of_files">
        <?php


        while ($row = dbarray($Files))
        {
        $style = '';
        $BarStyle = '';
        $File = new oDFile($row);

        if (!(($File->Status() >= FILE_ACCEPTED) || ($User->CheckID($File->UploaderID())) || IsAdmin())) continue; // jeśli plik nie jest zweryfikowany przez admina, pokaż go tylko adminowi i dodającemu

        if ($anchorID == $File->ID()) // jeśli id kotwice równe ID pliku, pokaż rozwiniecie
        {
            echo "<a id='ScrollHere'></a>";
            $style = 'style="display: block"';
        }


        ?>

        <div class='download_file'>
            <?php if (IsAdmin()) {
                ?>

                <?php
            } ?>
            <div class='FileHeader<?= $BarStyle ?>'>

                <img class="file-icon" src='<?= BDIR ?>images/icon/file.png'>

                <h3 class='FileDesc'><?= $File->FileDesc() ?></h3>

                <div class="file-accept">
                    <?php if ($File->Status() == FILE_NEW) echo 'need to accept';
                    elseif ($File->Status() == FILE_REJECTED) echo 'rejected';
                    elseif ($File->Status() == FILE_ACCEPTED) echo 'accepted';
                    elseif ($File->Status() == FILE_VERIFIED) echo 'verified';
                    //nie wiadomo czym jest status: -1

                    ?>
                </div>


                <div class='DownloadBtn'>
                    <?php if (file_exists($File->GetPath() . '/' . $File->GetRealFileName()) || IsAdmin()) { ?>
                        <div class='DownloadIMG'>
                    <form action="<?= BDIR . "downloads/StartDownload" ?>" method="post">
                        <input type='hidden' name='FileID' value="<?= $File->ID() ?>">
                        <input type='hidden' name='key' value='<?= Encrypt(time(NULL)) ?>'>
                        <div class="DownloadIT">DOWNLOAD</div>

                        <?php
                        if (IsLogin())
                            /* if (($File->InUserStock()) || $User->CheckID($File->UploaderID())) echo " Available";
                             else echo "- ".($File->PointsCost())."pkt";
                         else echo " Available";*/
                            //info if file is available by cost

                            ?>
                            <!--</div>-->
                            </form>
                            </div>
                        <?php } ?>

                </div>
            </div>
            <div class='FileMore' FileID='<?= $File->ID() ?>' <?= $style ?> >

                <?php /*echo trigger_error(print_r($File->License)); */
                ?>
                <div class='FileDescExt' <?= $style ?>>

                    <div class="LeftFileDescPos">
                        <div class='FileDescPos'>
                            <div class='desc'>Size of</div>
                            <div class='value'>15 MB</div>
                        </div>
                        <div class='FileDescPos'>
                            <div class='desc'>License:</div>
                            <div class='value'><?= $File->License ?></div>
                        </div>
                        <div class='FileDescPos'>
                            <div class='desc'>Author</div>
                            <div class='value'><a
                                        href='<?= BDIR . "member/" . $File->UploaderNick() ?>'><?= $File->UploaderNick() ?></a>
                            </div>
                        </div>
                        <div style="clear: both"></div>
                        <div class='FileDescPos ExtensionDesc'>
                            <div class='desc'>Extension:</div>
                            <div class='value'>ROM</div>
                        </div>

                        <div class='FileDescPos'>
                            <div class='desc'>Version:</div>
                            <div class='value'><?= $File->OSver ?></div>
                        </div>
                        <div class='FileDescPos'>
                            <div class='desc'>Date:</div>
                            <div class='value'><?= date("Y-m-d", $File->UploadedTime()) ?></div>
                        </div>

                        <div style="clear: both"></div>
                        <div class='descExtContainer'>
                            <div class='descExtTitle'>Description:</div>
                            <div style="text-align: left" class='value'><?= $File->GetDesc() ?></div>
                        </div>
                    </div>


                    <?php if (($User->CheckID($File->UploaderID())) || IsAdmin()) { ?>

                        <div class="desc-button-block">
                            <div class="desc-btn">EDIT</div>
                            <div class="desc-btn">DELETE</div>

                            <?php if (IsAdmin()) {
                                ?>
                                <div adminTool=1 class="changeDFileStatus verify-button" status="2"
                                     marker='<?= Encrypt($File->ID()) ?>'>VERIFIED
                                </div>
                            <?php } ?>

                            <?php if ($File->Status() == FILE_NEW || $File->Status() == FILE_REJECTED) { ?>
                                <div adminTool=1 class="changeDFileStatus desc-btn" status='1' alt='Accept'
                                     marker='<?= Encrypt($File->ID()) ?>'>ACCEPT
                                </div>
                            <?php } else {
                                ?>
                                <div adminTool=1 class="changeDFileStatus desc-btn" status='-10' alt='To trash'
                                     marker='<?= Encrypt($File->ID()) ?>'>TO TRASH
                                </div>
                            <?php } ?>

                        </div>


                    <?php } ?>
                    <?php if (IsAdmin()){
                    ?>


                    <?php if ($File->Status != FILE_VERIFIED) { ?>

                    <div class='file_voting' file_id='<?= $File->ID() ?>' <?= $style ?> >
                        <div class='vote vote-yes'>
                            <div point='1' class='<?php echo $File->UserVoted() == 1 ? 'voted' : ''; ?>'>
                                <i class="material-icons">check</i>

                            </div>

                            <span><?= $File->PointsGood() ?></span></div>
                        <div class='vote vote-no'>
                            <div point='-1' class='<?php echo $File->UserVoted() == -1 ? 'voted' : ''; ?>'>
                                <i class="material-icons">close</i>


                            </div>

                            <span><?= abs($File->PointsBad()) ?></span></div>

                    </div>
                    <div class='DownloadCount votes'
                    '><img class="vote_loading" src="<?= IMAGES ?>loading2.gif"></div>


                <?php }
                else
                {
                ?>


                <div class='file_voting' file_id='<?= $File->ID() ?>' <?= $style ?> >
                    <div class='vote vote-yes'>
                        <div point='1' class='<?php echo $File->UserVoted() == 1 ? 'voted' : ''; ?>'>
                            <i class="material-icons">check</i>

                        </div>

                        <span><?= $File->PointsGood() ?></span></div>
                    <div class='vote vote-no'>
                        <div point='-1' class='<?php echo $File->UserVoted() == -1 ? 'voted' : ''; ?>'>
                            <i class="material-icons">close</i>


                        </div>


                        <span><?= abs($File->PointsBad()) ?></span>
                    </div>

                </div>


                <div class='DownloadCount votes'
                '><img class="vote_loading" src="<?= IMAGES ?>loading2.gif"></div>
        <?php }
        if (!empty($File->OS))
            $OS_Str = implode(', ', $File->OS());
        else $OS_Str = '';
        ?>
        <?php
        } ?>


        </div>
    </div>
</div>

<?php
}


?>
</div>
</div>

<?php

$PagesQuant = floor($FileQuant / FILES_PAGINATION);
$CategoryLink = BDIR . "downloads/";

?>

<div class="pagination">
    <?php

    $FromPage = $page - FILES_PAGINATION_LINK_SPACING;
    if ($FromPage < 1) $FromPage = 1;

    $ToPage = $page + FILES_PAGINATION_LINK_SPACING;
    if ($ToPage > $PagesQuant) $ToPage = $PagesQuant;

    /*if ($FromPage>1) echo RenderPaginationLink(1, $CategoryLink, $page);*/
    /*if ($FromPage>2 ) echo RenderPaginationLink(0, '', $page);*/ // przerwywnik


    for ($i = $FromPage; $i <= ($ToPage); $i++)
        echo RenderPaginationLink($i, $CategoryLink, $page);


    if ($ToPage < ($PagesQuant)) {
        if ($ToPage < ($PagesQuant - 1)) echo RenderPaginationLink(0, '', $page); // przerwywnik
        echo RenderPaginationLink($PagesQuant, $CategoryLink, $page);
    }

    ?>

</div>
</div>