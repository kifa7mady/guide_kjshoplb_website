@extends('front.guide')

@section('title', 'KJ Shop - دليل العبادية')
@section('meta_title', 'دليل العبادية - استكشف المتاجر والصيدليات والمحلات في البلدة')
@section('meta_description', ' اكتشف جميع المتاجر والصيدليات والمحلات في بلدة العبادية من خلال موقعنا الشامل. استعرض مختلف الفئات للعثور على كل ما تحتاجه في البلدة، في مكان واحد!')
@section('meta_keywords', 'المتاجر, الصيدليات, المحلات, بلدة العبادية, موقع شامل, الفئات, البلدة, تسوق, خدمات, احتياجات, دليل, بحث, أماكن, منتجات')

<style>
    <?php include('front/widgets/home-category-widget/home-category-widget.css')?>
</style>
<style>
    <?php include('front/widgets/home-ad/home-ad.css')?>
</style>
@section('content')
    @php
        $ad_key = 0;
    @endphp
    @foreach($categories as $category_key => $category)
    @if ($category_key % 2 === 0)
        @php
            $ad_key = $ad_key + 1;
            if($ad_key >=5){
                $ad_key = 1;
            }
        @endphp
        @include('front.components.widgets.home-ad',['ad_key'=>$ad_key])
    @endif
    @include('front.components.widgets.home-category-widget',['category_key'=>$category_key])
    @endforeach
@endsection
