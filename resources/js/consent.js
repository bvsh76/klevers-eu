const COOKIE = 'klevers_consent';
const VERSION = 1;
const MAX_AGE = 60 * 60 * 24 * 180;

export function readConsent() {
    const match = document.cookie.match(/(?:^|;\s*)klevers_consent=([^;]*)/);
    if (!match) return null;
    try {
        const value = JSON.parse(decodeURIComponent(match[1]));
        return value && value.v === VERSION ? value : null;
    } catch {
        return null;
    }
}

function writeConsent({ analytics, external }) {
    const value = encodeURIComponent(JSON.stringify({ v: VERSION, analytics: !!analytics, external: !!external }));
    const secure = location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `${COOKIE}=${value}; Max-Age=${MAX_AGE}; Path=/; SameSite=Lax${secure}`;
}

function loadAnalytics() {
    const id = document.querySelector('meta[name="ga4-id"]')?.content;
    if (!id || window.__kleversGa) return;
    window.__kleversGa = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () {
        window.dataLayer.push(arguments);
    };
    window.gtag('consent', 'default', {
        ad_storage: 'denied',
        ad_user_data: 'denied',
        ad_personalization: 'denied',
        analytics_storage: 'denied',
    });
    window.gtag('consent', 'update', { analytics_storage: 'granted' });
    window.gtag('js', new Date());
    window.gtag('config', id);
    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
    document.head.appendChild(script);
}

// вставляет содержимое <template data-consent="..."> и заново создаёт <script>, чтобы они выполнились
export function activate(category) {
    document.querySelectorAll(`template[data-consent="${category}"]`).forEach((template) => {
        const fragment = template.content.cloneNode(true);
        fragment.querySelectorAll('script').forEach((old) => {
            const script = document.createElement('script');
            for (const attr of old.attributes) script.setAttribute(attr.name, attr.value);
            script.text = old.textContent;
            old.replaceWith(script);
        });
        template.closest('[data-consent-block]')?.querySelector('[data-consent-placeholder]')?.remove();
        template.replaceWith(fragment);
    });
}

function clearAnalyticsCookies() {
    const host = location.hostname;
    const parts = host.split('.');
    const domains = ['', host, `.${host}`];
    for (let i = 1; i < parts.length - 1; i++) domains.push(`.${parts.slice(i).join('.')}`);
    document.cookie
        .split(';')
        .map((c) => c.trim().split('=')[0])
        .filter((name) => /^(_ga|_gid)/.test(name))
        .forEach((name) => domains.forEach((d) => {
            document.cookie = `${name}=; Max-Age=0; Path=/${d ? `; Domain=${d}` : ''}`;
        }));
}

function apply(consent) {
    if (consent.analytics) loadAnalytics();
    if (consent.external) activate('external');
}

export default () => ({
    open: false,
    settings: false,
    analytics: false,
    external: false,

    init() {
        const saved = readConsent();
        if (saved) {
            this.analytics = saved.analytics;
            this.external = saved.external;
            apply(saved);
        } else {
            this.open = true;
        }
        window.addEventListener('consent-open', () => {
            this.open = true;
            this.settings = true;
        });
        window.addEventListener('consent-grant-external', () => {
            this.save({ analytics: this.analytics, external: true });
        });
    },

    acceptAll() {
        this.save({ analytics: true, external: true });
    },

    onlyNecessary() {
        this.save({ analytics: false, external: false });
    },

    saveSettings() {
        this.save({ analytics: this.analytics, external: this.external });
    },

    save(consent) {
        const previous = readConsent();
        writeConsent(consent);
        this.analytics = consent.analytics;
        this.external = consent.external;
        this.open = false;
        this.settings = false;
        const revoked = previous && ((previous.analytics && !consent.analytics) || (previous.external && !consent.external));
        if (revoked) {
            clearAnalyticsCookies();
            location.reload();
            return;
        }
        apply(consent);
    },
});