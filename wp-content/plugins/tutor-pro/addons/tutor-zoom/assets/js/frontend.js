(function ($) {
    'use strict';
    $(document).ready(function () {
        $('.tutor-zoom-meeting-countdown').each(function () {
            var date_time = $(this).data('timer');
            var timezone = $(this).data('timezone');
            var new_date = moment.tz(date_time, timezone);
            $(this).countdown(new_date.toDate(), function(event) {
                $(this).html(event.strftime('<div><h3>%D</h3><p>Days</p></div><div><h3>%H</h3><p>Hours</p></div><div><h3>%M</h3><p>Minutes</p></div><div><h3>%S</h3><p>Seconds</p></div>'));
            });
        })

        $('.tutor-zoom-lesson-countdown').each(function () {
            var date_time = $(this).data('timer');
            var timezone = $(this).data('timezone');
            var new_date = moment.tz(date_time, timezone);
            $(this).countdown(new_date.toDate(), function(event) {
                $(this).html(event.strftime('<span>%D <span>d</span></span> <span>%H <span>h</span></span> <span>%M <span>m</span></span> <span>%S <span>s</span></span>'));
            });
        })

        $('.tutor-zoom-meeting-detail').on('click', 'i.tutor-icon-copy', function(e) {
            e.stopPropagation();
            var $icon = $(this);
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($icon.parent().find('span').text()).select();
            document.execCommand("copy");
            $temp.remove();

            $icon.parent().append('<span class="tutor-copied-msg tutor-icon-mark"> Copied</span>').fadeIn(1000);
            setTimeout(function () {
                $icon.parent().find('.tutor-copied-msg').fadeOut(1000);
            }, 1000);
        });
    });
})(jQuery);