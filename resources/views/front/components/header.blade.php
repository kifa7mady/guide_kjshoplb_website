
<div class="header">
    <div class="container">
        <div class="header-wrapper d-jc-between d-ai-center">
            <a class="d-flex d-column header-logo d-ai-center" href="/">
                <img src="{!! asset('front/images/common/logo.svg') !!}" width="60" />
            </a>
            <div class="header-r d-flex d-wrap">
                <div class="header-r-icon search-icon">
                    <svg class="icon icon-search">
                        <use xlink:href="{!! asset('front/icons/icon-search.svg') !!}#icon-search"></use>
                    </svg>
                </div>
                <div class="header-r-icon notification-icon">
                    <svg class="icon icon-notification">
                        <use xlink:href="{!! asset('front/icons/icon-notification.svg') !!}#icon-notification"></use>
                    </svg>
                </div>
            </div>
{{--            @if(!empty($region))--}}
{{--            <a class="guide-location font-20 font-amiri color-1" href="{!! $region->seo_url !!}">دليل  {!! $region->getTranslation('name', 'ar')  !!}</a>--}}
{{--            @endif--}}
        </div>
    </div>
</div>


