document.addEventListener('DOMContentLoaded', () => {
  const currentDocumentUrl = `${window.location.pathname}${window.location.search}`;

  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    const fragment = anchor.getAttribute('href');

    if (!fragment || fragment === '#') {
      return;
    }

    anchor.setAttribute('href', `${currentDocumentUrl}${fragment}`);
  });
});
