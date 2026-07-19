(() => {
  'use strict';

  const tabs = document.querySelectorAll('[data-creator-tab]');
  const panels = document.querySelectorAll('[data-creator-panel]');
  const activateTab = (name) => {
    tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.creatorTab === name));
    panels.forEach((panel) => panel.classList.toggle('d-none', panel.dataset.creatorPanel !== name));
  };
  tabs.forEach((tab) => tab.addEventListener('click', () => activateTab(tab.dataset.creatorTab)));

  const modernFields = document.querySelector('[data-modern-route-fields]');
  document.querySelectorAll('input[name="type"]').forEach((input) => {
    input.addEventListener('change', () => modernFields?.classList.toggle('d-none', input.checked && input.value === 'legacy'));
  });

  const form = document.querySelector('[data-route-form]');
  const resetRouteForm = () => {
    form?.reset();
    if (form) form.elements.route_id.value = '';
  };
  document.querySelector('[data-route-reset]')?.addEventListener('click', resetRouteForm);

  document.querySelectorAll('[data-route-edit]').forEach((button) => {
    button.addEventListener('click', () => {
      if (!form) return;
      const route = JSON.parse(button.dataset.routeEdit);
      activateTab('routes');
      form.elements.route_id.value = route.id;
      form.elements.path.value = route.path;
      form.elements.name.value = route.name;
      form.elements.controller.value = route.controller;
      form.elements.action.value = route.action;
      form.elements.middleware.value = route.middleware.join(', ');
      form.querySelectorAll('input[name="methods[]"]').forEach((input) => {
        input.checked = route.methods.includes(input.value);
      });
      const constraint = Object.entries(route.constraints)[0] ?? ['', ''];
      form.elements.constraint_parameter.value = constraint[0];
      form.elements.constraint_expression.value = constraint[1];
      document.querySelector('#creator-route-editor')?.scrollIntoView({ behavior: 'smooth' });
    });
  });

  document.querySelectorAll('[data-route-delete]').forEach((deleteForm) => {
    deleteForm.addEventListener('submit', (event) => {
      if (!window.confirm('¿Eliminar esta ruta administrada?')) event.preventDefault();
    });
  });
})();
