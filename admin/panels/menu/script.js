$(document).ready(function () {
    $('.menu_category').die('dblclick');
    $('.menu_category').live('dblclick', function () {
        // change title of category
        $this = $(this);
        var ID = $this.attr('cat_id');
        var title = $(this).find('.menu_title').text();
        var NewTitle = prompt("Enter new title of category", title);

        if (NewTitle == '') {
            ShowDialogBox("Title too short", "BAD");
            return 0;
        }

        xhr = $.post(BDIR + 'query/admin',
            {
                CHANGE_MENU_TITLE: 1,
                CAT_ID: ID,
                NEW_TITLE: NewTitle
            },
            function (data) {
                switch (data.result) {
                    case 'SUCCESS':
                        $this.find('.menu_title').text(NewTitle);
                        ShowDialogBox("Title changed", "GOOD");
                        break;

                    case 'ERROR':
                        ShowDialogBox("ERROR:" + data.msg, "BAD");
                        break;

                    default:
                        ShowDialogBox("Unknown error", "BAD");
                }


            }, 'json');


    });

    $('.menu_trash_icon').die('click');
    $('.menu_trash_icon').live('click', function (e) {
        if (!confirm('Do you wanna delete categories in trash ?')) return 0;
        var ItemsToDelete = [];
        $(this).next('.menu_items').find('.menu_category').each(function () {
            var ID = $(this).attr('cat_id');
            $(this).remove();
            ItemsToDelete.push(ID);
        });

        xhr = $.post(BDIR + 'query/admin',
            {
                DELETE_CATEGORIES: 1,
                CAT_ID: ItemsToDelete
            },
            function (data) {
                switch (data.result) {
                    case 'SUCCESS':
                        ShowDialogBox("Categories deleted", "GOOD");
                        break;

                    case 'ERROR':
                        ShowDialogBox("ERROR:" + data.msg, "BAD");
                        break;

                    default:
                        ShowDialogBox("Unknown error", "BAD");
                }


            }, 'json');

    });


    $('.menu_category').die('click');
    $('.menu_category').live('click', function () {
        $this = $(this);
        var ID = $this.attr('cat_id')
        var data_level = parseInt($this.closest('.menu_level').attr('level')) + 1;
        if (data_level >= 5) return;
        $next = $this.closest('.menu_level').closest('.hide-scroll').next('.hide-scroll').find('.menu_items');
        $('.menu_level[level]').each(function () {
            if ($(this).attr('level') > data_level) $(this).find('.menu_items').html('');
        });
        $next.html('<img class="load_img" src="' + BDIR + 'images/loading2.gif">');


        var query_line = 'GET_MENU=' + ID + '&step=' + data_level;
        xhr = $.post(BDIR + 'query/menu',
            query_line,
            function (data) {
                $('.load_img').hide();
                var str = '';
                var desc = '';

                for (var i = 0; i < data.length; i++) {
                    // jeśli dana kategoria jest przechowywana w tymczasowej tabeli
                    var a_class = '';
                    $in_tmp_table = $(".menu_items[level='-1']").find('li[cat_id=' + data[i].id + ']');
                    if ($in_tmp_table.length) a_class = 'category_in_tmp';


                    if ((data[i].subQuan) == 1) desc = 'ITEM';
                    else if ((data[i].subQuan < 5) && (data[i].subQuan > 0)) desc = "ITEMS";
                    else desc = "ITEMS";

                    desc = (data[i].subQuan / 1) + ' ' + desc;
                    if (data_level >= 4) desc = '';
                    str += '<li class="menu_category ' + a_class + '" cat_id="' + data[i].id + '" menu_level="' + data_level + '">' +
                        '<div class="menu_title">' + data[i].Name + '</div>' +
                        '<div class="menu_subtitle">' + desc + '</div>' +
                        '<div class="level_subtitle">' + data_level + '</div>' +

                        '</li>';
                }

                $next.attr('parent_id', ID);
                $next.html(str);
                $next.sortable({
                    revert: true
                });

                var zindex = 800;

                $next.find('li').each(function () {
                    $(this).draggable({
                        containment: "body",
                        handle: ".menu_items[level='-1']",
                        scroll: true,
                        connectToSortable: ".menu_items",
                        cursor: 'move',
                        revert: function (event, ui) {
                            $(this).css("border", "none");
                            return !event;
                        },
                        start: function (event, ui) {
                            $(this).css("z-index", zindex++);
                            $(this).css("border", "2px solid #333");
                        }

                    });

                });

            }, 'json');

    });


    $('.menu_items[level=-1]').sortable({
        revert: true
    });
    $(this).find('.menu_items').each(function () {
        var level = parseInt($(this).attr('level'));
        if (level != 0) {
            var accept_str = '';
            if (level < 0)		// jeśli to tabela tymczasowa do przerzucania kategorii
            {
                $(this).droppable({
                    hoverClass: "over",
                    accept: '.menu_category',
                    drop: function (event, ui) {
                        var item_level = ui.draggable.attr('menu_level');
                        var cat_id = ui.draggable.attr('cat_id');

                        $("<li class='menu_category' menu_level=" + item_level + " cat_id=" + cat_id + "></li>").html(ui.draggable.html()).appendTo(this);
                        $(ui.draggable).remove();


                    }
                });

            }
            else 			// jeśli to tabela główna któregoś z poziomów
            {

                $(this).droppable({
                    hoverClass: "over",
                    accept: '.menu_category[menu_level="' + level + '"]',
                    drop: function (event, ui) {
                        var item_level = ui.draggable.attr('menu_level');
                        var cat_id = ui.draggable.attr('cat_id');
                        var parent_id = $(this).attr('parent_id');
                        // usuń jeśli istnieje już jakaś kopia w tej kategorii
                        $(this).find('li[cat_id=' + cat_id + '].category_in_tmp').remove();
                        $("<li class='menu_category' menu_level=" + item_level + " cat_id=" + cat_id + "></li>").html(ui.draggable.html()).appendTo(this);
                        $(ui.draggable).remove();

                        $in_tmp_table = $(".menu_items[level='-1'").find('li[cat_id=' + cat_id + ']');
                        if ($in_tmp_table.length) $(this).find('li[cat_id=' + cat_id + ']').addClass('category_in_tmp');

                        var query_line = 'MOVE_MENU_CATEGORY=' + cat_id + '&MOVE_TO=' + parent_id;
                        xhr = $.post(BDIR + 'query/admin',
                            query_line,
                            function (data) {
                                1;

                            }, 'json');


                    }
                });

            }


        }
    });


});
