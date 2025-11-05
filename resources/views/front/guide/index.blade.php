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
<script>
    (() => {
        const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

        // iOS / Safari detection
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        // viewport check (with generous margin)
        function inViewport(el, margin = 800) {
            const r = el.getBoundingClientRect();
            const vh = window.innerHeight || document.documentElement.clientHeight;
            const vw = window.innerWidth  || document.documentElement.clientWidth;
            return r.bottom >= -margin && r.top <= vh + margin && r.right >= -margin && r.left <= vw + margin;
        }

        // promote data-src -> src (with one-time cache-bust on iOS)
        function promote(img) {
            const real = img.dataset.src;
            if (!real) return;

            if (isIOS && !img.dataset.busted) {
                const sep = real.includes('?') ? '&' : '?';
                img.src = real + sep + 't=' + Date.now(); // nudge Safari to actually load
                img.dataset.busted = '1';
            } else {
                img.src = real;
            }
            img.removeAttribute('data-src');
            img.loading = 'eager'; // hint Safari not to defer further
        }

        // heavy nudge for stubborn Safari: reinsert node
        function reinsertIfIOS(img) {
            if (!isIOS || !img.parentNode) return img;
            const next = img.nextSibling;
            const clone = img.cloneNode(true); // events aren’t needed on <img>
            img.parentNode.removeChild(img);
            img.parentNode.insertBefore(clone, next);
            return clone;
        }

        function markLoaded(img) {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-loaded');
            img.__loading = false;
        }
        function markError(img) {
            img.classList.remove('lazy-loading');
            img.classList.add('lazy-error');
            img.__loading = false;
        }

        function upgrade(img) {
            if (img.__loading || img.classList.contains('lazy-loaded')) return;
            img.__loading = true;
            img.classList.add('lazy-loading');

            promote(img);
            const node = reinsertIfIOS(img);

            if (node.complete && node.naturalWidth) {
                markLoaded(node);
                return;
            }
            node.addEventListener('load',  () => markLoaded(node), { once: true });
            node.addEventListener('error', () => markError(node),  { once: true });
        }

        function runLazy(root = document) {
            const imgs = $$('img.lazy:not(.lazy-loaded)', root);
            if (!imgs.length) return;

            let ticking = false;
            const check = () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(() => {
                    imgs.forEach(img => {
                        if (!img.isConnected || img.classList.contains('lazy-loaded')) return;
                        if (inViewport(img, 800)) upgrade(img);
                    });
                    ticking = false;
                });
            };

            const onScroll = () => check();
            const onResize = () => check();
            const onShow   = () => setTimeout(check, 100);

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onResize, { passive: true });
            window.addEventListener('orientationchange', onResize, { passive: true });
            document.addEventListener('visibilitychange', onShow);
            window.addEventListener('pageshow', (e) => { if (e.persisted) setTimeout(check, 100); }, { passive: true });
            window.addEventListener('focus', onShow, { passive: true });
            window.addEventListener('touchmove', onScroll, { passive: true });

            // initial passes (Safari sometimes needs a couple frames)
            setTimeout(check, 50);
            setTimeout(check, 300);

            // safety: force any remaining after a short grace period
            const safety1 = setTimeout(() => $$('img.lazy:not(.lazy-loaded)', root).forEach(upgrade), 2000);
            const safety2 = setTimeout(() => $$('img.lazy:not(.lazy-loaded)', root).forEach(upgrade), 5000);

            return {
                refresh: () => setTimeout(check, 100),
                destroy() {
                    window.removeEventListener('scroll', onScroll);
                    window.removeEventListener('resize', onResize);
                    window.removeEventListener('orientationchange', onResize);
                    document.removeEventListener('visibilitychange', onShow);
                    window.removeEventListener('pageshow', onShow);
                    window.removeEventListener('focus', onShow);
                    window.removeEventListener('touchmove', onScroll);
                    clearTimeout(safety1);
                    clearTimeout(safety2);
                }
            };
        }

        // auto-init + expose for injected pages
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => runLazy());
        } else {
            runLazy();
        }
        window.initLazyImages = runLazy;
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


