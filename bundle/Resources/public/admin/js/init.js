(function($) {
    'use strict';
    $('.multientry').multientry();

    const saveButton = document.getElementById('content_type_edit__sidebar_right__save-tab');

    const observer = new MutationObserver(e => {
        $('.multientry').multientry();
    });

    // enables observer when elements is dropped
    document.addEventListener("drop", e => {
        document.querySelectorAll('.ibexa-collapse').forEach(el => {
            observer.observe(el, { childList: true, subtree: true });
        });
    });

    // disable observer while dragging to reduce function firing
    document.addEventListener("drag", e => {
        document.querySelectorAll('.ibexa-collapse').forEach(el => {
            observer.disconnect();
        });
    });

    // checks if any multientry inputs are empty and expands the field type
    saveButton && saveButton.addEventListener("click", e => {
        document.querySelectorAll('.multientry input').forEach(el => {
            if (el.value.length === 0) {
                e.preventDefault();
                const fullElement = el.closest('.ibexa-collapse');
                const elementBody = fullElement.querySelector('.ibexa-collapse__body');
                const collapseToggle = fullElement.querySelector('.ibexa-collapse__toggle-btn');
                fullElement.classList.contains('multientry-error') ? null : fullElement.classList.add('multientry-error');
                fullElement.classList.contains('ibexa-collapse--collapsed') ? fullElement.classList.remove('ibexa-collapse--collapsed') : null;
                elementBody.classList.contains('show') ? null : elementBody.classList.add('show');
                collapseToggle.classList.contains('collapsed') ? elementBody.classList.remove('collapsed') : null;
                fullElement.dataset.collapsed ? fullElement.dataset.collapsed = false : null;
            }
        })
    });
})(jQuery);
