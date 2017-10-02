<?php
// Dostęp jedynie dla zalogowanych
if (!IsAuth()) {
    _die("Only authorized users can upload file");
    StrangeEvent("Gość próbował dostać się do panelu", 'DOWNLOAD_ADDFILE', array($User, $_SESSION, $_SERVER));
}
$CategoryID = 0;
$Module = '';
$CategoryID = 0;


if (isset($DATA['_edit'])) // jeśli edit jest zdefiniowany
{
    $md5_pointer = $DATA['_edit'];
    $sql = DBarray(DBquery("SELECT * FROM (SELECT *, MD5(CONCAT(ID, '|', RealFileName)) AS Pointer FROM `UploadedFile`) as t1 WHERE Pointer='$md5_pointer'"));
    $File = new oDFile($sql);

    // jeśli użytkonik próbuje edytować nie swój plik lub plik które został zwryfikowany
    if (($File->Status == FILE_VERIFIED || !$User->CheckID($File->UploaderID)) && !IsAdmin())
        $File = oDFile::_blank();

    if (isset($_POST['save_changes'])) {
        $File->FileDesc = $_POST['FileDesc'];
        $File->FileDescExt = $_POST['FileDescExt'];
        $File->License = $_POST['FileLicense'];
        $File->OS = $_POST['OS'];
        $File->OSver = $_POST['OSBit'];
        $File->UpdateDB();
    }
} else $File = oDFile::_blank();

?>
<script type="text/javascript">
    $(window).ready(function () {
// jeśli wysłano katergorię dla pliku to wprowadzamy ją w kolejne pola
        if (CategoryTree.length > 0) {
            $("#CatTrigger").attr('next-sel', CategoryTree[CategoryTree.length - 1]);
            $("#CatTrigger").data('Steps', CategoryTree);
            CategoryTree.splice(CategoryTree.length - 1, 1);
        }

        $('#CatTrigger').trigger("change");


    });

</script>
<script type="text/javascript" src="<?= BDIR ?>lib/plupload/js/plupload.full.min.js"></script>
<script type="text/javascript"
        src="<?= BDIR ?>lib/plupload/js/jquery.plupload.queue/jquery.plupload.queue.min.js"></script>
<input type="hidden" id="SelectedModule" value="<?= strtolower($Module) ?>">
<form method="post" <?= !$File->ID() ? 'id="AddFileForm"' : '' ?> action="">

    <div class="AddFileHeader">Add File to Software</div>
    <div id="adding-form">
        <div class="AddFileConfigure">
            <div class="AddFileTabs">
                <input type="hidden" name="FileID" value="<?= $File->ID() ?>">


            </div>

            <div id='Congratulation'>
                Congratulations! Your file has been uploaded. You will be moved to download site.
            </div>

        </div>

        <div class='tabs_container'>

            <!-- KARTA INFORMACJI O PLIKU -->
            <div class='tab_container' style="display: block" id='FileInfo'>
                <?php if (!$File->ID()) { ?>
                    <div class="AddTab active" tab='FileInfo'>
                        <div>FILE INFORMATION</div>
                    </div>

                <?php } ?>
                <div id="SimilarNamesBox">
                    <div class="BoxTitle">Similar name in this category</div>
                    <ul class="SimilarNamesList">

                    </ul>
                </div>

                <div class="ConfItem">
                    <div class='ConfTitle'>TITLE:</div>


                    <div class='ConfValue'>
                        <textarea id="FileDesc" value="<?= $File->FileDesc() ?>" name="FileDesc"></textarea>
                    </div>
                </div>

                <div class="ConfItem">
                    <div class='ConfTitle'>License:</div>
                    <div class='ConfValue'>
                        <SELECT name="FileLicense" id='file_license'>
                            <OPTION value=''>Choose...</OPTION>
                            <OPTION value='Freeware' <?= $File->Licence() == 'Freeware' ? 'selected=' : '' ?>>Freeware
                            </OPTION>
                            <OPTION value='Shareware' <?= $File->Licence() == 'Shareware' ? 'selected' : '' ?>>
                                Shareware
                            </OPTION>
                            <OPTION value='Trial' <?= $File->Licence() == 'Trial' ? 'selected' : '' ?>>Trial</OPTION>
                            <OPTION value='GNU/GPL' <?= $File->Licence() == 'GNU/GPL' ? 'selected' : '' ?>>GNU/GPL
                            </OPTION>
                            <OPTION value='other' <?= $File->Licence() == 'other' ? 'selected' : '' ?>>Another</OPTION>
                        </SELECT>
                    </div>
                </div>

                <div class="ConfItem">
                    <div class='ConfTitle'>Version:</div>
                    <div class='ConfValue'>
                        <SELECT name="OSBit" id='OSver'>
                            <OPTION value='32Bit' <?= $File->OSver == '32Bit' ? 'selected' : '' ?>>32Bit</OPTION>
                            <OPTION value='64Bit' <?= $File->OSver == '64Bit' ? 'selected' : '' ?>>64Bit</OPTION>
                            <OPTION value='other' <?= $File->OSver == 'other' ? 'selected' : '' ?>>Another</OPTION>

                        </SELECT>
                    </div>
                </div>

                <div class="ConfItem">
                    <div class='ConfTitle'>Description:</div>
                    <div class='ConfValue'>
                        <textarea name="FileDescExt" class='FileDescExt'><?= $File->GetDesc() ?></textarea>
                    </div>
                </div>

                <div class="AddTabUpload active" tab="FileInfo">
                    <div>UPLOAD FILE</div>
                </div>
                <br/>


                <img id="pac" src="/images/pac.gif">


                <div class="pacman">
                    <p class="pacman-para timer count-title count-number" data-to="100" data-speed="2300">0</p>
                    <div class="pacman-icon">
                    <div class="pacman-top"></div>
                    <div class="pacman-bottom"></div>
                    </div>
                    <div id="dot1"></div>
                    <div id="dot2"></div>
                    <div id="dot3"></div>
                    <div id="dot4"></div>
                    <div id="dot5"></div>
                    <div id="dot6"></div>
                    <div id="dot7"></div>
                    <div id="dot8"></div>
                    <div id="dot9"></div>
                    <div id="dot10"></div>
                    <div id="feed"></div>
                </div>




                <div class='btn_blck' id="container">
                    <input type=button id="pickfiles" Value="Choose a file >">
                    <br>
                    <a href="http://dump.all4it.pl/"><input type=button id="back_button" Value="< Back"></a>
                    <a href="#"><input disabled class="disabled" type=button onclick="saveFile()" id="save_button"
                                       Value="> Save"></a>

                </div>
                <div id="save-file">
                    <br/>


                    <!-- Style od buttonów i pola ładowania -->
                    <style>


                        #filelist {
                            width: 800px;
                        }
                    </style>

                </div>

                <div class='tab_container' id='FileUpload'>


                    <div id="filelist">
                        <div id="ProgressgBar"></div>
                        <div id="AddFileName"></div>
                    </div>


                    <div class='btn_blck' id="container">

                        <input type=button id="pickfiles" Value="Browse..">
                        <input type=button id="pickfilesH" Value="Browse..">
                    </div>
                    <br/>


                    <br/>
                    <pre id="console"></pre>

                    <script type="text/javascript">
                        // Custom example logic

                        var uploader = new plupload.Uploader({
                            runtimes: 'html5,flash,silverlight,html4',
                            browse_button: 'pickfilesH', // you can pass an id...
                            container: document.getElementById('container'), // ... or DOM Element itself
                            url: '<?= BDIR ?>uploadFile',
                            flash_swf_url: '../js/Moxie.swf',
                            silverlight_xap_url: '../js/Moxie.xap',
                            multi_selection: false,
                            filters: {
                                max_file_size: '1000mb',
                                mime_types: [
                                    {title: "Image files", extensions: "jpg,gif,png"},
                                    {title: "Zip files", extensions: "zip"}
                                ]
                            },
                            multipart_params: {
                                "selCategory": $('#selCategoryID').val(),
                                "selModule": $('#selModule').val(),
                                "division": "DOWNLOAD"
                            },


                            init: {
                                PostInit: function () {
                                    return;
                                },

                                FilesAdded: function (up, files) {
                                    plupload.each(files, function (file) {

                                        $('#AddFileName').attr('file', file.id);
                                        $('#AddFileName').html(file.name + ' (' + plupload.formatSize(file.size) + ') <br> <b></b>');
                                        $('#pickfiles').fadeOut();
                                        uploader.settings.multipart_params.selCategory = $('#selCategoryID').val();
                                        uploader.settings.multipart_params.selModule = $('#selModule').val();

                                        var fields = $('#AddFileForm').serializeArray();

                                        $.each(fields, function (i, field) {
                                            uploader.settings.multipart_params[field.name] = field.value;
                                        });

                                        $('#save_button').removeAttr('disabled');
                                        $('#save_button').removeClass('disabled');


                                        //uploader.start();

                                    });

                                },

                                UploadProgress: function (up, file) {
                                    //	document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";

                                    $('#ProgressgBar').css("width", file.percent + '%');
                                    if (file.percent == 100) $('#AddFileName').children('b').html('Compress...');
                                    else $('#AddFileName').children('b').html(file.percent + '%');


                                    /*    $("#pac").css('display','block');*/
                                    $(".btn_blck").fadeOut();

                                },

                                Error: function (up, err) {
                                    ShowDialogBox("Unfortunately, error...<br>" + err.code + ": " + err.message, "BAD");
                                }
                            }
                        });

                        uploader.bind('FileUploaded', function (up, file, info) {
                            var obj = JSON.parse(info.response);

                            if (!(typeof obj.error === 'undefined')) {
                                ShowDialogBox("Unfortunately while uploading appear error.<br>"
                                    + obj.error.message + "<br>"
                                    + "Kod błędu: #" + obj.error.code, "BAD");
                                $('#AddFileName').empty();
                                $('#ProgressgBar').css("width", '0%');

                                up.refresh();
                                $('#pickfiles').fadeIn();
                                return;
                            }


                            switch (obj.result) {
                                case 'SUCCESS':
                                    $('.tabs_container').hide();
                                    /*$('.AddTab').fadeOut();*/
                                    $('#Congratulation').show();
                                    /* $("#pac").fadeOut(); */


                                    setTimeout(function () {
                                        window.location.href = '<?= BDIR ?>'
                                            + 'downloads'
                                            + '/item/' + obj.id
                                        ;
                                    }, 3000);

                                    break;
                                case 'ERROR':
                                    ShowDialogBox("Error. Admin will know about it<br>" +
                                        obj.msg +
                                        "We apologize for any inconvenience.", "BAD");
                                    break;
                                default:
                                    ShowDialogBox("Error<br>" +
                                        obj.msg +
                                        "We apologize for any inconvenience.", "BAD");
                            }


                        });


                        uploader.init();

                        function saveFile() {
                            uploader.start();
                        }

                    </script>

                </div>
            </div>


        </div>
    </div>
</form>