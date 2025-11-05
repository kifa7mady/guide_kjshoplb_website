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
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) ||
            (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        // Returns the nearest scrollable parent (or window if none)
        function getScrollParent(el) {
            let p = el.parentElement;
            while (p) {
                const s = getComputedStyle(p);
                const overflowY = s.overflowY;
                if ((overflowY === 'auto' || overflowY === 'scroll') && p.scrollHeight > p.clientHeight) {
                    return p;
                }
                p = p.parentElement;
            }
            return null; // means window
        }

        function inViewport(el, root = null, margin = 0) {
            const r = el.getBoundingClientRect();
            let top = r.top, bottom = r.bottom, left = r.left, right = r.right;
            let vw = window.innerWidth, vh = window.innerHeight;

            if (root && root !== window) {
                const rr = root.getBoundingClientRect();
                vw = rr.width; vh = rr.height;
                top -= rr.top; bottom -= rr.top; left -= rr.left; right -= rr.left;
            }
            return (bottom >= -margin && top <= vh + margin && right >= -margin && left <= vw + margin);
        }

        function upgradeImg(img) {
            const pic = img.parentElement?.tagName === 'PICTURE' ? img.parentElement : null;
            if (pic) {
                pic.querySelectorAll('source[data-srcset]').forEach(src => {
                    src.srcset = src.dataset.srcset;
                    src.removeAttribute('data-srcset');
                });
            }
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
                img.removeAttribute('data-srcset');
            }
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
            img.classList.add('lazy-loaded');
        }

        function initLazyImages(rootEl = document) {
            const imgs = Array.from(rootEl.querySelectorAll('img.lazy:not(.lazy-loaded)'));
            if (!imgs.length) return;

            // Determine the scrolling context (if your <main> is scrollable, use it)
            const scrollRoot = getScrollParent(rootEl) || getScrollParent(imgs[0]) || null;

            // Fallback runner (works even when IO is buggy on iOS PWA)
            const marginPx = Math.max(300, Math.round((window.innerHeight || 800) * 0.75)); // pre-load earlier
            const check = () => {
                for (const img of imgs) {
                    if (!img.isConnected || img.classList.contains('lazy-loaded')) continue;
                    if (inViewport(img, scrollRoot || window, marginPx)) upgradeImg(img);
                }
            };

            // Use IO when possible, but keep fallback listeners alive for iOS/WebView quirks
            let io = null;
            if ('IntersectionObserver' in window) {
                try {
                    io = new IntersectionObserver((entries) => {
                        for (const e of entries) {
                            if (e.isIntersecting) {
                                upgradeImg(e.target);
                                io.unobserve(e.target);
                            }
                        }
                    }, {
                        root: (scrollRoot && scrollRoot !== document.body) ? scrollRoot : null,
                        rootMargin: '50% 0px',   // very generous lead-in
                        threshold: 0.01
                    });

                    // Use rAF so iOS has a painted layout before observation
                    requestAnimationFrame(() => imgs.forEach(img => io.observe(img)));
                } catch {
                    io = null;
                }
            }

            // Always attach fallback listeners (cheap & passive)
            const target = scrollRoot || window;
            const onScroll = () => { requestAnimationFrame(check); };
            target.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll, { passive: true });
            window.addEventListener('orientationchange', onScroll, { passive: true });
            document.addEventListener('visibilitychange', onScroll, { passive: true });

            // Initial check (important for iOS when IO stalls until first scroll)
            check();

            // Expose a tiny API to re-check after DOM mutations
            return { refresh: check, destroy: () => {
                    target.removeEventListener('scroll', onScroll);
                    window.removeEventListener('resize', onScroll);
                    window.removeEventListener('orientationchange', onScroll);
                    document.removeEventListener('visibilitychange', onScroll);
                    if (io) io.disconnect();
                }};
        }

        // Expose globally so you can call after injecting HOME_URL HTML
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


