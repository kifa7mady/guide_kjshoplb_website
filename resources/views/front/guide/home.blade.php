<style>
    <?php include('front/widgets/home-category-widget/home-category-widget.css')?>
</style>
<style>
    <?php include('front/widgets/home-ad/home-ad.css')?>
</style>
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
