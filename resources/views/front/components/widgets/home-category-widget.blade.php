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
                    <div class="hct-img" data-id="{!! $customJobs->id !!}">
                        <div class="lazy-wrapper">
                        @if(!$customJobs->images->isEmpty())
                            <img
                                class="lazy d-block w-100"
                                src="{!! asset('/front/images/common/placeholder.jpg') !!}"
                                data-src="{!! live_asset('storage/'.$customJobs->images[0]->path) !!}"
                                onerror="this.onerror=null; this.src='{!! asset('storage/'.$customJobs->images[0]->path) !!}'"
                                decoding="async"
                                sizes="100vw"
                                alt="Shop Logo"
                                width="130" height="130"
                            />

                        @else
                            <img
                                class="lazy d-block w-100"
                                src="{!! asset('/front/images/common/placeholder.jpg') !!}"
                                data-src="{!! live_asset('storage/' .$customJobs->subCategories[0]->logo) !!}"
                                onerror="this.onerror=null; this.src='{!! asset('storage/' .$customJobs->subCategories[0]->logo) !!}'"
                                decoding="async"
                                sizes="100vw"
                                alt="Shop Logo"
                                width="130" height="130"
                            />
                        @endif
                        </div>
                    </div>
                @endif
                <div class="">{!! $customJobs->name !!}</div>
            </div>
        @endforeach
    </div>
</div>
@endif


