document.addEventListener('DOMContentLoaded', () => {
  const navigation = document.querySelector('.bee-docs-nav');
  const sections = [...document.querySelectorAll('.bee-docs-section[id]')];
  const links = new Map(
    [...document.querySelectorAll('.bee-docs-nav a[href*="#"]')].map((link) => [
      decodeURIComponent(new URL(link.href, window.location.href).hash.slice(1)),
      link,
    ])
  );

  const activateSection = (id) => {
    links.forEach((link, sectionId) => {
      const active = sectionId === id;
      link.classList.toggle('active', active);

      if (active) {
        link.setAttribute('aria-current', 'location');
        link.scrollIntoView({ block: 'nearest', inline: 'nearest' });
      } else {
        link.removeAttribute('aria-current');
      }
    });
  };

  if (navigation && sections.length > 0 && 'IntersectionObserver' in window) {
    const visibleSections = new Map();
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          visibleSections.set(entry.target.id, entry.intersectionRatio);
        } else {
          visibleSections.delete(entry.target.id);
        }
      });

      const active = [...visibleSections.entries()].sort((a, b) => b[1] - a[1])[0];
      if (active) {
        activateSection(active[0]);
      }
    }, { rootMargin: '-15% 0px -60% 0px', threshold: [0, 0.1, 0.35, 0.6] });

    sections.forEach((section) => observer.observe(section));
    activateSection(window.location.hash.slice(1) || sections[0].id);
  }

  const escapeHtml = (value) => value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;');

  const keywordPattern = /\b(?:async|await|class|const|declare|else|extends|false|final|function|if|implements|namespace|new|null|private|protected|public|readonly|return|static|string|true|use|void)\b/;
  const tokenPattern = /(\/\/[^\n]*|#[^\n]*|\/\*[\s\S]*?\*\/|'(?:\\.|[^'\\])*'|"(?:\\.|[^"\\])*"|\b(?:async|await|class|const|declare|else|extends|false|final|function|if|implements|namespace|new|null|private|protected|public|readonly|return|static|string|true|use|void)\b|\b\d+(?:\.\d+)?\b)/g;

  document.querySelectorAll('[data-language]').forEach((code) => {
    const source = code.textContent ?? '';
    let cursor = 0;
    let highlighted = '';

    source.replace(tokenPattern, (token, _match, offset) => {
      highlighted += escapeHtml(source.slice(cursor, offset));
      let type = 'number';
      if (token.startsWith('//') || token.startsWith('#') || token.startsWith('/*')) type = 'comment';
      else if (token.startsWith("'") || token.startsWith('"')) type = 'string';
      else if (keywordPattern.test(token)) type = 'keyword';
      highlighted += `<span class="bee-code-${type}">${escapeHtml(token)}</span>`;
      cursor = offset + token.length;
      return token;
    });

    code.innerHTML = highlighted + escapeHtml(source.slice(cursor));
  });

  document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-code]');
    if (!button) return;

    const code = button.closest('[data-code-block]')?.querySelector('code');
    if (!code) return;

    const label = button.querySelector('span');
    try {
      const source = code.textContent ?? '';
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(source);
      } else {
        const textarea = document.createElement('textarea');
        textarea.value = source;
        textarea.setAttribute('readonly', '');
        textarea.className = 'position-fixed opacity-0';
        document.body.append(textarea);
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
      }
      button.classList.add('is-copied');
      if (label) label.textContent = 'Copiado';
      window.setTimeout(() => {
        button.classList.remove('is-copied');
        if (label) label.textContent = 'Copiar';
      }, 1800);
    } catch (error) {
      if (label) label.textContent = 'No disponible';
    }
  });
});
