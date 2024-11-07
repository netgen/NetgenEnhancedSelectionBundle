const init = () => {
  window.initaliseMultientries();

  const observer = new MutationObserver(() => {
    window.initaliseMultientries();
  });

  // enables observer when element is dropped
  document.addEventListener('drop', () => {
    document.querySelectorAll('.ibexa-collapse').forEach((element) => {
      observer.observe(element, { childList: true, subtree: true });
    });
  });

  // disable observer while dragging to reduce function firing
  document.addEventListener('drag', () => {
    document.querySelectorAll('.ibexa-collapse').forEach(() => {
      observer.disconnect();
    });
  });

  // checks if any multientry inputs are empty and expands the field type
  const saveButton = document.getElementById('content_type_edit__sidebar_right__save-tab');

  saveButton && saveButton.addEventListener('click', (event) => {
    document.querySelectorAll('.multientry input').forEach((input) => {
      if (input.value.length > 0) {
        return;
      }

      event.preventDefault();

      const collapseWrapper = input.closest('.ibexa-collapse');
      const collapseBody = collapseWrapper.querySelector('.ibexa-collapse__body');
      const collapseToggle = collapseWrapper.querySelector('.ibexa-collapse__toggle-btn');

      collapseWrapper.classList.add('multientry-error');
      collapseWrapper.classList.remove('ibexa-collapse--collapsed');
      collapseWrapper.dataset.collapsed = false;

      collapseBody.classList.add('show');

      if (collapseToggle.classList.contains('collapsed')) {
        collapseBody.classList.remove('collapsed');
      }
    });
  });
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
