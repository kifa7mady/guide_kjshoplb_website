<div class="customer-widget">
    <div class="container mt-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @if(!empty($region))
                    <li class="breadcrumb-item " ><a href="{!! $region->seo_url !!}">{!! $region->getTranslation('name', 'en') !!}</a></li>
                @endif
            </ol>
        </nav>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">


                @if($customerJob->categories->isNotEmpty())
                    <li class="breadcrumb-item">
                        @foreach($customerJob->categories->unique('id') as $category)
                            <a href="{!! $category->seo_url !!}">
                                {!! ($loop->iteration > 1 ? ' & ' : '') . $category->getTranslation('name', 'en') !!}
                            </a>
                        @endforeach
                    </li>
                @endif


                @if(!empty($customerJob->subCategories->isNotEmpty()))
                    <li class="breadcrumb-item">
                        @foreach($customerJob->subCategories as $subCategory)
                            @if($loop->iteration > 1)
                                &
                            @endif
                            <a href="{!! $subCategory->seo_url !!}">
                                {!! $subCategory->getTranslation('name', 'en') !!}
                            </a>
                        @endforeach
                    </li>
                @endif

                @if(!empty($customerJob->name) && $customerJob->name)
                    <li class="breadcrumb-item" aria-current="page">{!! $customerJob->name !!}</li>
                @endif
            </ol>
        </nav>
    </div>
    <div class="container">
        <div class="customer-row">
            @if(isset($customerJob->categories) && isset($customerJob->categories[0]))
                <div class="customer-img">
                    @if(!$customerJob->images->isEmpty())
                        <img src="{!! live_asset('storage/' .$customerJob->images[0]->path) !!}" class="d-block w-100" alt="Shop Logo" style="width: 100%;height: auto">
                    @else
                        <img src="{!! live_asset('storage/' .$customerJob->subCategories[0]->logo) !!}" class="d-block w-100" alt="Shop Logo">
                    @endif
                </div>
            @endif

            <div class="customer-column">

                @if($customerJob->name)
                    <div class="customer-item">
                        <span class="label text-capitalize">{!! $customerJob->type ? ($customerJob->type . ' Name : ' ) : '' !!} </span> {!! $customerJob->name !!}
                    </div>
                @endif
                <div class="customer-item">
                    <span class="label text-capitalize">{{ Str::contains($customerJob->customer->customer_names, '&') ? 'Names' : 'Name' }}:</span> {!! $customerJob->customer->customer_names !!}
                </div>

                @if(!empty($customerJob->customer->mobile))
                    <div class="customer-item">
                        <span class="label text-capitalize">Phone Number:</span>
                        @foreach($customerJob->customer->mobile as $mobile)
                            <a href="tel:{!! $mobile !!}">{!! $mobile !!}</a>
                        @endforeach
                    </div>
                @endif

                @if($customerJob->description)
                    <div class="customer-item">
                        <span class="label text-capitalize">Description:</span> {!! $customerJob->description !!}
                    </div>
                @endif



            </div>
        </div>
        <div class="customer-row">
            @if(isset($customerJob->images[1]) && isset($customerJob->images[1]->path))
                @foreach($customerJob->images as $image)
                    @if($loop->iteration == 1)
                        @continue
                    @endif
                    <div class="customer-gallery">
                        <img src="{!! asset('storage/' .$image->path) !!}" class="d-block w-100" alt="Shop Logo">
                    </div>
                @endforeach
            @endif
        </div>

        {{--        <a href="#" class="cta-button">Contact Us for More Info</a> --}}
    </div>
</div>
