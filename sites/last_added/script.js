var maxPaginationItems;
jQuery(document).ready(function () {

    if ($(window).height() < 1200) maxPaginationItems = 12;
    if ($(window).height() < 1100) maxPaginationItems = 11;
    if ($(window).height() < 1000) maxPaginationItems = 10;
    if ($(window).height() < 950) maxPaginationItems = 9;
    if ($(window).height() < 850) maxPaginationItems = 8;
    if ($(window).height() < 800) maxPaginationItems = 7;
    if ($(window).height() < 650) maxPaginationItems = 6;

    $('.article-status:contains(NEW)').css('opacity', '0');
    $('.article-status:contains(ACCEPTED)').css('color', '#e0b03e');
    var mainContent = $('.cd-main-content'),
        header = $('.cd-main-header'),
        sidebar = $('.cd-side-nav'),
        sidebarTrigger = $('.cd-nav-trigger'),
        topNavigation = $('.cd-top-nav'),
        searchForm = $('.cd-search'),
        accountInfo = $('.account');

    //on resize, move search and top nav position according to window width
    var resizing = false;
    moveNavigation();
    $(window).on('resize', function () {
        if (!resizing) {
            (!window.requestAnimationFrame) ? setTimeout(moveNavigation/*, 300*/) : window.requestAnimationFrame(moveNavigation);
            resizing = true;
        }
    });

    //on window scrolling - fix sidebar nav
    var scrolling = false;
    checkScrollbarPosition();

    $(window).on('scroll', function () {
        if (!scrolling) {
            (!window.requestAnimationFrame) ? setTimeout(checkScrollbarPosition/*, 300*/) : window.requestAnimationFrame(checkScrollbarPosition);
            scrolling = true;
        }
    });


    //mobile only - open sidebar when user clicks the hamburger menu
    sidebarTrigger.on('click', function (event) {
        event.preventDefault();
        $([sidebar, sidebarTrigger]).toggleClass('nav-is-visible');

    });

    $('#LastAddTypeList a').click(function () {
        // $('#selector_div li').html('<form action="'+BDIR+'" name="ChangeLastAdded" method="post" style="display:none;"><input type="text" name="TypeOfLastAddedContent" value="'+$(this).attr('class')+'" /></form>');
        // document.forms['ChangeLastAdded'].submit();
        var str = '';
        var index = 0;
        var picked_cat = $(this).attr('class');
        var picked_text = $(this).html();

        xhr = $.post(BDIR + 'query/lastadded', "LAST_ADDED_CONTENT=" + picked_cat,
            function (data) {
                $container = $('.last-articles');
                $container.empty();


                for (var i = 0; i < data.item_count; i++) {
                    index++;
                    var division = data.items[i].DIVISION;
                    if (division == 'BOA') {
                        division = 'BOA VIE'
                    }
                    if (division == 'KBC') {
                        division = 'KBC EC'
                    }
                    str += '<div id="article' + index + '" class="article-list">' +
                        '<div class="category-box ' + data.items[i].ClassName + '"></div>' +
                        '<div class="specs">' + data.items[i].Specs + '</div>' +
                        '<div class="likes">' +
                        '<p class="article-status"></p>'


                        +
                        '   <a href="' + BDIR + data.items[i].link + '" class="more">> Read more</a>' +
                        '</div>' +
                        '</div>';


                }
                $container.html(str);
                setMaxArticles(1, maxPaginationItems);


            }, 'json');

    });

    //click on item and show submenu
    $('.has-children > a').on('click', function (event) {

        var mq = checkMQ(),
            selectedItem = $(this);
        if (mq == 'mobile' || mq == 'tablet') {
            event.preventDefault();
            if (selectedItem.parent('li').hasClass('selected')) {
                selectedItem.parent('li').removeClass('selected');
            } else {
                sidebar.find('.has-children.selected').removeClass('selected');
                accountInfo.removeClass('selected');
                selectedItem.parent('li').addClass('selected');
            }
        }
    });

    //click on account and show submenu - desktop version only
    accountInfo.children('a').on('click', function (event) {
        var mq = checkMQ(),
            selectedItem = $(this);
        if (mq == 'desktop') {
            event.preventDefault();
            accountInfo.toggleClass('selected');
            sidebar.find('.has-children.selected').removeClass('selected');
        }
    });

    $(document).on('click', function (event) {
        if (!$(event.target).is('.has-children a')) {
            sidebar.find('.has-children.selected').removeClass('selected');
            accountInfo.removeClass('selected');
        }
    });


    function checkMQ() {
        //check if mobile or desktop device
        return window.getComputedStyle(document.querySelector('.cd-main-content'), '::before').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
    }

    function moveNavigation() {
        var mq = checkMQ();

        if (mq == 'mobile' && topNavigation.parents('.cd-side-nav').length == 0) {
            detachElements();
            topNavigation.appendTo(sidebar);
            searchForm.removeClass('is-hidden').prependTo(sidebar);
        } else if (( mq == 'tablet' || mq == 'desktop') && topNavigation.parents('.cd-side-nav').length > 0) {
            detachElements();
            searchForm.insertAfter(header.find('.cd-logo'));
            topNavigation.appendTo(header.find('.cd-nav'));
        }
        checkSelected(mq);
        resizing = false;
    }

    function detachElements() {
        topNavigation.detach();
        searchForm.detach();
    }

    function checkSelected(mq) {
        //on desktop, remove selected class from items selected on mobile/tablet version
        if (mq == 'desktop') $('.has-children.selected').removeClass('selected');
    }

    function checkScrollbarPosition() {
        var mq = checkMQ();

        if (mq != 'mobile') {
            var sidebarHeight = sidebar.outerHeight(),
                windowHeight = $(window).height(),
                mainContentHeight = mainContent.outerHeight(),
                scrollTop = $(window).scrollTop();

            ( ( scrollTop + windowHeight > sidebarHeight ) && ( mainContentHeight - sidebarHeight != 0 ) ) ? sidebar.addClass('is-fixed').css('bottom', 0) : sidebar.removeClass('is-fixed').attr('style', '');
        }
        scrolling = false;
    }

    $("select").find("option[value='info']").addClass("info");
    $("select").find("option[value='img']").addClass("img");
    $("select").find("option[value='bios']").addClass("bios");
    $("select").find("option[value='oth']").addClass("oth");
    $("select").find("option[value='sol']").addClass("sol");
    $("select").find("option[value='boa']").addClass("boa");
    $("select").find("option[value='sch']").addClass("sch");
    $("select").find("option[value='tot']").addClass("tot");
    $("select").find("option[value='kbc']").addClass("kbc");
    $("select").find("option[value='soft']").addClass("soft");

    var trigger = $('.category_selector');
    var list = $('.categories');

    trigger.click(function () {
        trigger.toggleClass('active');
        list.slideToggle(200);
    });

    //this is optional to close the list while the new page is loading
    list.click(function () {
        trigger.click();
    });
    setMaxArticles(1, maxPaginationItems);
    displayPagination(9);
    $('#page1').addClass('active');
    $(document).mouseup(function (e) {
        var btnExpand = $('.category_selector');

        if (!list.is(e.target) // if the target of the click isn't the container...
            && list.has(e.target).length === 0 // ... nor a descendant of the container
            && !btnExpand.is(e.target)) {
            list.hide();
        }
    });


});


function setMaxArticles(page, max) {
    $('.article-list').hide();
    $('.pag').removeClass('active');
    $('#page' + page).addClass('active');
    for (let j = (page - 1) * max + 1; j <= (page - 1) * max + max; j++) {
        $('#article' + j).show();

    }
}

function displayPagination(pages) {

    for (let i = 1; i <= pages; i++) {
        $('#pagination ul').append('<li><a class="pag" id="page' + i + '" href="#" onclick="setMaxArticles(' + i + ',' + maxPaginationItems + ')">' + i + '</a></li>');
    }

    $('#pagination ul').append('<li>...</li>');

}
