var ready = (callback) => {
    if (document.readyState != "loading") callback();
    else document.addEventListener("DOMContentLoaded", callback);
}

ready(() => {
    if (window.jQuery) {
        $(window).resize(function () {
            video.resizeEmbeddedVideo();
        });

        /**
         * Video elemental
         * - transcript and resize js
         * @type {string}
         */
        var video = {
            setWidthTo: '.video__wrapper',

            init: function () {
                video.setEmbeddedVideoRatios();
                video.transcriptToggles();
            },

            transcriptToggles: function () {
                $('.video-transcription button').on('click', function () {
                    let $parent = $(this).parent();
                    let visible = 'video-transcription--visible';
                    let hidden = 'video-transcription--hidden';
                    let $transcription = $parent.find(
                        '.video-transcription__text'
                    );
                    if ($parent.hasClass(hidden)) {
                        $parent.addClass(visible).removeClass(hidden);
                        $transcription.slideDown().attr('aria-hidden', 'false');
                    } else {
                        $parent.addClass(hidden).removeClass(visible);
                        $transcription.slideUp().attr('aria-hidden', 'true');
                    }
                });
            },

            getVideos: function () {
                return $(video.setWidthTo + ' iframe');
            },

            setEmbeddedVideoRatios: function () {
                $('iframe[src*="youtube"]').each(function () {
                    var src = $(this).attr('src');
                    if (src.indexOf('?') > 0) {
                        var arrSrc = src.split('?');
                        src = arrSrc[0];
                    }
                    $(this).attr('src', src + '?rel=0&enablejsapi=1');
                });

                video.getVideos().each(function (i) {
                    var $iframe = $(this);
                    var h, w;
                    if ($iframe.attr('height') && $iframe.attr('width')) {
                        w = $iframe.attr('width');
                        h = $iframe.attr('height');
                    } else {
                        w = $iframe.width();
                        h = $iframe.height();
                    }
                    var dataAspectRatio = h / w;
                    $iframe
                        .attr('data-aspect-ratio', dataAspectRatio)
                        .removeAttr('height')
                        .removeAttr('width')
                        .addClass('sized');
                });
                video.resizeEmbeddedVideo();
            },

            resizeEmbeddedVideo: function () {
                video.getVideos().each(function () {
                    var $vid = $(this);
                    var $ws = $vid.closest(video.setWidthTo);
                    var w = $ws.outerWidth();
                    var ap = $vid.attr('data-aspect-ratio');
                    $vid.width(w).height(w * ap);
                });
            },
        };

        video.init();
    }
});
