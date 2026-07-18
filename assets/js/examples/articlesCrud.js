(() => {
  'use strict';

  const root = document.querySelector('#articles-crud');
  if (!root) return;

  const apiUrl = root.dataset.apiUrl;
  const form = document.querySelector('#article-form');
  const elements = {
    id: document.querySelector('#article-id'),
    title: document.querySelector('#article-title'),
    slug: document.querySelector('#article-slug'),
    excerpt: document.querySelector('#article-excerpt'),
    content: document.querySelector('#article-content'),
    status: document.querySelector('#article-status'),
    statusFilter: document.querySelector('#articles-status-filter'),
    tableBody: document.querySelector('#articles-table-body'),
    empty: document.querySelector('#articles-empty'),
    alert: document.querySelector('#articles-alert'),
    loading: document.querySelector('#articles-loading'),
    submit: document.querySelector('#article-submit'),
    cancel: document.querySelector('#article-cancel'),
    formTitle: document.querySelector('#article-form-title'),
    previous: document.querySelector('#articles-previous'),
    next: document.querySelector('#articles-next'),
    page: document.querySelector('#articles-page'),
  };
  const state = { articles: [], page: 1, lastPage: 1, busy: false };

  const request = async (url, options = {}) => {
    const response = await fetch(url, {
      ...options,
      headers: { Accept: 'application/json', ...options.headers },
    });
    const contentType = response.headers.get('content-type') || '';
    const payload = response.status === 204
      ? null
      : contentType.includes('application/json')
        ? await response.json()
        : { message: await response.text() };
    if (!response.ok) {
      throw new Error(payload?.message || `Error HTTP ${response.status}`);
    }
    return payload;
  };

  const showAlert = (message, type = 'success') => {
    elements.alert.textContent = message;
    elements.alert.className = `alert alert-${type}`;
  };

  const setBusy = (busy) => {
    state.busy = busy;
    elements.loading.classList.toggle('d-none', !busy);
    elements.submit.disabled = busy;
  };

  const resetForm = () => {
    form.reset();
    elements.id.value = '';
    elements.formTitle.textContent = 'Nuevo artÃ­culo';
    elements.submit.textContent = 'Guardar';
    elements.cancel.classList.add('d-none');
  };

  const startEditing = (article) => {
    elements.id.value = article.id;
    elements.title.value = article.title || '';
    elements.slug.value = article.slug || '';
    elements.excerpt.value = article.excerpt || '';
    elements.content.value = article.content || '';
    elements.status.value = article.status || 'draft';
    elements.formTitle.textContent = `Editar artÃ­culo #${article.id}`;
    elements.submit.textContent = 'Actualizar';
    elements.cancel.classList.remove('d-none');
    elements.title.focus();
  };

  const actionButton = (label, className, handler) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = className;
    button.textContent = label;
    button.addEventListener('click', handler);
    return button;
  };

  const renderRows = () => {
    elements.tableBody.replaceChildren();
    elements.empty.classList.toggle('d-none', state.articles.length !== 0);
    state.articles.forEach((article) => {
      const row = document.createElement('tr');
      const title = document.createElement('td');
      const status = document.createElement('td');
      const views = document.createElement('td');
      const actions = document.createElement('td');
      title.textContent = article.title;
      status.textContent = article.status;
      views.textContent = article.views;
      actions.className = 'text-end text-nowrap';
      actions.append(
        actionButton('Editar', 'btn btn-sm btn-outline-primary me-2', () => startEditing(article)),
        actionButton('Eliminar', 'btn btn-sm btn-outline-danger', () => removeArticle(article)),
      );
      row.append(title, status, views, actions);
      elements.tableBody.append(row);
    });
    elements.page.textContent = `PÃ¡gina ${state.page} de ${state.lastPage}`;
    elements.previous.disabled = state.busy || state.page <= 1;
    elements.next.disabled = state.busy || state.page >= state.lastPage;
  };

  const loadArticles = async () => {
    setBusy(true);
    try {
      const url = new URL(apiUrl, window.location.origin);
      url.searchParams.set('page', state.page);
      url.searchParams.set('per_page', '10');
      if (elements.statusFilter.value) url.searchParams.set('status', elements.statusFilter.value);
      const payload = await request(url);
      state.articles = payload.data;
      state.lastPage = payload.last_page;
      renderRows();
    } catch (error) {
      showAlert(error.message, 'danger');
    } finally {
      setBusy(false);
      renderRows();
    }
  };

  const removeArticle = async (article) => {
    if (!window.confirm(`Â¿Eliminar "${article.title}"?`)) return;
    setBusy(true);
    try {
      const body = new URLSearchParams({ _method: 'DELETE' });
      await request(`${apiUrl}/${article.id}`, { method: 'POST', body });
      showAlert('ArtÃ­culo eliminado.');
      await loadArticles();
    } catch (error) {
      showAlert(error.message, 'danger');
    } finally {
      setBusy(false);
    }
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!form.reportValidity() || state.busy) return;
    setBusy(true);
    try {
      const id = elements.id.value;
      const body = new URLSearchParams(new FormData(form));
      if (id) body.set('_method', 'PATCH');
      await request(id ? `${apiUrl}/${id}` : apiUrl, { method: 'POST', body });
      showAlert(id ? 'ArtÃ­culo actualizado.' : 'ArtÃ­culo creado.');
      resetForm();
      state.page = id ? state.page : 1;
      await loadArticles();
    } catch (error) {
      showAlert(error.message, 'danger');
    } finally {
      setBusy(false);
    }
  });

  elements.cancel.addEventListener('click', resetForm);
  elements.statusFilter.addEventListener('change', () => { state.page = 1; loadArticles(); });
  elements.previous.addEventListener('click', () => { if (state.page > 1) { state.page -= 1; loadArticles(); } });
  elements.next.addEventListener('click', () => { if (state.page < state.lastPage) { state.page += 1; loadArticles(); } });

  loadArticles();
})();
