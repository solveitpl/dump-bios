$(window).ready(function () {
    $('.post-accept:contains(accepted)').css('color', '#e0b03e');
    $('.post-accept:contains(verified)').parent().parent().parent().find($('.verified-button')).attr("disabled", true);


    $('.browser_item > div.browser_item_img').click(function (e) {

        var prevOffset = $(this).offset().top - 70;


        if (!$(this).parent().find($('.browser_item_desc')).is(":visible")) $('.browser_item_desc').hide();
        $(this).parent().find($('.browser_item_desc')).toggle();
        if ($(this).parent().find($('.browser_item_desc')).hasClass('IMAGES')) {
            if ($(this).next().is(":visible")) {

                $('.content').scrollTop(0);
                $('.content').scrollTop($(this).offset().top - 70);
            } else {
                $('.content').scrollTop(prevOffset);
            }
        }

    });

    $('.close_win').click(function () {
        $(this).parent().parent().fadeOut();
    })


    $('.sort_block2 > .SortBy2, .sort_block2_title').click(function () {
        $('.sort-select-block').slideToggle('fast');

    });
    $(document).mouseup(function (e) {
        var btnExpand = $('.SortBy2');
        var titleExpand = $('.sort_block2_title');

        if (!$('.sort-select-block').is(e.target) // if the target of the click isn't the container...
            && $('.sort-select-block').has(e.target).length === 0 // ... nor a descendant of the container
            && !btnExpand.is(e.target)) {

            if (!titleExpand.is(e.target)) $('.sort-select-block').hide();
        }
    });

    $('.sort_block2 .sort-select-block > div').click(function () {
        console.log($(this).text());
        var xhr = $.post(
            BDIR + 'query/browser',
            'CHANGE_SORT=' + $(this).text(),
            function (data) {
                switch (data.result) {
                    case "SUCCESS":
                        location.reload();
                        break;

                    default:
                        ShowDialogBox("Unknow error", "BAD");
                }
            }, 'json');


    });


    $('.browser-download-button[action=download]').on('click', function (e) {
        e.stopPropagation();
        $file = $(this).closest(".browser_item");
        var origin = $(this).attr('originFile');
        var id = $file.attr('item');
        var InStock = $file.attr('InStock');
        var key = $file.attr('key');

        if (USER.Perm == 0) {
            ShowDialogBox("Pliki pobierać mogą jedynie zalogowani użytkownicy", "INFO");
            return 0;
        }

        if (InStock)
            window.location.href = BDIR + 'GetFileDemo/?Post=' + id + '&File=' + origin + '&Mode=BUY';
        else {
            $('#inset_form').html(
                '<form action="' + BDIR + 'browser/Buy/" name="buy" method="post" style="display:none;">'
                + '<input type="hidden" name="key" value="' + key + '" /><input type="hidden" name="filename" value="' + origin + '"></form>');
            document.forms['buy'].submit();

        }
    });

    $('.browser_item_img img[action=view]').click(function () {
        $item = $(this).closest('.browser_item');
        var originFile = $(this).attr('originFile');
        var id = $item.attr("item");
        var point = $item.attr("pointscost");
        var status = $item.find("[status]").attr('status');
        var PointsGood = $item.find('[point=1]').text();
        var PointsBad = $item.find('[point=-1]').text();
        var CategoryID = $('#CategoryID').val();
        var MedIMG = $(this).attr('medIMG');


        var InStock = $item.attr("instock");

        $('.PointCost span').text('(-' + point + 'pkt)');
        if (InStock)
            $('.PointCost span').hide();
        else
            $('.PointCost span').show();
        $img = $('.browser_big .img_container');
        $img.css("background-image", "url('"
            + BDIR
            + "images/loading2.gif')");

        $('<img/>').attr('src', MedIMG).load(
            function () {
                $(this).fadeOut();
                $(this).remove();
                $img.css({
                    "background-image": "url('"
                    + MedIMG + "')",
                });
            });

        $('.browser_big').fadeIn();

        $('.browser_big').attr("curr_id", $item.attr("item"));
        $('.browser_big').attr("Filename", MedIMG);
        $('.browser_big').attr("originFile", originFile);


    })

    $('.browser_big .img_nav.next').click(function () {
        $item = $(this).closest('.browser_big');
        var Filename = $item.attr('Filename');
        $next = $('img[medIMG="' + Filename + '"]').parent().next('div').children('img');
        $next.delay(3000).trigger("click");
    });

    $('.browser_big .img_nav.prev').click(function () {
        $item = $(this).closest('.browser_big');
        var Filename = $item.attr('Filename');
        $next = $('img[medIMG="' + Filename + '"]').parent().prev('div').children('img');
        $next.delay(3000).trigger("click");
    });

    $('.votes-container div[point]').click(function () {
        var FileID = $(this).closest('.browser_item').attr("item");


        var point = $(this).attr('point');
        $this = $(this);
        $(this).parent().find('div').removeClass('voted');
        $this.addClass('voted');
        $('.loading_img').fadeIn();

        var xhr;
        xhr = $.post(BDIR + 'query/browser',
            'BROWSER_POST_VOTE=' + FileID
            + '&POST_POINT=' + point,
            function (data) {
                switch (data.result) {
                    case "ACCESS_DENIED":
                        ShowDialogBox("Głosować mogą jedynie zalogowani i uprawnieni użytkownicy", "INFO");
                        break;
                    case "NOT_IN_STOCK":
                        ShowDialogBox("Nie możesz oceniać nie pobranego przez Ciebie pliku", "INFO");
                        break;
                    case "VOTED_ALREADY":
                        ShowDialogBox("Głos już został oddany na ten plik", "INFO");
                        break;
                    case "OWNER_DENIED":
                        ShowDialogBox("Nie możesz głosować na swój plik", "INFO");
                        break;

                    case "VOTED_SUCCESS":
                        //ShowDialogBox("Głos został oddany "+data.msg, "GOOD");
                        $this.parent().find('div[point=1]').children('span').text(data.PointsGood);
                        $this.parent().find('div[point=-1]').children('span').text(data.PointsBad);
                        switch (data.action) {
                            case "VERIFICATED":
                                ShowDialogBox("Twoim głosem, jak innych uzytkowników. Post został oznaczony jako zweryfikowany :)", "GOOD");
                                break;

                            case "TO_TRASH":

                                ShowDialogBox("Inni również narzekali na ten post, dlatego w tym momencie jest przenoszony do kosza. Dziękujęmy za Twój głos.");
                                $('.browser_item[item=' + FileID + ']').fadeOut();
                                $('.browser_item[item=' + FileID + ']').delay(2000).remove();
                                $('.browser_big').fadeOut();
                                break;
                            default:
                                break;
                        }


                        //	setTimeout('location.reload()',2000);

                        break;
                    case "INTERNAL_ERROR":
                        ShowDialogBox("Błąd wewnętrzny. Powiadomiono administratora. Przepraszamy", "BAD");
                        break;
                    default:
                        ShowDialogBox("Nieznany błąd. " + data.msg, "BAD");
                }
                $('.loading_img').fadeOut();
            }, 'json');


    });


    $('.browser_item_img img').on('error', function () {
        //this.src = BDIR + 'images/broken_img.gif'
    });

    $('#addFileBtn').click(function () {
        if (USER.Perm == 0) {
            ShowDialogBox("Tylko zalogowani użytkownicy mogą ładować pliki", "INFO");
            return;
        }

        $(this).closest('form').submit();
    });


    $('#GetFile').click(function () {
        var id = $('.browser_big').attr('curr_id');
        var origin = $('.browser_big').attr('originFile');
        $file = $(".browser_item[item=" + id + "]");
        var point = $file.attr('point');
        var InStock = $file.attr('InStock');
        var key = $file.attr('key');

        if (USER.Perm == 0) {
            ShowDialogBox("Pliki pobierać mogą jedynie zalogowani użytkownicy", "INFO");
            return 0;
        }

        if (InStock)
            window.location.href = BDIR + 'GetFileDemo/?Post=' + id + '&File=' + origin + '&Mode=BUY';
        else {
            $('#inset_form').html(
                '<form action="' + BDIR + 'browser/Buy/" name="buy" method="post" style="display:none;">'
                + '<input type="hidden" name="key" value="' + key + '" /><input type="hidden" name="filename" value="' + origin + '"></form>');
            document.forms['buy'].submit();

        }

    });

    $('#post_form').submit(function () {

        $input = $('#post_title_input');
        if ($input.val() == '') {
            ShowDialogBox("Wpisz nazwę.", "BAD");
            $('#marker').focus();
            return false;
        }
    });

    $('.del_tmp_file').live('click', function () {
        var file = $(this).attr('file');
        $this = $(this);

        var xhr = $.post(
            BDIR + 'query/browser',
            'DEL_TEMP_FILE=' + encodeURIComponent(file),
            function (data) {
                switch (data.result) {
                    case "ACCESS_DENIED":
                        ShowDialogBox(
                            "Głosować mogą jedynie zalogowani i uprawnieni użytkownicy", "INFO");
                        break;
                    case "INTERNAL_ERROR":
                        ShowDialogBox("Błąd wewnętrzny. Powiadomiono administratora. Przepraszamy", "BAD");
                        break;
                    case "SUCCESS":
                        $this.fadeOut();
                        $this.closest('tr').delay(500).remove();
                        break;

                    default:
                        ShowDialogBox("Nieznany błąd.", "BAD");
                }
            }, 'json');
    });


    $('.browser_item_img .slide_to_left, .slide_to_right').live('click', function () {
        $container = $(this).closest('.browser_item_img');
        var direction = parseInt($(this).attr('direction'))
        //$container.scrollLeft($container.scrollLeft()+(300*direction));
        $container.animate({scrollLeft: $container.scrollLeft() + 300 * direction}, 100);


    });

    $('.remove_file_btn').live('click', function () {
        if (!confirm("Czy na pewno usunąć ten plik z postu ?")) return;
        var data = encodeURIComponent($(this).attr('data'));
        var key = encodeURIComponent($(this).attr('key'));
        var time = Date.now();
        $img = $(this).parent();
        var xhr = $.post(
            BDIR + 'query/browser',
            'DEL_FILE=' + data + '&KEY=' + key + '&c=' + time,
            function (data) {
                switch (data.result) {
                    case "ACCESS_DENIED":
                        ShowDialogBox(
                            "No, no, no", "INFO");
                        break;
                    case "INTERNAL_ERROR":
                        ShowDialogBox("Błąd wewnętrzny. Powiadomiono administratora. Przepraszamy", "BAD");
                        break;
                    case "SUCCESS":
                        $img.fadeOut();
                        $img.remove();
                        break;

                    default:
                        ShowDialogBox("Nieznany błąd.", "BAD");
                }
            }, 'json');

    });

    $('.browser_item_edit.change_title').live('click', function () {
        $Item = $(this).closest('.browser_item');
        $TitleSpan = $Item.find('.browser_title_val');
        $nickSpan = $(this).parents().find('.browser-desc-info').children().first().children().last().find('span');
        $descSpan = $(this).parent().find('.desc-info-item').last().find('span');
        var FileID = $Item.attr("item");
        $(this).hide();
        $TitleSpan.html('<input class="browser_title_edit_text" type="text" not-empty=true value="' + $TitleSpan.text() + '">');

        $nickSpan.html('<input class="browser_nick_edit_text" type="text" not-empty=true value="' + $nickSpan.text() + '">');

        $descSpan.html('<input class="browser_desc_edit_text" type="text" not-empty=true value="' + $descSpan.text() + '">');


    });

    $('.browser_title_edit_text').live('keypress', function (e) {
        if (e.keyCode == 13) {
            if ($(this).val() == '') return 0;
            $Item = $(this).closest('.browser_item');
            $TitleSpan = $Item.find('.browser_title_val');
            $EditBtn = $Item.find('.browser_item_edit');
            $input = $(this);
            var FileID = $Item.attr("item");
            AddLoadingBckgnd($input);

            var xhr = $.post(
                BDIR + 'query/browser',
                'CHANGE_POST_TITLE=' + FileID + '&NewV=' + encodeURIComponent($(this).val())
                + '&key=' + encodeURIComponent($Item.attr('key')),
                function (data) {
                    switch (data.result) {
                        case "ACCESS_DENIED":
                            ShowDialogBox(
                                "Brak uprawnień", "BAD");
                            break;
                        case "INTERNAL_ERROR":
                            ShowDialogBox("Błąd wewnętrzny. Powiadomiono administratora. Przepraszamy", "BAD");
                            break;
                        case "SUCCESS":
                            ShowDialogBox(data.msg, "GOOD");
                            $input.hide();
                            $TitleSpan.html($input.val());
                            $EditBtn.show();
                            break;

                        default:
                            ShowDialogBox("Nieznany błąd.", "BAD");
                    }
                }, 'json');


        }


    });

    /*
     * Who voted on post
     *
     */

    $('.votes').click(function () {
        var post_id = $(this).closest('.browser_item').attr('item');

        $content = $(this).find('.votes_details');
        $content.fadeToggle();
        if ($content.html() == '')
            $content.html('<img src="' + BDIR + 'images/loading2.gif">');
        var xhr;
        xhr = $.post(BDIR + 'query/browser',
            'WHO_VOTED=' + post_id,
            function (data) {
                switch (data.result) {
                    case "SUCCESS":
                        var str = '';
                        for (var i = 0; i < data.voters; i++)
                            str += data.VOTES[i].Nick + '(' + data.VOTES[i].Points + '), ';
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


    $('.browser_item_download').live('click', function () {
        var ItemID = $(this).closest('.browser_item').attr('item');

        var InStock = $(this).closest('.browser_item').attr('InStock');
        var key = $(this).closest('.browser_item').attr('key');

        if (USER.Perm == 0) {
            ShowDialogBox("Pliki pobierać mogą jedynie zalogowani użytkownicy", "INFO");
            return 0;
        }

        if (InStock)
            window.location.href = BDIR + 'GetFileDemo/?Post=' + ItemID + '&File=package&Mode=BUY';
        else {
            $('#inset_form').html(
                '<form action="' + BDIR + 'browser/Buy/" name="buy" method="post" style="display:none;">'
                + '<input type="hidden" name="key" value="' + key + '" /><input type="hidden" name="filename" value="package"></form>');
            document.forms['buy'].submit();

        }

    });
    setTimeout(function () {
        $(".FilterInputClass").css('display', 'none');
        $(".levelHolderClass ul").css('top', '35px');
    }, 0);


});