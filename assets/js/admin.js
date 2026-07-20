document.addEventListener('DOMContentLoaded', () => {
  const adminShell = document.querySelector('.bee-admin-shell');
  const sidebarToggle = document.querySelector('[data-bee-sidebar-toggle]');

  if (!adminShell || !sidebarToggle) {
    return;
  }

  const storageKey = 'bee.admin.sidebar.collapsed';
  const desktopMedia = window.matchMedia('(min-width: 992px)');

  const readPreference = () => {
    try {
      return window.localStorage.getItem(storageKey) === 'true';
    } catch (error) {
      return false;
    }
  };

  const storePreference = (collapsed) => {
    try {
      window.localStorage.setItem(storageKey, String(collapsed));
    } catch (error) {
      // The control remains functional when storage is unavailable.
    }
  };

  const render = (collapsed) => {
    const isCollapsed = desktopMedia.matches && collapsed;
    const label = isCollapsed ? 'Abrir navegación' : 'Cerrar navegación';

    adminShell.classList.toggle('is-sidebar-collapsed', isCollapsed);
    sidebarToggle.setAttribute('aria-expanded', String(!isCollapsed));
    sidebarToggle.setAttribute('aria-label', label);
    sidebarToggle.setAttribute('title', label);
  };

  let isCollapsed = readPreference();
  render(isCollapsed);

  sidebarToggle.addEventListener('click', () => {
    isCollapsed = !adminShell.classList.contains('is-sidebar-collapsed');
    storePreference(isCollapsed);
    render(isCollapsed);
  });

  desktopMedia.addEventListener('change', () => render(isCollapsed));
});
