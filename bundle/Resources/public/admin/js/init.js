(function($) {
    'use strict';
    $('.multientry').multientry();

    const observer = new MutationObserver(e => {
        $('.multientry').multientry();
    });

    document.addEventListener("drop", e => {
        document.querySelectorAll('.ibexa-collapse').forEach(el => {
            observer.observe(el, { childList: true, subtree: true });
        });
    });

    document.addEventListener("drag", e => {
        document.querySelectorAll('.ibexa-collapse').forEach(el => {
            observer.disconnect();
        });
    });
})(jQuery);
