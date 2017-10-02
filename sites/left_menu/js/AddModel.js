$(window).ready(function () {


    /*$.ajax({
                url: BDIR+'downloads/Add',
                data: $(".tab_container")
            }).done(function(data) {
                $("<link/>", {
                    rel: "stylesheet",
                    type: "text/css",
                    href: BDIR+"sites/download/download.css"
                }).appendTo("head");
                $('.upload-model').replaceWith($(data).find('.tab_container'));


            });*/


    $(".AddModelNewCheckBox input").on('change', function () {
        $input_div = $(this).closest('.AddModelField').children('.new_model_input');
        $select = $(this).closest('.AddModelField').children('.AddSelect');
        var level = $(this).closest('.AddModelField').attr('level');
        $input = $input_div.children('input');
        var NewModel = $(this).attr('checked') == 'checked';


        if (NewModel) {
            $input_div.show();
            $input.val('');
            $select.hide();
            $select.val(-1);


            $('.AddModelField').each(function (index) {
                if (level < index) $('.AddModelField[level=' + index + ']').hide();
                if ($(this).attr('level') > level) {


                }
            });

        }
        else {
            $input_div.hide();
            $select.show();


            $('.AddModelField').each(function (index) {
                if ($(this).attr('level') > level) {
                    $(this).find('.IsANewModel').show();
                }
            });

        }

        $('.AddModelField').each(function (index) {
            if ($(this).attr('level') > level) {
                $(this).find('input[type=text]').val('');
            }


        });

        var next_level = parseInt(level) + 1;
        $("input[type=text][level=5]").trigger('keyup');


    });

    $("input[type=text][level=5]").on('keyup', function () {
        if ($(this).val() != '')
            $('input[type="submit"]').prop('disabled', false);
        else $('input[type="submit"]').prop('disabled', true);

    });

    // pole tekstowe przy wprowadzaniu nowych wartości
    $(".new_model_input input").on('keyup', function () {
        $Field = $(this).closest('.AddModelField');
        $this = $(this);
        var next_level = parseInt($Field.attr('level')) + 1;
        var prev_level = parseInt($Field.attr('level')) - 1;
        $prev = $(".AddModelField[level=" + prev_level + "]");
        var prev_value;
        var match_array;
        // test czy poprzednie pole jest z select-a czy pola tekstowego
        if ($prev.find('select').val() == -1) prev_value = $prev.find('input').val();
        else prev_value = $prev.find('select').val();


        if ($(this).val().length == 0)
            $('.AddModelField').each(function (index) {
                //	alert($(this).attr('level'));
                if ($(this).attr('level') >= next_level) {
                    $(this).find('input[type=text]').val('');
                    $(this).fadeOut();
                }
            });

        // Jeśli pole ma więcej niż jeden znak
        if ($(this).val().length > 0) {
            $('.AddModelField[level=' + next_level + ']').fadeIn();
            // sprawdzanie czy istnieje już podobna grupa


            xhr = $.post(BDIR + 'query/menu',
                "CHECK_CATEGORY=" + $(this).val() + "&PREV_NAME=" + prev_value,
                function (data) {
                    if (data.result == 'FOUND') {
                        match_array = data.matches.split(',');
                        $this.autocomplete({
                            source: match_array
                        });

                    }

                }, 'json');
        }
    });


    // pobieranie kolejnych wpisów
    $('.AddSelect').on('change', function () {

        var category = $(this).val();
        /*console.log(category);*/
        var level = parseInt($(this).attr('level')) + 1;
        $loading = $(this).closest('.AddModelField').find('.loading_img');
        var $next = $('.AddSelect[level=' + level + ']');
        var content = '<option value="-1">Choose..</option>';
        $loading.fadeIn()
        $next_field = $('.AddModelField[level=' + level + ']');
        $next_field.fadeIn();

        $next_field.find('input[type=checkbox]').prop('checked', false);
        $next_field.find('input[type=checkbox]').trigger('change');
        query_line = 'GET_MENU=' + category + '&step=' + level;

        xhr = $.post(BDIR + 'query/menu',
            query_line,
            function (data) {
                // wyłączamy selecty które są dalej niż uaktualniony
                $('.AddModelField').each(function (index) {
                    if ($(this).attr('level') > level) {
                        $select = $(this).find('select');
                        $select.val('-1');
                        $select.html(content);

                    }
                });


                $next.empty();
                for (var i = 0; i < data.length; i++)
                    content += '<OPTION value="' + data[i].id + '">' + data[i].Name + '</OPTION>';


                $next.removeAttr('disabled');
                $next.html(content);
                //$next.labselect($next.attr('sel'));

                //$next.find('option:contains("'+$next.attr('sel')+'")').prop('selected', 'selected');
                $next.find('option[value="' + $next.attr('sel') + '"]').prop('selected', 'selected');


                if ($next.is('[sel]')) {

                    if ($next.val() != -1)
                        $next.trigger('change');
                    $next.removeAttr('sel');

                }

                if (level == DetailDepth)
                    $this.attr('listning', 0);


                $loading.fadeOut();

            }, 'json');

    });

    // Zapobieganie zatwierdzania formularza enterm
    $(document).on("keypress", "form", function (event) {
        return event.keyCode != 13;
    });


    $('#AddMenuTrigger').trigger('change');
    setTimeout(function () {
        if ($('#' + 'cat_' + window.location.pathname.match(/\d+/)[0] + ' .levelHolderClass')[0].dataset.level == 6) {
            $(".FilterInputClass").css('display', 'none');
            $(".levelHolderClass ul").css('top', '35px');
        }
    }, 0);

})