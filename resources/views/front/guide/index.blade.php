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
        // ---- tiny helpers ----
        const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

        function nearestScrollRoot(el) {
            let p = el;
            while (p && p !== document.body) {
                const st = getComputedStyle(p);
                if ((st.overflowY === 'auto' || st.overflowY === 'scroll') && p.scrollHeight > p.clientHeight) {
                    return p;
                }
                p = p.parentElement;
            }
            return null;
        }

        function inViewport(el, root=null, margin=0) {
            const r = el.getBoundingClientRect();
            let vw = window.innerWidth, vh = window.innerHeight;
            let { top, left, bottom, right } = r;

            if (root && root !== window) {
                const cr = root.getBoundingClientRect();
                vw = cr.width; vh = cr.height;
                top -= cr.top; bottom -= cr.top; left -= cr.left; right -= cr.left;
            }
            return bottom >= -margin && top <= vh + margin && right >= -margin && left <= vw + margin;
        }

        function promoteSources(img) {
            const pic = img.parentElement && img.parentElement.tagName === 'PICTURE' ? img.parentElement : null;
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
                // Safari-specific: Force reload by breaking cache
                img.src = img.dataset.src + (img.dataset.src.includes('?') ? '&' : '?') + 't=' + Date.now();
                img.removeAttribute('data-src');
            }
            img.loading = 'eager';
        }

        async function upgrade(img) {
            if (img.classList.contains('lazy-loaded')) return;

            // Add loading state
            img.classList.add('lazy-loading');

            promoteSources(img);

            // Safari-specific: Force load by removing and re-adding to DOM
            const parent = img.parentNode;
            const nextSibling = img.nextSibling;
            const clone = img.cloneNode(true);

            parent.removeChild(img);
            parent.insertBefore(clone, nextSibling);

            try {
                if (clone.decode) await clone.decode();
            } catch (e) {
                console.log('Image decode failed, continuing anyway:', e);
            }

            clone.classList.remove('lazy-loading');
            clone.classList.add('lazy-loaded');
        }

        function initLazyImages(rootEl = document) {
            const imgs = $$('img.lazy:not(.lazy-loaded)', rootEl);
            if (!imgs.length) return;

            const scrollRoot = nearestScrollRoot(rootEl) || nearestScrollRoot(imgs[0]) || null;
            const target = scrollRoot || window;

            const marginPx = Math.max(400, Math.round((window.innerHeight || 800)));

            let ticking = false;
            const check = () => {
                if (ticking) return;
                ticking = true;
                requestAnimationFrame(() => {
                    imgs.forEach(img => {
                        if (!img.isConnected || img.classList.contains('lazy-loaded')) return;
                        if (inViewport(img, scrollRoot || window, marginPx)) upgrade(img);
                    });
                    ticking = false;
                });
            };

            // Enhanced event listeners for iOS
            const onScroll = () => check();
            const onResize = () => check();
            const onVis = () => {
                // Force check when page becomes visible again
                setTimeout(check, 100);
            };

            target.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onResize, { passive: true });
            window.addEventListener('orientationchange', onResize, { passive: true });
            document.addEventListener('visibilitychange', onVis);
            document.addEventListener('webkitvisibilitychange', onVis); // Safari
            window.addEventListener('pageshow', (e) => {
                // Fix for Safari back/forward cache
                if (e.persisted) setTimeout(check, 100);
            }, { passive: true });
            window.addEventListener('focus', onVis, { passive: true });

            // More aggressive iOS touch events
            window.addEventListener('touchstart', onScroll, { passive: true });
            window.addEventListener('touchmove', onScroll, { passive: true });
            window.addEventListener('touchend', onScroll, { passive: true });

            // Initial check with delays for Safari
            setTimeout(check, 100);
            setTimeout(check, 500);

            // Safety polling
            const safety = setInterval(check, 300);
            setTimeout(() => clearInterval(safety), 10000);

            // Mutation Observer for dynamic content
            const mo = new MutationObserver((muts) => {
                let shouldCheck = false;
                muts.forEach(m => m.addedNodes.forEach(n => {
                    if (n.nodeType === 1) {
                        if (n.matches && n.matches('img.lazy:not(.lazy-loaded)')) shouldCheck = true;
                        if ($$('img.lazy:not(.lazy-loaded)', n).length) shouldCheck = true;
                    }
                }));
                if (shouldCheck) {
                    setTimeout(check, 100);
                }
            });
            mo.observe(rootEl, { childList: true, subtree: true });

            return {
                refresh: () => setTimeout(check, 100),
                destroy() {
                    target.removeEventListener('scroll', onScroll);
                    window.removeEventListener('resize', onResize);
                    window.removeEventListener('orientationchange', onResize);
                    document.removeEventListener('visibilitychange', onVis);
                    document.removeEventListener('webkitvisibilitychange', onVis);
                    window.removeEventListener('pageshow', onVis);
                    window.removeEventListener('focus', onVis);
                    window.removeEventListener('touchstart', onScroll);
                    window.removeEventListener('touchmove', onScroll);
                    window.removeEventListener('touchend', onScroll);
                    mo.disconnect();
                    clearInterval(safety);
                }
            };
        }

        // Initialize when DOM is ready
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


