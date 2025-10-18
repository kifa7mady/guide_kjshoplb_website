<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/css/glide.core.min.css">


@if(isset($categories[$category_key]->name))
    <style>
            <?php include('front/widgets/home-category-widget/home-category-widget.css') ?>
    </style>
    <div class="guide-widget home-category-widget">
        <div class="d-wrap hct-breadcrumb">
            <div>{!! $categories[$category_key]->name !!}</div>
            <div> ></div>
            <div>{!! $categories[$category_key]->parent->name !!}</div>
            <div class="hct-viewall">View all</div>
        </div>
        <div class="hct-row">
            <div class="glide" id="glide">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        @foreach($categories[$category_key]->CustomerJobsByCategory as $customJobs)
                            <li class="glide__slide">
                                @if(isset($customJobs->categories) && isset($customJobs->categories[0]))
                                    <div class="customer-img">
                                        @if(!$customJobs->images->isEmpty())
                                            <img src="{!! asset('storage/' .$customJobs->images[0]->path) !!}"
                                                 onerror="this.src='{!! live_asset('storage/' .$customJobs->images[0]->path) !!}'"
                                                 class="d-block w-100" alt="Shop Logo">
                                        @else
                                            <img src="{!! asset('storage/' .$customJobs->subCategories[0]->logo) !!}"
                                                 onerror="this.src='{!! live_asset('storage/' .$customJobs->subCategories[0]->logo) !!}'"
                                                 class="d-block w-100" alt="Shop Logo">
                                        @endif
                                    </div>
                                @endif
                                <div class="">{!! $customJobs->name !!}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
<script>
    new Glide('#glide', {
        type: 'carousel',
        perView: 3.5,
        gap: 16
    }).mount();
</script>
