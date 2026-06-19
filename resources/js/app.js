import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const prefetchedUrls = new Set();
const excludedPrefetchPaths = [
    '/admin',
    '/organization',
    '/user',
    '/staff',
    '/auth',
    '/cart',
    '/checkout',
];

function canPrefetch(link) {
    if (!link || link.target || link.hasAttribute('download') || link.dataset.noPrefetch !== undefined) {
        return false;
    }

    const url = new URL(link.href, window.location.href);

    return url.origin === window.location.origin
        && url.protocol.startsWith('http')
        && !url.hash
        && !excludedPrefetchPaths.some((path) => url.pathname.startsWith(path))
        && !prefetchedUrls.has(url.href);
}

function prefetchLink(link) {
    if (!canPrefetch(link)) return;
    if (navigator.connection?.saveData || ['slow-2g', '2g'].includes(navigator.connection?.effectiveType)) return;

    const url = new URL(link.href, window.location.href);
    prefetchedUrls.add(url.href);

    const hint = document.createElement('link');
    hint.rel = 'prefetch';
    hint.href = url.href;
    hint.as = 'document';
    document.head.appendChild(hint);
}

document.addEventListener('pointerover', (event) => {
    const link = event.target.closest?.('a[href]');
    if (link) prefetchLink(link);
}, { passive: true });

document.addEventListener('focusin', (event) => {
    const link = event.target.closest?.('a[href]');
    if (link) prefetchLink(link);
});

if ('serviceWorker' in navigator && window.isSecureContext) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').catch(() => {});
    }, { once: true });
}
