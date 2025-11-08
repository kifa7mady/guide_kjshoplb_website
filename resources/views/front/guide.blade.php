<!DOCTYPE html>
<html lang="en-US">

<head>
    @include('front.components.head')
    @include('front.components.styles') <!-- Include custom styles if needed -->
    @include('front.components.scripts') <!-- Include JavaScript files -->
</head>

<body>
<main>
    @include('front.components.header')

    <section class="content">
        @yield('content')
    </section>

    @include('front.components.footer')
</main>
</body>
</html>
