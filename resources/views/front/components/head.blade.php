<!-- resources/views/components/head.blade.php -->
<meta charset="UTF-8">
<title>@yield('title', 'KJSHOP')</title>

<meta charset="utf-8">
<meta name="robots" content="noodp">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

<link rel="icon" href="{!! asset('front/images/common/favicon-16x16.png') !!}" sizes="16x16" type="image/png">
<link rel="icon" href="{!! asset('front/images/common/favicon-32x32.png') !!}" sizes="32x32" type="image/png">
<link rel="icon" href="{!! asset('front/images/common/favicon-48x48.png') !!}" sizes="48x48" type="image/png">
<link rel="apple-touch-icon" href="{!! asset('front/images/common/favicon-180x180.png') !!}" sizes="180x180">
<link rel="icon" href="{!! asset('front/images/common/favicon-512x512.png') !!}" sizes="512x512" type="image/png">


<meta name="description" content="@yield('meta_description', 'دليل العبادية - استكشف المتاجر والصيدليات والمحلات في البلدة')">
<meta name="keywords" content="@yield('meta_keywords', 'default,المتاجر, الصيدليات, المحلات, بلدة العبادية, موقع شامل, الفئات, البلدة, تسوق, خدمات, احتياجات, دليل, بحث, أماكن, منتجات')">

<meta property="og:title" content="@yield('meta_title', 'دليل العبادية - استكشف المتاجر والصيدليات والمحلات في البلدة')" >
<meta property="og:description" content="@yield('meta_description', 'default, اكتشف جميع المتاجر والصيدليات والمحلات في بلدة العبادية من خلال موقعنا الشامل. استعرض مختلف الفئات للعثور على كل ما تحتاجه في البلدة، في مكان واحد!')">
<meta property="og:locale" content="Ar">
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="@yield('meta_image',trans('front/metatag.meta_tag_image',[], 'ar'))" />
<meta property="og:image:secure_url" content="@yield('meta_image',trans('front/metatag.meta_tag_image',[], 'ar')) !!}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/jpg">
<meta property="og:type" content="website">

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#013047">


<link href="https://fonts.googleapis.com/css2?family=Amiri&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('front/css/common/app.css') }}">
<link rel="stylesheet" href="{{ asset('front/css/common/styles.css') }}?v=4">

<!-- Include other CSS files if needed -->


{{--<link rel="manifest" href="/manifest.json">--}}
{{--<meta name="apple-mobile-web-app-capable" content="yes">--}}
{{--<meta name="apple-mobile-web-app-status-bar-style" content="black">--}}

@yield('head')
@stack('head')
