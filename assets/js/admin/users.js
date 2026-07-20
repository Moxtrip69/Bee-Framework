document.addEventListener('submit', (event) => {
  const form = event.target.closest('[data-confirm-form]');
  if (form && !window.confirm(form.dataset.confirmForm)) {
    event.preventDefault();
  }
});
