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
    syncCustomExpression();
  };
  document.querySelector('[data-route-reset]')?.addEventListener('click', resetRouteForm);

  const expressionPreset = document.querySelector('[data-route-expression-preset]');
  const customExpression = document.querySelector('[data-route-expression-custom]');
  const syncCustomExpression = () => {
    customExpression?.classList.toggle('d-none', expressionPreset?.value !== 'custom');
  };
  expressionPreset?.addEventListener('change', syncCustomExpression);

  const expressionPresets = {
    '\\d+': 'numeric',
    '[A-Za-z]+': 'alpha',
    '[A-Za-z0-9]+': 'alphanumeric',
    '[a-z0-9]+(?:-[a-z0-9]+)*': 'slug',
    '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}': 'uuid',
  };

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
      expressionPreset.value = constraint[1] ? (expressionPresets[constraint[1]] ?? 'custom') : '';
      syncCustomExpression();
      document.querySelector('#creator-route-editor')?.scrollIntoView({ behavior: 'smooth' });
    });
  });

  document.querySelectorAll('[data-route-delete]').forEach((deleteForm) => {
    deleteForm.addEventListener('submit', (event) => {
      if (!window.confirm('¿Eliminar esta ruta administrada?')) event.preventDefault();
    });
  });

  const fieldName = document.querySelector('[data-model-field-name]');
  const fieldType = document.querySelector('[data-model-field-type]');
  const fieldList = document.querySelector('[data-model-field-list]');
  const addFieldButton = document.querySelector('[data-model-field-add]');
  const modelFields = new Map();

  const renderModelFields = () => {
    if (!fieldList) return;
    fieldList.replaceChildren();
    if (modelFields.size === 0) {
      const emptyRow = document.createElement('tr');
      emptyRow.innerHTML = '<td class="text-center text-secondary py-3" colspan="3">Agrega la primera columna.</td>';
      fieldList.append(emptyRow);
      return;
    }
    modelFields.forEach((type, name) => {
      const row = document.createElement('tr');
      const nameCell = document.createElement('td');
      const typeCell = document.createElement('td');
      const actionCell = document.createElement('td');
      const hidden = document.createElement('input');
      const remove = document.createElement('button');
      hidden.type = 'hidden';
      hidden.name = 'fields[]';
      hidden.value = `${name}:${type}`;
      nameCell.textContent = name;
      nameCell.append(hidden);
      typeCell.innerHTML = `<code>${type}</code>`;
      actionCell.className = 'text-end';
      remove.type = 'button';
      remove.className = 'btn btn-sm btn-outline-danger';
      remove.dataset.modelFieldRemove = name;
      remove.setAttribute('aria-label', `Eliminar columna ${name}`);
      remove.innerHTML = '<i class="fas fa-trash" aria-hidden="true"></i>';
      actionCell.append(remove);
      row.append(nameCell, typeCell, actionCell);
      fieldList.append(row);
    });
  };

  const addModelField = () => {
    const name = fieldName?.value.trim() ?? '';
    if (!/^[A-Za-z_][A-Za-z0-9_]*$/.test(name)) {
      fieldName?.setCustomValidity('Usa un nombre de columna válido.');
      fieldName?.reportValidity();
      return;
    }
    fieldName.setCustomValidity('');
    modelFields.set(name, fieldType?.value ?? 'string');
    fieldName.value = '';
    renderModelFields();
    fieldName.focus();
  };

  addFieldButton?.addEventListener('click', addModelField);
  fieldName?.addEventListener('input', () => fieldName.setCustomValidity(''));
  fieldName?.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      event.preventDefault();
      addModelField();
    }
  });
  fieldList?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-model-field-remove]');
    if (!button) return;
    modelFields.delete(button.dataset.modelFieldRemove);
    renderModelFields();
  });
})();
