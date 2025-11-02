@extends('front.guide')

@section('title', 'KJ Shop - دليل العبادية')
@section('meta_title', 'دليل العبادية - استكشف المتاجر والصيدليات والمحلات في البلدة')
@section('meta_description', ' اكتشف جميع المتاجر والصيدليات والمحلات في بلدة العبادية من خلال موقعنا الشامل. استعرض مختلف الفئات للعثور على كل ما تحتاجه في البلدة، في مكان واحد!')
@section('meta_keywords', 'المتاجر, الصيدليات, المحلات, بلدة العبادية, موقع شامل, الفئات, البلدة, تسوق, خدمات, احتياجات, دليل, بحث, أماكن, منتجات')

<!-- HTML -->

<script>
    (() => {
        const HOME_URL = "http://guide.kjshoplb.local/guide/get-home-page";
        const ICON_URL = @json(asset('front/icons/icon-square-loader.svg'));

        const sleep = (ms) => new Promise(r => setTimeout(r, ms));

        async function loadGuideHome(delayMs = 2000) {
            const main = document.querySelector('main');
            if (!main) return;

            const loader = document.createElement('div');
            loader.className = 'guide-loader';
            loader.innerHTML = `<img src="${ICON_URL}" alt="" />`;
            main.appendChild(loader);

            await sleep(delayMs);

            const ac = new AbortController();
            const timer = setTimeout(() => ac.abort(), 15000);

            try {
                const res = await fetch(HOME_URL, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    signal: ac.signal
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);

                const html = await res.text();
                main.insertAdjacentHTML('beforeend', html);
            } catch (err) {
                console.error('Failed loading guide homepage:', err);
                const errBox = document.createElement('div');
                errBox.style.padding = '12px';
                errBox.style.color = '#013047';
                errBox.textContent = 'Could not load content. Please try again.';
                main.appendChild(errBox);
            } finally {
                clearTimeout(timer);
                loader.remove();
            }
        }

        // ✅ call it
        document.addEventListener('DOMContentLoaded', () => loadGuideHome(2000));
        window.loadGuideHome = loadGuideHome;
    })();
</script>

