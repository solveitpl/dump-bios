/*
 * Plik jest includowany tylko w przypadku gdy zalogowany jest administrator
 */
$(window).ready(function () {

    /*
     * FileBrowser
     * Akceptacja postów
     */
    $('.set_post_status').live('click', function () {
        if (!$(this).is('[disabled=disabled]')) {
            $item = $(this).closest('.browser_item');
            var NewStatus = $(this).attr('status');
            var PostID = $item.attr('item');

            var xhr = $.post(BDIR + 'query/admin',
                "SET_POST_STATUS=" + PostID + '&NewStatus=' + NewStatus,
                function (data) {
                    switch (data.result) {
                        case 'SUCCESS':
                            switch (NewStatus) {
                                case '0':
                                    $item.find('.browser_item_points').text('Post został oznaczony jako konieczny do akceptacji');
                                    $item.find('.post-accept').text('NEED TO ACCEPT');
                                    break;

                                case '1':
                                    $item.removeClass('need_to_accept');
                                    $item.removeClass('rejected');
                                    $item.find('.post-accept').text('accepted');
                                    $item.find('.post-accept').css('color', '#e0b03e');
                                    $item.find('.set_post_status').first().text('TO TRASH');
                                    $item.find('.set_post_status').first().attr('status', '-1');
                                    $item.find('.verified-button').removeAttr('disabled');

                                    break;

                                case '-1':
                                    $item.addClass('rejected');
                                    $item.find('.post-accept').text('post rejected');
                                    $item.find('.post-accept').css('color', '#b9b8b8');
                                    $item.find('.set_post_status').first().text('ACCEPT');
                                    $item.find('.set_post_status').first().attr('status', '1');
                                    $item.find('.verified-button').removeAttr('disabled');

                                    break;
                                case '2':
                                    $item.find('.verified-button').attr("disabled", true);
                                    $item.find('.post-accept').text('verified');
                                    $item.find('.post-accept').css('color', '#b9b8b8');


                                    break;
                            }
                            break;
                        case 'ERROR':
                            ShowDialogBox('Błąd', data.msg);
                    }
                }, 'json');
        }
    });

    $('.delete_post').live('click', function () {
        $item = $(this).closest('.browser_item');
        var PostID = $item.attr('item');
        var marker = encodeURIComponent($(this).attr('marker'));
        console.log(PostID);
        if (!confirm("Czy usunąć post wraz z wszystkimi plikami ?")) return 0;

        var xhr = $.post(BDIR + 'query/admin',
            "DELETE_POST=" + PostID + '&marker=' + marker,
            function (data) {
                switch (data.result) {
                    case 'SUCCESS':
                        ShowDialogBox(data.msg, "INFO");
                        break;
                    case 'ERROR':
                        ShowDialogBox(data.msg, "BAD");
                }
            }, 'json');
    });

    /*
     * DOWNLOAD SITE
     */
    $('.changeDFileStatus[adminTool]').live('click', function () {
        $item = $(this).closest('.FileMore');
        $toolbar = $(this).closest('.download_file').find('.FileHeader');
        $clickedElement = $(this);
        $statusInfo = $(this).parent().parent().parent().parent().find('.file-accept');
        $statusInfoVerified = $(this).parent().parent().parent().find('.file-accept');
        var NewStatus = $(this).attr('status');
        var FileID = $item.attr('fileid');
        var marker = encodeURIComponent($(this).attr('marker'));

        var xhr = $.post(BDIR + 'query/admin',
            "CHANGE_DFILE_STATUS=" + FileID + '&NewStatus=' + NewStatus + '&key=' + marker,
            function (data) {
                switch (data.result) {
                    case 'SUCCESS':
                        $toolbar.removeClass('waiting_for_accept')
                        $toolbar.removeClass('file_rejected')

                        switch (NewStatus) {
                            case '0':
                                $toolbar.addClass('waiting_for_accept');
                                console.log('status 0');
                                break;

                            case '1':
                                $toolbar.removeClass('waiting_for_accept');
                                $statusInfo.text('ACCEPTED');
                                $statusInfo.css('color', '#e0b03e');
                                $clickedElement.text('TO TRASH');
                                $clickedElement.attr('status', '-10');
                                break;

                            case '-1':
                                console.log('status -1');
                                break;
                            case '-10':
                                $clickedElement.text('ACCEPT');
                                $statusInfo.text('REJECTED');
                                $statusInfo.css('color', '#b9b8b8');
                                $clickedElement.attr('status', '1');
                                break;

                            case '2':
                                $statusInfoVerified.text('VERIFIED');
                                $statusInfoVerified.css('color', '#b9b8b8');
                                break;
                        }
                        break;
                    case 'ERROR':
                        ShowDialogBox('Błąd', data.msg);
                }
            }, 'json');
    });


    /*
     * ARTICLE SITE
     */
    $('#ArticleStatus').live('change', function () {
        var NewStatus = $(this).val();
        var ArticleID = $('#article_id').val();
        var marker = encodeURIComponent($(this).attr('marker'));
        $('#change_status_img').fadeIn();
        var xhr = $.post(BDIR + 'query/admin',
            "CHANGE_ARTICLE_STATUS=" + ArticleID + '&NewStatus=' + NewStatus + '&key=' + marker,
            function (data) {
                switch (data.result) {
                    case 'SUCCESS':

                        switch (NewStatus) {
                            case '0':

                                break;

                            case '1':

                                break;

                            case '-1':
                                break;
                        }
                        break;
                    case 'ERROR':
                        ShowDialogBox('Błąd', data.msg);
                }
                $('#change_status_img').fadeOut();
            }, 'json');
    });


})
