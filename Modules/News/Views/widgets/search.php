<!-- Search Widget -->
<div class="widget widget-search">
    <form action="<?=route_to('search')?>" autocomplete="off" role="search" method="get">
    <input type="search" value="" name="q" id="widgetSearch" pattern=".{3,}" placeholder="<?=lang('Search.search_placeholder')?>" aria-label="Search" />
    <button class="search-btn" type="submit" id="widgetSearchButton"><i class="fa fa-search"></i></button>
    </form>
</div>