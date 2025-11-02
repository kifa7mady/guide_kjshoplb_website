@if(isset($categories[$category_key]->name))

<div class="guide-widget home-category-widget">
    <div class="d-wrap hct-breadcrumb">
        <div>{!! $categories[$category_key]->parent->name !!}</div>
        <div class="icon arrow-icon">
            <svg class="icon icon-arrow-right">
                <use xlink:href="{!! asset('front/icons/icon-arrow-right.svg') !!}#icon-arrow-right"></use>
            </svg>
        </div>
        <div>{!! $categories[$category_key]->name !!}</div>
        <div class="hct-viewall">View all</div>
    </div>
    <div class="hct-row">
        @foreach($categories[$category_key]->CustomerJobsByCategory as $customJobs)
            <div class="hct-item d-column">
                @if(isset($customJobs->categories) && isset($customJobs->categories[0]))
                    <div class="hct-img">
                        @if(!$customJobs->images->isEmpty())
                            <img src="{!! asset('storage/' .$customJobs->images[0]->path) !!}" onerror="this.src='{!! live_asset('storage/' .$customJobs->images[0]->path) !!}'"
                                 class="d-block w-100" alt="Shop Logo" 
                            >
                        @else
                            <img src="{!! asset('storage/' .$customJobs->subCategories[0]->logo) !!}" onerror="this.src='{!! live_asset('storage/' .$customJobs->subCategories[0]->logo) !!}'"
                                 class="d-block w-100" alt="Shop Logo" >
                        @endif
                    </div>
                @endif
                <div class="">{!! $customJobs->name !!}</div>
            </div>
        @endforeach
    </div>
</div>
@endif
