var PreparedMenu = '';
var lowestMenuName = '';
var arrMenu = '';
var DetailDepth = 6;
var breadCrump = [];
var breadId = [];

function breadcrumpAction(e) {


    var breadLength = $(".breadcrumb")[0].childElementCount;
    var breadNumber = e.target.className;

    if ((breadLength != breadNumber) && (breadNumber != 1)) {
        for (var i = 0; i <= breadLength - breadNumber; i++) {
            breadCrump.pop();
            breadId.pop();
        }
        $('.DIVISION_NAME').hide();
        $('#menu').find('#cat_' + e.target.id + ' a:first').trigger('click');
    }
    if (breadNumber == 1) {
        for (var i = 0; i <= breadLength - breadNumber; i++) {
            breadCrump.pop();
            breadId.pop();
        }
        $('.DIVISION_NAME').hide();
        $('#menu').find('#catid_' + e.target.id + ' a:first').trigger('click');
    }


}

function loadBreadcrump() {
    setTimeout(function () {
        var loopId = 'cat_' + window.location.pathname.match(/\d+/)[0];
        breadCrump.push($('#' + loopId + ' a:first').text());
        breadId.push(loopId.match(/\d+/)[0])

        for (var i = 0; i < 6; i++) {
            par = $('#' + loopId).parent().closest('li')[0].id;
            loopId = par;

            if (/cat_/g.test(par)) {

                breadCrump.push($('#' + loopId + ' a:first').text());
                breadId.push(loopId.match(/\d+/)[0])


            }
            if (/catid_/g.test(par)) {
                breadCrump.push($('#' + loopId + ' a:first').text());
                breadId.push(loopId.match(/\d+/)[0])
                break;
            }

        }
        breadCrump.reverse();
        breadId.reverse();

        for (var i = 0; i < breadCrump.length; i++) {
            $(".breadcrumb").append("<li><a class='" + (i + 1) + "' href='#' id='" + breadId[i] + "' onclick='breadcrumpAction(event)'>" + breadCrump[i] + "</a></li>");


        }
    }, 20);

}

function browserBreadcrump() {
    setTimeout(function () {
        breadId = JSON.parse("[" + $('.hidden-cat-tree').text() + "]")[0];

        for (var i = 0; i < breadId.length; i++) {
            if (i == 0) breadCrump.push($('#catid_' + breadId[i] + ' a:first').text());
            else breadCrump.push($('#cat_' + breadId[i] + ' a:first').text());

        }
        for (var i = 0; i < breadCrump.length; i++) {
            $(".breadcrumb").append("<li><a class='" + (i + 1) + "' href='#' id='" + breadId[i] + "' onclick='breadcrumpAction(event)'>" + breadCrump[i] + "</a></li>");


        }
    }, 20);
}


$(document).ready(function () {

    if (/addmodel/gi.test(window.location.pathname)) loadBreadcrump();
    if (/browser/gi.test(window.location.pathname)) browserBreadcrump();
    if (/solutions/gi.test(window.location.pathname)) browserBreadcrump();



    $('.FilterInputClass').live("click", function (e) {
        $(this).find('input').val('');
        $(this).find('input').focus();
        e.stopPropagation();
        e.preventDefault();

    })

    $('.FilterInputClass input').live("keyup", function (e) {
        $ul = $(this).parent().next('ul');
        var text = $(this).val().toLowerCase();
        if (text.length == 0) {
            $ul.find('li').show(500);
            return 0;
        }

        $ul.children('li').each(function (index, item) {
            var title = $(item).find('.sorting').text().toLowerCase();

            if (title.indexOf(text) > -1) $(item).show(500);
            else $(item).hide(500);

        });

    });


    $('.FilterInputClass').live("mousemove", function (e) {

        e.stopPropagation();
        e.preventDefault();
    })

    // JS Array implementation, overlap mode
    arrMenu = default_menu;

    if (PreparedMenu.length != 0) {
        //	arrMenu = [PreparedMenu];
        //	arrMenu[0].items.concat(menu_const_btn);

        for (var i = 0; i < arrMenu[0].items.length; i++)
            if (arrMenu[0].items[i].id == PreparedMenu.ParentID)
                arrMenu[0].items[i].items[0].items = PreparedMenu.items;

    }


    $('#menu').multilevelpushmenu({
        menu: arrMenu,
        backText: 'Back',
        FilterInputClass: 'FilterInputClass',
        onItemClick: function () {
            var event = arguments[0],
                $menuLevelHolder = arguments[1],
                $item = arguments[2],
                options = arguments[3],
                title = $menuLevelHolder.find('h2:first').text(),
                itemName = $item.find('a:first').text();
            var link = $item.find('a:first').prop('href');
            window.location.href = link;

            document.getElementsByClassName(".levelHolderClass").style.backgroundColor = "blue";


        },

        onBackItemClick: function () {
            $('.DIVISION_NAME').hide();
            $(".breadcrumb").html('');
            breadCrump.pop();
            breadCrump.pop();
            breadId.pop();
            breadId.pop();
            for (var i = 0; i < breadCrump.length; i++) {
                $(".breadcrumb").append("<li><a class='" + (i + 1) + "' href='#' id='" + breadId[i] + "' onclick='breadcrumpAction(event)'>" + breadCrump[i] + "</a></li>");


            }
            var event = arguments[0],
                $menuLevelHolder = arguments[1],
                options = arguments[2],
                title = $menuLevelHolder.find('h2:first').text();
            var $parent = $menuLevelHolder.closest('ul').closest('li');
            var $addTo = $('#menu').find('.levelHolderClass:first');
            if ($parent.text() != '')
                $parent.find('a:first').trigger('click');
            else {
                //$( '#menu ul:first').empty();
                //var $addTo = $('#menu').find('.levelHolderClass:first');


            }

            if ($parent.length == 0) {
                $('.add_new_btn').css('display', 'none');

            } else $('.add_new_btn').css('display', 'block');

        },

        onMenuReady: function () {

            if (lowestMenuName.length)
                $('#menu').multilevelpushmenu('expand', lowestMenuName);


        },

        onTitleItemClick: function () {

            alert('kliklo');

        },
        onGroupItemClick: function (e) {

            var event = arguments[0],
                $menuLevelHolder = arguments[1],
                $item = arguments[2],
                options = arguments[3],
                title = $menuLevelHolder.find('h2:first').text(),
                itemName = $item.find('a:first').text();


            ajaxMainpage(e.currentTarget.pathname, e.currentTarget.text.replace(/\s/g, ''));

            function ajaxMainpage(path, item) {
                if (/downloads|article/g.test(path) && $('.adver-container').length == 0) {
                    if (/NOTEBOOK|PC|Tablet/g.test(item)) {
                        $.ajax({
                            url: BDIR,
                            data: $(".content")
                        }).done(function (data) {
                            $("<link/>", {
                                rel: "stylesheet",
                                type: "text/css",
                                href: BDIR + "sites/homepage/main.css"
                            }).appendTo("head");
                            setTimeout(function () {
                                $('.content').replaceWith($(data).find('.content'));
                                $('.content').css('background-image', 'url("' + BDIR + '/images/background.jpg")');
                                $('.content').css('background-size', 'contain');
                                for (var i = 0; i < breadCrump.length; i++) {
                                    $(".breadcrumb").append("<li><a class='" + (i + 1) + "' href='#' id='" + breadId[i] + "' onclick='breadcrumpAction(event)'>" + breadCrump[i] + "</a></li>");

                                }
                            }, 100);

                        });

                    }
                }

            }


            breadCrump.push(itemName);
            breadId.push($item.prop('id').split('_')[1]);
            $(".breadcrumb").html('');
            for (var i = 0; i < breadCrump.length; i++) {
                $(".breadcrumb").append("<li><a class='" + (i + 1) + "' href='#' id='" + breadId[i] + "' onclick='breadcrumpAction(event)'>" + breadCrump[i] + "</a></li>");

            }


            // select category
            var mi = document.createElement("input");
            mi.setAttribute('type', 'text');
            mi.setAttribute('value', 'default');
            $menuLevelHolder.find('.filter_input').val('');
            var data_level = parseInt($item.find('[data-level]:first').data('level'));
            var arr = $item.prop('id').split('_');
            var ID = arr[1];
            var query_line = 'GET_MENU=' + ID + '&step=' + data_level;
            xhr = $.post(BDIR + 'query/menu',
                query_line,
                function (data) {

                    NewItems = [];
                    $item;
                    $item.find('ul').empty();
                    var $addTo = $item.find('.levelHolderClass:first');

                    if (data.length == 0)
                        $('#menu').multilevelpushmenu('additems', GetVoidItem(itemName), $addTo, 0);

                    for (var i = 0; i < data.length; i++) {

                        var link = '#';

                        if (data_level == DetailDepth) {
                            link = BDIR + data[i].Page + data[i].link;
                            NewItems.unshift({
                                id: 'cat_' + data[i].id,
                                icon: 'fa fa-desktop',
                                name: data[i].Name,
                                link: link
                            });
                        }
                        else

                            NewItems.push({
                                id: 'cat_' + data[i].id,
                                icon: 'fa fa-desktop',
                                className: 'sorting',
                                name: data[i].Name,
                                link: link,
                                items: [GetVoidItem(data[i].Name)]
                            });
                    }

                    var $path_obj = $('#menu').multilevelpushmenu('pathtoroot', $menuLevelHolder);
                    // zbieramy scieżkę dla przycisku dodawania
                    var path_link = '';
                    $.each($path_obj, function (v) {

                        if (v > 0) path_link += $(this).find('h2:first').text() + '/';

                    });
                    path_link += $item.find('h2:first').text();

                    // przycisk Dodaj na końcu każdego menu

                    $('.add_new_btn').attr("href", BDIR + 'AddModel/' + ID);
                    $('.add_new_btn').css('display', 'block');


                    $('#menu').multilevelpushmenu('additems', NewItems, $addTo, 0);
                    //add input box


                }, 'json');

            if (data_level != 6) {
                $(".FilterInputClass").css('display', 'block');
                $(".levelHolderClass ul").css('top', '56px');
            }
            var inp = $(".filter_input");
            switch (data_level) {
                case 1:
                    inp.attr('placeholder', 'Search producent');
                    break;
                case 2:
                    inp.attr('placeholder', 'Search model');
                    break;
                case 3:
                    inp.attr('placeholder', 'Search model number');
                    break;
                case 4:
                    inp.attr('placeholder', 'Search motherboard');
                    break;
                case 5:
                    inp.attr('placeholder', 'Search revision');
                    break;
                case 6:
                    $(".FilterInputClass").css('display', 'none');
                    $(".levelHolderClass ul").css('top', '35px');

                default:
                    break;

            }


        },

        mode: 'cover',
        swipe: 'desktop'
    });
    var icons = {
        notebook: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48.752px" viewBox="0 0 30.752 28.004" style="enable-background:new 0 0 30.752 28.004;" xml:space="preserve"> <path d="M7.036,18.314h16.68V7.722H7.036V18.314z M28.311,20.027h-1.515V5.289 c0-0.377-0.306-0.682-0.683-0.682H4.639c-0.377,0-0.683,0.305-0.683,0.682v14.738H2.44c-0.377,0-0.682,0.305-0.682,0.682 c0,1.481,1.207,2.688,2.691,2.688h21.852c1.484,0,2.691-1.206,2.691-2.688C28.993,20.332,28.688,20.027,28.311,20.027z M25.08,18.995c0,0.376-0.305,0.682-0.682,0.682H6.354c-0.377,0-0.683-0.306-0.683-0.682V7.041c0-0.376,0.306-0.682,0.683-0.682 h18.044c0.377,0,0.682,0.306,0.682,0.682V18.995z"/> </svg>',
        pc: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48.752px" viewBox="0 0 30.752 28.004" style="enable-background:new 0 0 30.752 28.004;" xml:space="preserve"> <path d="M8.258,19.692v-6.081H2.283c-0.418,0-0.756-0.337-0.756-0.754c0-0.416,0.338-0.754,0.756-0.754 h5.976v-1.941H2.283c-0.418,0-0.756-0.338-0.756-0.754c0-0.417,0.338-0.755,0.756-0.755h5.976V7.25 c0-0.187,0.027-0.366,0.074-0.538h-6.05c-0.418,0-0.756-0.337-0.756-0.754c0-0.416,0.338-0.754,0.756-0.754h7.509 c0.067,0,0.131,0.012,0.194,0.028c0.105-0.018,0.213-0.028,0.324-0.028h1.765V3.092c0-0.417-0.338-0.754-0.756-0.754H0.756 C0.338,2.338,0,2.675,0,3.092v21.818c0,0.417,0.338,0.755,0.756,0.755h10.563c0.417,0,0.756-0.338,0.756-0.755v-3.173H10.31 C9.179,21.738,8.258,20.82,8.258,19.692z M6.038,21.256c-0.644,0-1.166-0.521-1.166-1.163c0-0.642,0.522-1.162,1.166-1.162 c0.644,0,1.166,0.521,1.166,1.162C7.203,20.736,6.681,21.256,6.038,21.256z M30.213,6.712H10.31c-0.297,0-0.539,0.24-0.539,0.537 v12.442c0,0.297,0.242,0.538,0.539,0.538h19.903c0.297,0,0.539-0.241,0.539-0.538V7.25C30.752,6.953,30.51,6.712,30.213,6.712z M24.697,24.157h-2.016v-2.419h-4.839v2.419h-2.017c-0.418,0-0.756,0.338-0.756,0.755c0,0.416,0.338,0.754,0.756,0.754h2.772 h3.327h2.772c0.418,0,0.756-0.338,0.756-0.754C25.454,24.495,25.115,24.157,24.697,24.157z"/>  </svg>',
        tablet: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48.752px" viewBox="0 0 30.752 28.004" style="enable-background:new 0 0 30.752 28.004;" xml:space="preserve"> <path d="M8.735,15.977h8.816c0.368,0,0.666-0.299,0.666-0.667c0-0.369-0.298-0.668-0.666-0.668H8.735 c-0.367,0-0.665,0.299-0.665,0.668C8.07,15.678,8.368,15.977,8.735,15.977z M8.735,13.019h12.58c0.368,0,0.665-0.299,0.665-0.668 s-0.297-0.668-0.665-0.668H8.735c-0.367,0-0.665,0.299-0.665,0.668S8.368,13.019,8.735,13.019z M8.735,10.474h13.338 c0.368,0,0.666-0.299,0.666-0.668v-5.07c0-0.369-0.298-0.668-0.666-0.668H8.735c-0.367,0-0.665,0.299-0.665,0.668v5.07 C8.07,10.175,8.368,10.474,8.735,10.474z M8.735,18.936h11.188c0.368,0,0.665-0.299,0.665-0.668c0-0.368-0.297-0.667-0.665-0.667 H8.735c-0.367,0-0.665,0.299-0.665,0.667C8.07,18.637,8.368,18.936,8.735,18.936z M25.071,0.075H5.681 c-0.735,0-1.334,0.601-1.334,1.339V26.59c0,0.739,0.599,1.338,1.334,1.338h19.39c0.736,0,1.334-0.6,1.334-1.338V1.414 C26.405,0.676,25.807,0.075,25.071,0.075z M15.376,26.208c-0.462,0-0.837-0.376-0.837-0.841c0-0.464,0.375-0.841,0.837-0.841 c0.463,0,0.838,0.377,0.838,0.841S15.839,26.208,15.376,26.208z M24.197,23.031H6.555V2.597h17.642V23.031z M8.735,21.895h11.188 c0.368,0,0.665-0.299,0.665-0.668c0-0.368-0.297-0.668-0.665-0.668H8.735c-0.367,0-0.665,0.3-0.665,0.668 C8.07,21.596,8.368,21.895,8.735,21.895z"/></svg>',
        tutorials: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48.752px" viewBox="0 0 30.752 28.004" style="enable-background:new 0 0 30.752 28.004;" xml:space="preserve"> <path d="M29.104,23.751l-5.486-5.487l3.914-2.262c0.307-0.177,0.484-0.513,0.459-0.865 s-0.25-0.659-0.578-0.79l-9.248-3.689c0.178-0.693,0.27-1.404,0.27-2.127c0-4.703-3.825-8.53-8.527-8.53 C5.203,0,1.378,3.827,1.378,8.53c0,4.704,3.825,8.53,8.527,8.53c0.723,0,1.435-0.092,2.127-0.271l3.688,9.251 c0.132,0.328,0.438,0.553,0.79,0.578c0.354,0.025,0.688-0.152,0.865-0.458l2.261-3.915l5.486,5.487 c0.173,0.174,0.407,0.271,0.651,0.271c0.245,0,0.479-0.098,0.652-0.271l2.677-2.677C29.464,24.695,29.464,24.111,29.104,23.751z M9.957,7.383C9.615,7.246,9.225,7.326,8.964,7.587S8.623,8.238,8.76,8.581l1.169,2.932c-1.646-0.022-2.991-1.374-3.005-3.021 C6.918,7.698,7.22,6.955,7.775,6.399c0.55-0.55,1.283-0.852,2.067-0.852c0.009,0,0.017,0,0.024,0 c1.646,0.014,2.997,1.359,3.02,3.004L9.957,7.383z M16.435,9.967l-1.752-0.699c0.034-0.235,0.051-0.474,0.049-0.713 c-0.021-2.654-2.196-4.83-4.85-4.852c-0.013,0-0.025,0-0.039,0c-1.276,0-2.473,0.494-3.371,1.393 C5.564,6.003,5.07,7.215,5.08,8.507c0.021,2.653,2.196,4.829,4.85,4.851c0.013,0,0.024,0,0.037,0 c0.228,0,0.454-0.018,0.678-0.049l0.698,1.751c-0.47,0.104-0.95,0.156-1.438,0.156c-3.686,0-6.684-2.999-6.684-6.686 S6.22,1.845,9.906,1.845c3.686,0,6.684,2.999,6.684,6.686C16.59,9.017,16.537,9.497,16.435,9.967z"/> </svg>',
        software: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="48.752px" viewBox="0 0 30.752 28.004" style="enable-background:new 0 0 30.752 28.004;" xml:space="preserve"> <path d="M26.025,18.226l-1.297,0.002c-0.229,0-0.449,0.09-0.608,0.248 c-0.391,0.389-0.913,0.604-1.472,0.604c-1.131,0.001-2.053-0.883-2.054-1.97c0-1.088,0.918-1.973,2.047-1.975 c0.559-0.001,1.083,0.212,1.475,0.599c0.159,0.157,0.378,0.247,0.607,0.246l1.296-0.001c0.225-0.001,0.44-0.087,0.598-0.239 c0.158-0.153,0.248-0.359,0.248-0.575c0,0,0,0,0-0.001l-0.01-6.512c0-0.216-0.09-0.423-0.249-0.574 c-0.159-0.152-0.375-0.238-0.599-0.238l-7.43,0.012h-1.562l-0.001-0.768V7.082c0-0.31,0.131-0.605,0.362-0.822 c0.563-0.524,0.874-1.227,0.874-1.976V4.267c-0.002-1.524-1.29-2.764-2.874-2.771c-1.584,0.007-2.872,1.246-2.874,2.771v0.017 c0,0.749,0.311,1.451,0.874,1.976c0.231,0.217,0.362,0.513,0.362,0.822v0.003l-0.001,0.768h-1.562L4.745,7.84 c-0.225,0-0.44,0.086-0.599,0.238C3.987,8.23,3.897,8.437,3.897,8.653l-0.01,6.512c0,0.001,0,0.001,0,0.001 c0,0.216,0.089,0.422,0.247,0.575c0.158,0.152,0.374,0.238,0.598,0.239l1.297,0.001c0.229,0.001,0.448-0.089,0.607-0.246 c0.392-0.387,0.915-0.6,1.474-0.599c1.129,0.002,2.047,0.887,2.047,1.975c-0.001,1.087-0.923,1.971-2.054,1.97 c-0.558-0.001-1.081-0.216-1.471-0.604c-0.16-0.158-0.379-0.248-0.608-0.248l-1.297-0.002c-0.467-0.001-0.846,0.362-0.847,0.812 l0.009,6.649v0.001c0,0.448,0.378,0.813,0.845,0.813l8.63,0.006c0.225,0,0.44-0.085,0.599-0.237 c0.159-0.152,0.248-0.359,0.249-0.575l0.001-0.689c0-0.001,0-0.001,0-0.002c0-0.22-0.093-0.431-0.257-0.584 c-0.4-0.373-0.621-0.872-0.621-1.404v-0.011c0.002-1.083,0.917-1.964,2.042-1.969c1.125,0.005,2.04,0.886,2.042,1.969v0.011 c0,0.532-0.221,1.031-0.621,1.404c-0.164,0.153-0.257,0.364-0.257,0.584c0,0.001,0,0.001,0,0.002l0.001,0.689 c0,0.216,0.09,0.423,0.249,0.575c0.159,0.152,0.375,0.237,0.599,0.237l8.63-0.006c0.467-0.001,0.845-0.365,0.845-0.813v-0.001 l0.009-6.649C26.872,18.588,26.493,18.225,26.025,18.226z"/> </svg>'
    };
    $(".one").prepend(icons.notebook);
    $(".two").prepend(icons.pc);
    $(".three").prepend(icons.tablet);
    $(".four").prepend(icons.software);
    $(".five").prepend(icons.tutorials);

    $('#menu_multilevelpushmenu .levelHolderClass h2:first').remove();
    $('#catid_4').append('<div class="outline"><h2>TUTORIALS</h2></div>');
    $('#catid_5').append('<div class="outline"><h2>SOFTWARE</h2></div>');
});


// JS Aray instead HTML Markup
function GetVoidItem(name) {

    var voidItem = {
        title: name,
        icon: 'fa fa-desktop',
        items: [
            {
                name: name,
                icon: 'fa fa-phone-square',
                link: '#'
            }

        ]
    };
    return voidItem;
}

function GetAddItem(link, category) {

    var AddItem = {

        name: 'Add new',
        icon: 'fa fa-plus',
        className: 'add_new_btn',
        link: BDIR + 'AddModel/' + link
    };


    return AddItem;

}


var default_menu = [

    {
        title: '',
        icon: 'fa fa-reorder',
        items: [
            {
                id: 'catid_1',
                className: 'one arrow-right',
                name: 'NOTEBOOK',
                link: '#',
                items: [
                    {
                        title: 'NOTEBOOK',
                        link: '#',
                        items: [{
                            icon: 'fa fa-laptop',
                            cat_id: 1,
                            name: 'None',
                            link: '#'
                        }]

                    }
                ]
            },

            {
                id: 'catid_2',
                className: 'two arrow-right',
                name: 'PC',
                link: '#',
                items: [
                    {
                        title: 'PC',
                        link: '#',
                        items: [{

                            cat_id: 2,
                            name: 'None',
                            link: '#'
                        }]

                    }
                ]
            },
            {
                id: 'catid_3',
                className: 'three arrow-right',
                name: 'Tablet',
                link: '#',
                items: [
                    {
                        title: 'TABLET',
                        link: '#',
                        items: [{
                            cat_id: 3,
                            name: 'None',
                            link: '#'
                        }]

                    }
                ]
            },

            {
                id: 'catid_4',
                className: 'four',
                name: 'TUTORIALS',
                link: BDIR + 'article'

            },
            {
                id: 'catid_5',
                icon: 'material-icons',
                className: 'five',
                name: 'SOFTWARE',
                link: BDIR + 'downloads'
                /*items: [
                    {
                        title: 'Software',
                        link: '#',
                        items:[{
                            cat_id: 5,
                            name: 'None',
                            link: '#'
                        }]

                    }
                ]*/
            }
        ]
    }
];


