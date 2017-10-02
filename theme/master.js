var PERM = {
    GUEST: 0,
    USER: 5,
    MOD: 10,
    ADMIN: 15
}

function isInt(n) {
    return Number(n) === n && n % 1 === 0;
}

Array.prototype.remove = function (from, to) {
    var rest = this.slice((to || from) + 1 || this.length);
    this.length = from < 0 ? this.length + from : from;
    return this.push.apply(this, rest);
};

var USER = {
    Nick: '',
    Perm: 0,
    CheckPerm: function (PERM) {
        return (PERM <= this.Perm);
    }
}

function in_array(needle, haystack) {
    for (var i in haystack) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

function AddLoadingBckgnd($item) {
    $item.css({
        "background": "url('" + BDIR + "images/loading2.gif') no-repeat scroll 95% 4px",
        "background-color": "white",
        "background-size": "18px"
    });
}

function ObjectLength_Modern(object) {
    return Object.keys(object).length;
}


$(document).ready(
    function () {


        // dopasowanie długości DIV'a menu do zawartości strony;
        var container_height = $(".main_container").css("height");
        var left_side_menu_min_height = $(".left_side_menu").css("min-height");
        if (container_height > left_side_menu_min_height)
            $(".left_side_menu").css("height", $(".main_container").css("height"));

        //	$(window).resize();


        $("[action=Navigate][arg]").live('click', function () {
            target = $(this).attr('target');
            if (target == '_blank')
                window.open($(this).attr("arg"), '_blank');
            else
                window.location = BDIR + $(this).attr("arg");

        });

        $("[not-empty]").live('focusout', function () {
            if ($(this).val() == '') {
                $(this).css('background-color', '#aa3333');
                ShowDialogBox("This field cannot be empty", "BAD");
                $(this).focus();
                $(this).animate({
                    backgroundColor: "#fff"
                }, 500);
                return false;
            }
            return true;

        });

        $("[intValue]").live('focusout', function () {
            if (!isInt($(this).val())) {
                $(this).val('0');
                $(this).css('background-color', '#aa3333');
                ShowDialogBox("This isn't integer !", "BAD");
                $(this).focus();
                $(this).animate({
                    backgroundColor: "#fff"
                }, 500);

            }
        });


            $(".AddTabUpload").click(function(){
                $(".pacman-icon").animate({
                    left: '270px'}, 2350);
                    $("#dot1").delay(200).fadeOut(100);
                    $("#dot2").delay(400).fadeOut(100);
                    $("#dot3").delay(600).fadeOut(100);
                    $("#dot4").delay(800).fadeOut(100);
                    $("#dot5").delay(1000).fadeOut(100);
                    $("#dot6").delay(1200).fadeOut(100);
                    $("#dot7").delay(1400).fadeOut(50);
                    $("#dot8").delay(1600).fadeOut(50);
                    $("#dot9").delay(1800).fadeOut(50);
                    $("#dot10").delay(2000).fadeOut(50);

/*                $('.pacman-para').each(function () {
                    $(this).prop('Counter',0).animate({
                        Counter: $(this).text()
                    }, {
                        duration: 2400,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                });*/

                (function ($) {
                    $.fn.countTo = function (options) {
                        options = options || {};

                        return $(this).each(function () {
                            // set options for current element
                            var settings = $.extend({}, $.fn.countTo.defaults, {
                                from:            $(this).data('from'),
                                to:              $(this).data('to'),
                                speed:           $(this).data('speed'),
                                refreshInterval: $(this).data('refresh-interval'),
                                decimals:        $(this).data('decimals')
                            }, options);

                            // how many times to update the value, and how much to increment the value on each update
                            var loops = Math.ceil(settings.speed / settings.refreshInterval),
                                increment = (settings.to - settings.from) / loops;

                            // references & variables that will change with each update
                            var self = this,
                                $self = $(this),
                                loopCount = 0,
                                value = settings.from,
                                data = $self.data('countTo') || {};

                            $self.data('countTo', data);

                            // if an existing interval can be found, clear it first
                            if (data.interval) {
                                clearInterval(data.interval);
                            }
                            data.interval = setInterval(updateTimer, settings.refreshInterval);

                            // initialize the element with the starting value
                            render(value);

                            function updateTimer() {
                                value += increment;
                                loopCount++;

                                render(value);

                                if (typeof(settings.onUpdate) == 'function') {
                                    settings.onUpdate.call(self, value);
                                }

                                if (loopCount >= loops) {
                                    // remove the interval
                                    $self.removeData('countTo');
                                    clearInterval(data.interval);
                                    value = settings.to;

                                    if (typeof(settings.onComplete) == 'function') {
                                        settings.onComplete.call(self, value);
                                    }
                                }
                            }

                            function render(value) {
                                var formattedValue = settings.formatter.call(self, value, settings);
                                $self.html(formattedValue);
                            }
                        });
                    };

                    $.fn.countTo.defaults = {
                        from: 0,               // the number the element should start at
                        to: 0,                 // the number the element should end at
                        speed: 1000,           // how long it should take to count between the target numbers
                        refreshInterval: 100,  // how often the element should be updated
                        decimals: 0,           // the number of decimal places to show
                        formatter: formatter,  // handler for formatting the value before rendering
                        onUpdate: null,        // callback method for every time the element is updated
                        onComplete: null       // callback method for when the element finishes updating
                    };

                    function formatter(value, settings) {
                        return value.toFixed(settings.decimals);
                    }
                }(jQuery));

                jQuery(function ($) {
                    // custom formatting example
                    $('.count-number').data('countToOptions', {
                        formatter: function (value, options) {
                            return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
                        }
                    });

                    // start all the timers
                    $('.timer').each(count);

                    function count(options) {
                        var $this = $(this);
                        options = $.extend({}, options || {}, $this.data('countToOptions') || {});
                        $this.countTo(options);
                    }
                });

                });





            });



	



