<!DOCTYPE html>
<html lang="en-US">

<head>
    @include('front.components.head')
    @include('front.components.styles') <!-- Include custom styles if needed -->
    @include('front.components.scripts') <!-- Include JavaScript files -->
</head>

<body>
@include('front.components.header')

<main>
    @yield('content')
</main>

@include('front.components.footer')
</body>
</html>
