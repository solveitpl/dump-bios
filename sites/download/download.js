var CategoryTree = '';

$(document).ready(function () {

    $('.file-accept:contains(accepted)').css('color', '#e0b03e');


    $('.desc-btn:contains(EDIT)').live('click', function () {

        $item = $(this).closest('.download_file');
        $TitleSpan = $item.find('.FileDesc');
        $nickSpan = $item.find('.desc:contains(Author)').siblings();
        $licenseSpan = $item.find('.desc:contains(License)').siblings();
        $versionSpan = $item.find('.desc:contains(Version)').siblings();
        $descSpan = $item.find('.descExtTitle').siblings();
        $TitleSpan.html('<input class="software_title_edit_text" type="text" not-empty=true value="' + $TitleSpan.text() + '">');

        $nickSpan.html('<input class="software_nick_edit_text" type="text" not-empty=true value="' + $nickSpan.text() + '">');

        $descSpan.html('<input class="software_desc_edit_text" type="text" not-empty=true value="' + $descSpan.text() + '">');

        $licenseSpan.html('<input class="software_license_edit_text" type="text" not-empty=true value="' + $licenseSpan.text() + '">');

        $versionSpan.html('<input class="software_version_edit_text" type="text" not-empty=true value="' + $versionSpan.text() + '">');


    });


    $('.FileHeader').click(function () {
        $expand = $(this).next('.FileMore');
        if ($expand.css('display') == 'none') {
            $('.FileMore').hide();
            $expand.show();
        } else {

            $('.FileMore').hide();
            $expand.hide();
        }
    });


    $('.file_voting').click(function (e) {
        e.stopPropagation();
    });

    $('#AddFileForm').on('submit', function () {
        return false;
    });

    $('.file_voting div').click(function (e) {
        e.stopPropagation();
        var file = $(this).parent().parent().attr('file_id');
        var point = $(this).attr('point');
        var xhr;
        var $this = $(this);
        $(this).parent().parent().find('div').removeClass('voted');
        $this.addClass('voted');
        var origin_img = $this.prop('src');
        $this.prop('src', BDIR + 'images/loading2.gif');
        xhr = $.post(BDIR + 'query/download',
            'FILE_VOTE=' + file
            + '&FILE_POINT=' + point,
            function (data) {
                switch (data.result) {
                    case "ACCESS_DENIED":
                        ShowDialogBox("Only authorized users can vote", "INFO");
                        break;
                    case "NOT_IN_STOCK":
                        ShowDialogBox("Please, download file before voting", "INFO");
                        break;
                    case "VOTED_ALREADY":
                        ShowDialogBox("You already voted for this file", "INFO");
                        break;
                    case "YOUR_FILE":
                        ShowDialogBox("You cannot vote for files uploaded by you", "INFO");
                        break;
                    case "VOTED_SUCCESS":
                        //ShowDialogBox("Głos został oddany "+data.msg, "GOOD");
                        $this.parent().parent().find('div[point=1]').next('span').text(data.PointsGood);
                        $this.parent().parent().find('div[point=-1]').next('span').text(data.PointsBad);
                        switch (data.action) {
                            case "VERIFICATED":
                                ShowDialogBox("It seems this software is quite good so we mark it as verified. Thank you !", "GOOD");
                                break;

                            case "TO_TRASH":
                                ShowDialogBox("Other users also complain about this file so we move it to trash. Thank you !");
                                $this.closest('.download_file').fadeOut();
                                $this.closest('.download_file').delay(2000).remove();
                                break;
                            default:
                                break;
                        }


                        //	setTimeout('location.reload()',2000);

                        break;
                    case "ERROR":
                        ShowDialogBox(data.msg, "BAD");
                        break;
                    case "INTERNAL_ERROR":
                        ShowDialogBox("Internal error. Sorry. Admin got report about this.", "BAD");
                        break;
                    default:
                        ShowDialogBox("Unknown error.", "BAD");
                }
                $this.prop('src', origin_img);
            }, 'json');


    });

    $('#addFileBtn').click(function () {
        if (USER.CheckPerm(PERM.USER)) {
            $(this).closest('form').submit();
        }
        else
            ShowDialogBox("Only authorized person can upload files", "BAD");
    });


    // download button
    $('.DownloadIMG .DownloadIT').click(function () {
        if (USER.CheckPerm(PERM.USER)) {
            $(this).closest('form').submit();
        }
        else
            ShowDialogBox("Only authorized person can download files", "BAD");

    });

    $('.AddTab').click(function () {
        $('.AddTab').removeClass('active');
        $(this).addClass('active');
        $('.tab_container').hide();
        $('#' + $(this).attr('tab')).fadeIn();
    });

    $('.FileOption[edit]').click(function (e) {
        e.stopPropagation();
        var pointer = $(this).find('img').attr('pointer');
        window.location.href = BDIR + 'downloads/Add/_edit/' + pointer;

    });

    $('.FileOption[del]').click(function (e) {
        e.stopPropagation();
        $this_header = $(this).closest('.FileHeader');
        $this_file = $(this).closest('.download_file');
        var pointer = encodeURIComponent($(this).find('img').attr('pointer'));
        if (confirm('Delete this file permanently ?')) {
            xhr = $.post(BDIR + 'query/download',
                "DELETE_DOWNLOAD_FILE=" + pointer,
                function (data) {
                    switch (data.status) {
                        case -10:
                            if (USER.CheckPerm(PERM.ADMIN)) $this_header.addClass('file_rejected');
                            else $this_file.fadeOut();
                            break;
                        case -100:
                            if (USER.CheckPerm(PERM.ADMIN)) $this_file.fadeOut();
                            break;
                    }

                }, 'json');

        }
    });

    $('.CategorySel').on('change', function () {
        var step = parseInt($(this).attr('step')) + 1;
        var category = $(this).attr('value');
        var $this = $(this);
        var xhr;
        var content = '<OPTION value="-1">Wybierz...</OPTION>';
        var query_line = '';
        var $next = $('select[step=' + step + ']');
        var next_sel = $(this).attr('next-sel');
        if (step < 3)
            query_line = 'GET_MENU=' + category;
        else
            query_line = 'GET_MENUENTRY_DETAILS=' + category + '&filter[0]=downloads&filter[1]=images';


        $next.next('img').fadeIn();
        xhr = $.post(BDIR + 'query/menu',
            query_line,
            function (data) {
                // wyłączamy selecty które są dalej niż uaktualniony
                $('#FileCat select').each(function (index) {
                    if ($(this).attr('step') > step) {
                        $(this).val('-1');
                        $(this).html(content);
                        $(this).attr('disabled', 'disabled');
                    }
                });

                $next.empty();
                for (var i = 0; i < data.length; i++) {
                    if (step == 3) content += '<OPTION value="' + data[i].Name.toLowerCase() + '">' + data[i].Name + '</OPTION>';
                    else content += '<OPTION value="' + data[i].id + '">' + data[i].Name + '</OPTION>';
                }
                $next.removeAttr('disabled');
                $next.html(content);
                $next.val(next_sel);
                if (next_sel) {
                    $this.removeAttr('next-sel');
                    $next.attr('next-sel', CategoryTree[CategoryTree.length - 1]);
                    if (step == 2) $next.attr('next-sel', $('#SelectedModule').val());

                    CategoryTree.splice(CategoryTree.length - 1, 1);


                    $next.trigger('change');
                }
                $next.next('img').fadeOut();

            }, 'json');
    });

    $('#FileCatNext').click(function () {


        var SomeDisabled = 0;
        $('#FileCat select').each(function (index) {
            SomeDisabled += (($(this).attr('disabled') == 'disabled') * 1);
        });

        if (!SomeDisabled)
            $('.AddTab[tab=FileInfo]').click();
        else {
            ShowDialogBox("Every field need to be filled", "BAD");
            return -1;
        }
        return 1;

    });

    $('#FileInfoNext').click(function () {
        $FileDesc = $('#FileDesc');
        if ($FileDesc.val() == '') {
            ShowDialogBox("This field cannot be empty !", "BAD");
            $FileDesc.css("background-color", "#ffDDDD");
        }
        else
            $('.AddTab[tab=FileUpload]').click();
    });

    $('#FileDesc').keyup(function () {
        if ($(this).val().length > 3) $(this).css("background-color", "white"); else return;
        var Module = $('.CategorySel[step=3] option:selected').text();
        var ModelID = $('.CategorySel[step=2]').val();
        var xhr;

        xhr = $.post(BDIR + 'query/download',
            'GET_FILELIST=1&CATEGORY=' + ModelID
            + '&MODULE=' + Module + '&Name=' + $('#FileDesc').val(),
            function (data) {
                // wypisanie pozycji menu
                if (data.length) {
                    $('#SimilarNamesBox').fadeIn();
                    var str = '';
                    for (var i = 0; i < data.length; i++)
                        str += '<li><a targer="_blank" href="' + BDIR + 'downloads/' +
                            $('.CategorySel[step=3] option:selected').text() + '/' +
                            $('.CategorySel[step=2] option:selected').text() + '/' +
                            data[i].Id +

                            '">' + data[i].FileDesc + '</a></li>';
                    $('#SimilarNamesBox .SimilarNamesList').html(str);

                }
                else
                    $('#SimilarNamesBox').fadeOut();
            }, 'json');


    });

    $('#FileInfo input[id!=FileDesc],textarea,select').focusin(function () {
        $('#SimilarNamesBox').fadeOut();


    });


    $('.votes').mouseenter(function () {
        return 0; // this function will bring back after finish front end
        var FileID = $(this).attr('file_id');
        //alert(id);
        $content = $(this).find('.votes_details');
        $content.html('<img src="' + BDIR + 'images/loading2.gif">');
        var xhr;
        xhr = $.post(BDIR + 'query/download',
            'WHO_VOTED=' + FileID,
            function (data) {
                switch (data.result) {
                    case "SUCCESS":
                        var str = '';
                        for (var i = 0; i < data.voters; i++)
                            str += data.VOTES[i].Nick + '(' + data.VOTES[i].Points + '), ';
                        if (str == '') str = 'No votes';
                        $content.text(str);

                        break;
                    case "INTERNAL_ERROR":
                        ShowDialogBox("Internal error. Admin got ign about this. Sorry", "BAD");
                        break;
                    case "ERROR":
                        ShowDialogBox("Error. Admin got ign about this. Sorry", "BAD");
                        break;

                    default:
                        ShowDialogBox("Unknown error.", "BAD");
                }
            }, 'json');
    });

    $('#pickfiles').click(function () {
        // Walidacja formularza wyboru kategorii

        var SomeDisabled = 0;

        $('#FileCat select').each(function (index) {
            SomeDisabled += (($(this).attr('disabled') == 'disabled') * 1) || (($(this).val() == '-1') * 1);
        });
        if (SomeDisabled) {
            $('.AddTab[tab=FileCat]').click();
            ShowDialogBox("Pick category properly", "BAD");
            return;
        }

        // Walidacja formularza info
        $FileDesc = $('#FileDesc');
        if ($FileDesc.val() == '') {
            ShowDialogBox("Title field cannot be empty", "BAD");
            $FileDesc.css("background-color", "#ffDDDD");
            $('.AddTab[tab=FileInfo]').click();
            return 0;
        }


        $('#pickfilesH').click();

    });


    if ($("#ScrollHere").length)
        $('html, body').animate({
            scrollTop: $("#ScrollHere").offset().top
        }, 2000);


});
