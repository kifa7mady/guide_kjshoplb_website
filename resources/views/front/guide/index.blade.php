@extends('front.guide')

@section('title', 'KJ Shop - دليل العبادية')
@section('meta_title', 'دليل العبادية - استكشف المتاجر والصيدليات والمحلات في البلدة')
@section('meta_description', ' اكتشف جميع المتاجر والصيدليات والمحلات في بلدة العبادية من خلال موقعنا الشامل. استعرض مختلف الفئات للعثور على كل ما تحتاجه في البلدة، في مكان واحد!')
@section('meta_keywords', 'المتاجر, الصيدليات, المحلات, بلدة العبادية, موقع شامل, الفئات, البلدة, تسوق, خدمات, احتياجات, دليل, بحث, أماكن, منتجات')

<!-- HTML -->

<style>
    main {
        overflow-y: auto;
        /* -webkit-overflow-scrolling: touch;  // if this causes issues, try removing it */
    }
</style>
<style>
    /* Smooth fade-in effect for lazy-loaded images */
    img.lazy {
        opacity: 0;
        transition: opacity 0.6s ease;
    }

    img.lazy.lazy-loaded {
        opacity: 1;
    }
</style>

<script>
    (() => {
        const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

        function promoteSources(img) {
            const pic = img.parentElement?.tagName === 'PICTURE' ? img.parentElement : null;
            if (pic) {
                pic.querySelectorAll('source[data-srcset]').forEach(s => {
                    s.srcset = s.dataset.srcset;
                    s.removeAttribute('data-srcset');
                });
            }
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
                img.removeAttribute('data-srcset');
            }
            if (img.dataset.src) {
                img.src = img.dataset.src + (img.dataset.src.includes('?') ? '&' : '?') + 't=' + Date.now();
                img.removeAttribute('data-src');
            }
            img.loading = 'eager';
        }

        async function upgrade(img) {
            if (img.classList.contains('lazy-loaded')) return;

            img.classList.add('lazy-loading');
            promoteSources(img);

            try {
                if (img.decode) await img.decode();
            } catch (e) {
                console.log('Image decode failed:', e);
            }

            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
        }

        function initLazyImages(rootEl = document) {
            const imgs = $$('img.lazy:not(.lazy-loaded)', rootEl);
            if (!imgs.length || !('IntersectionObserver' in window)) return;

            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        upgrade(img);
                        obs.unobserve(img);
                    }
                });
            }, {
                rootMargin: '400px 0px',
                threshold: 0.01
            });

            imgs.forEach(img => observer.observe(img));

            const mo = new MutationObserver(muts => {
                muts.forEach(m => m.addedNodes.forEach(n => {
                    if (n.nodeType === 1) {
                        if (n.matches?.('img.lazy:not(.lazy-loaded)')) observer.observe(n);
                        $$('img.lazy:not(.lazy-loaded)', n).forEach(img => observer.observe(img));
                    }
                }));
            });
            mo.observe(rootEl, { childList: true, subtree: true });

            return {
                refresh: () => $$('img.lazy:not(.lazy-loaded)', rootEl).forEach(img => observer.observe(img)),
                destroy: () => observer.disconnect()
            };
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => initLazyImages());
        } else {
            initLazyImages();
        }

        window.initLazyImages = initLazyImages;
    })();
</script>





<script>
    (() => {
        // ---- Routes emitted safely from Blade ----
        const HOME_URL      = @json(route('guide.homePage'));
        const CUSTOMER_TPL  = @json(route('guide.customerPage', ['id' => '__ID__']));

        // ---- Small utilities ----
        const $      = (sel, root = document) => root.querySelector(sel);
        const sleep  = (ms) => new Promise(r => setTimeout(r, ms));

        // Abortable GET that returns text (HTML). Times out automatically.
        async function fetchHTML(url, { timeoutMs = 15000, headers = {} } = {}) {
            const ac = new AbortController();
            const t  = setTimeout(() => ac.abort(), timeoutMs);

            try {
                const res = await fetch(url, {
                    method: 'GET',
                    headers: { Accept: 'text/html', ...headers },
                    credentials: 'include',
                    signal: ac.signal
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return await res.text();
            } finally {
                clearTimeout(t);
            }
        }

        // Simple page-level loader (append/remove), returned element is removable.
        function showLoader(target = $('main')) {
            const el = document.createElement('div');
            el.className = 'guide-loader';
            el.innerHTML = '<img src="/front/icons/icon-square-loader.svg" alt="" />';
            target.appendChild(el);
            return el;
        }

        // Replace all children of target with parsed HTML (scripts optional)
        function replaceWithHTML(target, html, { runScripts = false } = {}) {
            const frag = document.createRange().createContextualFragment(html);
            target.replaceChildren(frag);

            if (runScripts) {
                // Re-run inline scripts if your view returns any
                target.querySelectorAll('script').forEach(old => {
                    const s = document.createElement('script');
                    if (old.src) {
                        s.src = old.src;
                    } else {
                        s.textContent = old.textContent;
                    }
                    // copy type / attrs
                    if (old.type) s.type = old.type;
                    [...old.attributes].forEach(a => {
                        if (a.name !== 'type' && a.name !== 'src') s.setAttribute(a.name, a.value);
                    });
                    old.replaceWith(s);
                });
            }
        }

        // ---- Home loader ----
        async function loadGuideHome(delayMs = 500) {
            const main = $('main');
            if (!main) return;

            const loader = showLoader(main);
            await sleep(delayMs); // small UX delay for smoother loader

            try {
                const html = await fetchHTML(HOME_URL);
                replaceWithHTML(main, html, { runScripts: false });
                initLazyImages(main);
            } catch (err) {
                console.error('Failed loading guide homepage:', err);
                const errBox = document.createElement('div');
                errBox.className = 'guide-error';
                errBox.textContent = 'Could not load content. Please try again.';
                main.replaceChildren(errBox);
            } finally {
                loader.remove();
            }
        }

        // ---- Customer page loader (event delegation) ----
        let inFlight = false; // prevent rapid double-clicks
        document.addEventListener('click', async (ev) => {
            const card = ev.target.closest('.hct-img');
            if (!card || inFlight) return;

            const id = card.dataset.id;
            if (!id) return;

            const main = $('main');
            if (!main) return;

            inFlight = true;
            const loader = showLoader(main);

            try {
                const url  = CUSTOMER_TPL.replace('__ID__', encodeURIComponent(id));
                const html = await fetchHTML(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                replaceWithHTML(main, html, { runScripts: false });
                initLazyImages(main);
                // Optional scroll:
                // main.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (err) {
                console.error('Failed loading customer page:', err);
                loader.remove();
                const errBox = document.createElement('div');
                errBox.className = 'guide-error';
                errBox.textContent = 'Could not load the customer page. Please try again.';
                main.replaceChildren(errBox);
                return; // ensure finally still flips the flag
            } finally {
                loader.remove();
                inFlight = false;
            }
        });

        // Kick off home load when DOM is ready
        document.addEventListener('DOMContentLoaded', () => loadGuideHome(500));

        // Expose for manual re-trigger (optional)
        window.loadGuideHome = loadGuideHome;
    })();
</script>


