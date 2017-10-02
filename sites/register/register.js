$(document).ready(function () {
    if (navigator.appVersion.indexOf("Chrome/") != -1) {
        $("#bday").css('padding-left', '28px');
        $("#bmonth").css('padding-left', '20px');
        $("#byear").css('padding-left', '28px');
    } else {
        $('#Country').css('height', '47px');
        $('#Country').css('padding-top', '20px');

    }
    $("#TermsAgree").click(function () {

        $("#RegisterMe").prop('disabled', !$(this).prop('checked'));
        if ($(this).prop('checked')) {
            $("#RegisterMe").addClass('register-hooverable');
        } else $("#RegisterMe").removeClass('register-hooverable');
    });

    // Sprawdzanie poprawności adresu email
    $("#email").keyup(function () {
            var $INPUT = $(this)
            var xhr;

            xhr = $.post(BDIR + 'query/login',
                'CHECK_EMAIL_ADDRESS=' + $(this).val(),
                function (data) {

                    if (parseInt(data.DATA) == 0) {
                        $INPUT.attr("VALIDITY", "GOOD");
                        $('#EmailMsg').hide();
                        $('#EmailMsg').empty();
                        $INPUT.removeClass('bad_input').addClass('good_input');
                    }
                    else if (parseInt(data.DATA) > 0) {
                        $INPUT.attr("VALIDITY", "BAD");
                        $('#EmailMsg').text("Email alredy registered");
                        $INPUT.removeClass('good_input').addClass('bad_input');

                    }
                    else if (data.DATA == 'BAD') {
                        $INPUT.attr("VALIDITY", "BAD");
                        $('#EmailMsg').text("Email format is incorrect");
                        $INPUT.removeClass('good_input').addClass('bad_input');

                    }
                    $('#EmailMsg').fadeIn();

                }, 'json');
            if ($("#email").val() != '') {
                $(".email-header").removeClass('anim-typewriter-hide');
                $(".email-header").addClass('anim-typewriter');
            } else {
                $(".email-header").removeClass('anim-typewriter');
                $(".email-header").addClass('anim-typewriter-hide');
            }


        }
    );


    /* Walidacja hasła w czasie rzeczywistym */
    $("#password, #password_retype").keyup(function () {
        var $pass = $('#password');
        var $retype = $('#password_retype');

        if ($pass.val() != $retype.val()) {
            $('#PassMsg').html("Passwords must be this this");
            $('#PassMsg').fadeIn();
            $pass.removeClass('good_input').addClass('bad_input');
            $retype.removeClass('good_input').addClass('bad_input');
            $pass.attr("VALIDITY", "BAD");
            $retype.attr("VALIDITY", "BAD");
        }

        else if ($pass.val().length < 6) {
            $('#PassMsg').html("Password is too short");
            $('#PassMsg').fadeIn();
            $pass.removeClass('good_input').addClass('bad_input');
            $retype.removeClass('good_input').addClass('bad_input');
            $pass.attr("VALIDITY", "BAD");
            $retype.attr("VALIDITY", "BAD");
        }
        else {
            $('#PassMsg').html("");
            $('#PassMsg').fadeOut();
            $pass.addClass('good_input').removeClass('bad_input');
            $retype.addClass('good_input').removeClass('bad_input');
            $pass.attr("VALIDITY", "GOOD");
            $retype.attr("VALIDITY", "GOOD");
        }


    });
    $("#password").keyup(function () {
        if ($("#password").val() != '') {
            $(".password-header").removeClass('anim-typewriter-hide');
            $(".password-header").addClass('anim-typewriter');
        } else {
            $(".password-header").removeClass('anim-typewriter');
            $(".password-header").addClass('anim-typewriter-hide');
        }


    });
    $("#password_retype").keyup(function () {
        if ($("#password_retype").val() != '') {
            $(".retype-header").removeClass('anim-typewriter-hide');
            $(".retype-header").addClass('anim-typewriter');
        } else {
            $(".retype-header").removeClass('anim-typewriter');
            $(".retype-header").addClass('anim-typewriter-hide');
        }


    });

    // Sprawdzanie dostępności Ksywki
    $("#Nick").keyup(function () {
        var $INPUT = $(this)
        var xhr;
        xhr = $.post(BDIR + 'query/login',
            'CHECK_NICK=' + $(this).val(),
            function (data) {

                if (parseInt(data.DATA) == 0) {
                    $INPUT.attr("VALIDITY", "GOOD");
                    $('#NickMsg').hide();
                    $('#NickMsg').empty();
                    $INPUT.removeClass('bad_input').addClass('good_input');
                }
                else if (parseInt(data.DATA) > 0) {
                    $INPUT.attr("VALIDITY", "BAD");
                    $('#NickMsg').text("Nick already exists");
                    $INPUT.removeClass('good_input').addClass('bad_input');

                }

                if ($INPUT.val().length < 2) {
                    $INPUT.attr("VALIDITY", "BAD");
                    $('#NickMsg').text("At least 2 chars");
                    $INPUT.removeClass('good_input').addClass('bad_input');

                }

                $('#NickMsg').fadeIn();

            }, 'json');
        if ($("#Nick").val() != '') {
            $(".nick-header").removeClass('anim-typewriter-hide');
            $(".nick-header").addClass('anim-typewriter');
        } else {
            $(".nick-header").removeClass('anim-typewriter');
            $(".nick-header").addClass('anim-typewriter-hide');
        }
    });


    $("#City").keyup(function () {
        if ($("#City").val() != '') {
            $(".city-header").removeClass('anim-typewriter-hide');
            $(".city-header").addClass('anim-typewriter');
        } else {
            $(".city-header").removeClass('anim-typewriter');
            $(".city-header").addClass('anim-typewriter-hide');
        }
    });
    $("#Country").keyup(function () {
        if ($("#Country").val() != '') {
            $(".country-header").removeClass('anim-typewriter-hide');
            $(".country-header").addClass('anim-typewriter');
        } else {
            $(".country-header").removeClass('anim-typewriter');
            $(".country-header").addClass('anim-typewriter-hide');
        }
    });

    $("#register_form").submit(function (event) {
        $("#Nick").keyup();
        $("#password").keyup();
        $("#email").keyup();
        // Walidacja formularza rejestracjis
        var matches = 0;
        $("input").each(function (i, val) {
            if ($(this).attr("VALIDITY") == 'BAD') {
                matches++;
            }
        });

        if (matches > 0) {
            event.preventDefault();
        }


    });


});