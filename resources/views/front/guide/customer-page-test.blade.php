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

    @include('front.guide.customer-page')
</main>

@include('front.components.footer')
</body>
</html>
