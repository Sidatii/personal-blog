import './bootstrap';
import './dark-mode';
import Alpine from 'alpinejs';
import 'htmx.org';
import renderMathInElement from 'katex/contrib/auto-render';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const prose = document.querySelector('.prose');
    if (!prose) return;

    // Collapse <br> tags inside $$...$$ blocks inserted by the soft_break renderer
    prose.querySelectorAll('p').forEach(function (p) {
        if (p.innerHTML.includes('$$')) {
            p.innerHTML = p.innerHTML.replace(/\$\$([\s\S]*?)\$\$/g, function (match) {
                return match.replace(/<br\s*\/?>/gi, '\n');
            });
        }
    });

    renderMathInElement(prose, {
        delimiters: [
            { left: '$$',  right: '$$',  display: true  },
            { left: '$',   right: '$',   display: false },
            { left: '\\[', right: '\\]', display: true  },
            { left: '\\(', right: '\\)', display: false },
        ],
        throwOnError: false,
    });
});
