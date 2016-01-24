<div class="frame-inside page-category">
        <div class="container">
            <!---->
            <div class="filter left-catalog">
                <div id="small-filter-btn" class="btn-additional-s_c2 mq-w-320 mq-block m-b_0  is-open">
                    <button type="button">
                        <span class="text-el">
                            <span class="d_l text-open">������ ������ <span class="icon-show-part up"></span></span>
                            <span class="d_l text-closed">�������� ������ <span class="icon-show-part"></span></span>
                        </span>
                    </button>
                </div>
                <div class="filter-slide  open-filter">

<script>
$(document).ready(function() {
    //$('.frame-filter').nStCheck();
    $('.frame-group-checks').find('.niceCheck').click(function(e){
        //console.log("123");
    });
});

</script>
<form method="post">
    <input type="hidden" name="filter" value="1">
    <div class="frame-filter p_r">
<script type="text/javascript">
    totalProducts = parseInt('20');
    function createObjSlider(minCost, maxCost, defMin, defMax, curMin, curMax, lS, rS){
        this.minCost = minCost;
        this.maxCost = maxCost;
        this.defMin = defMin;
        this.defMax = defMax;
        this.curMin = curMin;
        this.curMax = curMax;
        this.lS = lS;
        this.rS = rS;
    };
    sliders = new Object();
    sliders.slider1 = new createObjSlider('.minCost', '.maxCost', 69, 1010, 69, 1010, 'lp', 'rp');
</script>
<div class="preloader wo-i" style="display: none;"></div>
<div id="slider-range"></div>
<div class="frames-checks-sliders">
    <!--
    <div class="frame-slider" data-rel="sliders.slider1">
        <div class="inside-padd">
            <div class="title">����</div>
            <div class="form-cost number">
                <div class="t-a_j">
                                        <label>
                        <input type="text" class="minCost" data-title="������ �����" name="lp" value="69" data-mins="69">
                    </label>
                    <label>
                        <input type="text" class="maxCost" data-title="������ �����" name="rp" value="1010" data-maxs="1010">
                    </label>
                </div>
            </div>
            <div class="slider-cont">
                <noscript>����������� �� �������</noscript>
                                <div class="slider ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all" id="slider1" aria-disabled="false">
                    <a href="#" class="ui-slider-handle left-slider ui-state-default ui-corner-all" style="left: 0%;"></a>
                    <a href="#" class="ui-slider-handle right-slider ui-state-default ui-corner-all" style="left: 100%;"></a>
                <div class="ui-slider-range ui-widget-header ui-corner-all" style="left: 0%; width: 100%;"></div></div>
            </div>
        </div>
    </div>
    -->
    <!--
    {{ debugValue(system_flags.eshop.features) }}
    -->

    {% for ftr in system_flags.eshop.features %}
        {% if ftr.in_filter == 1 %}
            {% if ftr.ftype == 0 %}
            <div class="frame-group-checks">
                <div class="inside-padd">
                    <div class="title">
                        <span class="f-s_0">
                            <span class="icon-arrow"></span>
                            <span class="d_b">
                                <span class="text-el">{{ ftr.name }}</span>
                            </span>
                        </span>
                    </div>
                    <div class="lineForm">
                        <input type="text" value="{{current_filter[ftr.id]}}" name="filters[{{ ftr.id }}]">
                    </div>
                </div>
            </div>
            {% elseif ftr.ftype == 1 %}
            <div class="frame-group-checks">
                    <div class="inside-padd">
                        <div class="title">
                            <span class="f-s_0">
                                <span class="icon-arrow"></span>
                                <span class="d_b">
                                    <span class="text-el">{{ ftr.name }}</span>
                                </span>
                            </span>
                        </div>
                        <input name="filters[{{ ftr.id }}]" value="1" type="checkbox" {% if (current_filter[ftr.id] is defined) %} checked {% endif %}>
                        <ul class="filters-content">
                                 <li>
                                    <div class="frame-label" id="brand_132">
                                        <span class="niceCheck nstcheck">
                                            <input name="filters[{{ ftr.id }}]" value="1" type="checkbox">
                                        </span>
                                        <div class="name-count">
                                            <span class="text-el">�������</span>
                                            <span class="count"></span>
                                        </div>
                                    </div>
                                </li>
                        </ul>
                    </div>
            </div>
            {% elseif ftr.ftype == 2 %}
            <div class="frame-group-checks">
                    <div class="inside-padd">
                        <div class="title">
                            <span class="f-s_0">
                                <span class="icon-arrow"></span>
                                <span class="d_b">
                                    <span class="text-el">{{ ftr.name }}</span>
                                </span>
                            </span>
                        </div>
                        <div class="lineForm">
                            <select name="filters[{{ ftr.id }}]">
                                <option value=""></option>
                                {% for fkopt, fvopt in ftr.foptions %}
                                <option value="{{ fkopt }}" {% if (current_filter[ftr.id] is defined) %} selected {% endif %}>{{ fvopt }}</option>
                                {% endfor %}
                            </select>
                        </div>
                    </div>
            </div>
            {% endif %}
        {% endif %} 

    {% endfor %}

        <div class="frame-group-checks">
            <div class="inside-padd">
                <div class="title">
                    <span class="f-s_0">
                        <span class="icon-arrow"></span>
                        <span class="d_b">
                            <span class="text-el">{{ ftr.name }}</span>
                        </span>
                    </span>
                </div>
                <div class="lineForm">
                    
                    <div class="frame-label">
                        <span class="title">&nbsp;</span>
                        <span class="frame-form-field">
                            <div class="btn-buy">
                                <button type="submit" id="submitFilter">
                                    <span class="text-el">���������</span>
                                </button>
                            </div>
                        </span>
                    </div>
                    
                </div>
            </div>
        </div>

</div>
</div>
</form>
<script>
</script>                </div>
            
            </div>
            
            {% if (info) %}
                <div class="feed-me">
                {{info}}
                </div>
            {% endif %}
            
            <div class="right-catalog" > <!--style="width:100% !important;margin-left: 0;"-->
                <!-- Start. Category name and count products in category-->
                <div class="f-s_0 title-category">
                    <div class="frame-title">
                        <h1 class="title">{{ cat_info.name }}</h1>
                    </div>
                    <span class="count">{{ cat_info.cnt }}</span>
                </div>
                <!-- End. Category name and count products in category-->
                                <!--Start. Banners block-->
                
                <!--End. Banners-->
                    <div class="frame-header-category">
        <div class="header-category f-s_0">
            <div class="inside-padd">
                <!-- Start. Order by block -->
                <div class="frame-sort d_i-b v-a_t">

<script type="text/javascript">
jQuery(document).ready(function(){

var params = {
    changedEl: ".lineForm select",
    visRows: 50,

}

cuSel(params);

});
</script>
<form method="post" id="catalogForm">
<div class="lineForm">
    <select class="sort" id="sort" name="order">
        <option value="date_desc" {% if (filter.order == 'date_desc') %}selected{% endif %}>�� ����</option>
        <option value="name_asc" {% if (filter.order == 'name_asc') %}selected{% endif %}>�� �������� (�-�)</option>
        <option value="price_asc" {% if (filter.order == 'price_asc') %}selected{% endif %}>�� ������� � �������</option>
        <option value="price_desc" {% if (filter.order == 'price_desc') %}selected{% endif %}>�� ������� � �������</option>
        <option value="stock_desc" {% if (filter.order == 'stock_desc') %}selected{% endif %}>�� �������</option>
        <option value="likes_desc" {% if (filter.order == 'likes_desc') %}selected{% endif %}>�� ������������</option>
    </select>
</div>
</form>
                    <!--
                    <div class="lineForm">
                        <div class="cusel sort" id="cuselFrame-sort" style="width:176px" tabindex="0"><div class="cuselFrameRight"></div><div class="cuselText">�����</div><div class="cusel-scroll-wrap" style="display: none; visibility: visible;"><div class="jScrollPaneContainer" style="height: 192px; width: 180px;"><div class="cusel-scroll-pane" id="cusel-scroll-sort" style="overflow: hidden; width: 180px; height: 192px; padding: 0px;">
                                                                                        <span val="action" class="cuselActive">�����</span>
                                                            <span val="price">�� ������� � �������</span>
                                                            <span val="price_desc">�� ������� � �������</span>
                                                            <span val="hit">����������</span>
                                                            <span val="rating">�������</span>
                                                            <span val="hot">�������</span>
                                                            <span val="views">�� ���������� ����������</span>
                                                            <span val="name">�� �������� (�-�)</span>
                                                    </div></div></div><input type="hidden" id="sort" name="order" value="action"></div>
                    </div>
                    -->
                </div>
                <!-- End. Order by block -->
                <!--         Start. Product per page  -->
                <!--
                <div class="frame-count-onpage d_i-b v-a_t">
                                                                                    <div class="lineForm">
                                                <div class="cusel" id="cuselFrame-sort2" style="width:159px" tabindex="0"><div class="cuselFrameRight"></div><div class="cuselText">12 ������� �� ��������</div><div class="cusel-scroll-wrap" style="display: none; visibility: visible;"><div class="jScrollPaneContainer" style="height: 72px; width: 189px;"><div class="cusel-scroll-pane" id="cusel-scroll-sort2" style="overflow: hidden; width: 189px; height: 72px; padding: 0px;">
                                                            <span selected="selected" val="12" class="cuselActive">12 ������� �� ��������</span>
                                                            <span val="24">24 ������ �� ��������</span>
                                                            <span val="48">48 ������� �� ��������</span>
                                                    </div></div></div><input type="hidden" id="sort2" name="user_per_page" value="12"></div>
                    </div>
                </div>
                -->
                <!--         End. Product per page  -->
                <!--        Start. Show products as list or table-->
                <nav class="frame-catalog-view d_i-b v-a_t">
                    <ul class="tabs tabs-list-table" data-elchange="#items-catalog-main" data-cookie="listtable">
                        <li class="active">
                            <button type="button" data-href="tablemini" data-title="���� �������" data-rel="tooltip">
                                <span class="icon_tablemini_cat"></span><span class="text-el">���� �������</span>
                            </button>
                        </li>
                        <li class="">
                            <button type="button" data-href="list" data-title="������" data-rel="tooltip">
                                <span class="icon_list_cat"></span><span class="text-el">������</span>
                            </button>
                        </li>
                                            </ul>
                </nav>
                <!--        End. Show products as list or table-->
            </div>
            <!--                Start. if $CI->uri->segment(2) == "search" then show hidden field-->
                        <!--                End. if $CI->uri->segment(2) == "search" then show hidden field-->
        </div>
    </div>
                <!-- Start.If count products in category > 0 then show products list and pagination links -->
                                <ul class="animateListItems items items-catalog items-product tablemini" id="items-catalog-main">
                    <!-- Include template for one product item-->
                    


{% for entry in entries %}
<li class="globalFrameProduct {% if (entry.variants[0].stock == 0) or (entry.variants[0].stock == 1) %}not-avail{% elseif (entry.variants[0].stock == 5) %}to-cart{% endif %}" data-pos="top">
    <!-- Start. Photo & Name product -->
    <a href="{{entry.fulllink}}" class="frame-photo-title">
        <span class="photo-block">
            <span class="helper"></span>
            {% if (entry.images[0].filepath) %}<img src='{{home}}/uploads/eshop/products/thumb/{{entry.images[0].filepath}}' class="vImg">{% else %}<img src='{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg' class="vImg">{% endif %}
                                            </span>
        <span class="title">{{ entry.name }}</span>
    </a>
    <!-- End. Photo & Name product -->
    <div class="description">
        <!-- Start. article & variant name & brand name -->
                <div class="frame-variant-name-code">
                        <span class="frame-variant-code frameVariantCode">�������:
                <span class="code js-code">
                                        {{ entry.code }}                                    </span>
            </span>
           <!--
           <span class="frame-item-brand">�����:
                <span class="code js-code">
                                        <a href="http://fluid.imagecmsdemo.net/shop/brand/fly">
                        Fly                    </a>
                                    </span>
            </span>
            -->
                    </div>
                <!-- End. article & variant name & brand name -->
                <!--
                        <div class="frame-star f-s_0">
                <div class="star">
        <div id="star_rating_5623" class="productRate star-small">
            <div style="width: 80%"></div>
        </div>
    </div>
            <a href="http://fluid.imagecmsdemo.net/shop/product/mobilnyi-telefon-fly-e141-tv-dual-sim-black#comment" class="count-response">
                4                ������            </a>
        </div>
        -->
                        <!-- Start. Prices-->
        <div class="frame-prices f-s_0">
                                                <!-- Start. Product price-->
            <span class="current-prices f-s_0">
                <span class="price-new">
                    <span>
                    {% if (entry.variants[0].price) %}
<span class="price priceVariant">{{ (entry.variants[0].price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</span> {{ system_flags.eshop.current_currency.sign }}
                    {% endif %}
                    </span>
                </span>
                
                {% if (not (entry.variants[0].compare_price == '0.00')) and (not (entry.variants[0].compare_price == '')) %}
                <span class="price-add">
                    <span>
<span class="price addCurrPrice"><s>{{ (entry.variants[0].compare_price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }}</s></span> <s>{{ system_flags.eshop.current_currency.sign }}</s>
                    </span>
                </span>
                {% endif %}
                            </span>
                        <!-- End. Product price-->
        </div>
        <!-- End. Prices-->
                        
                <div class="funcs-buttons frame-without-top" style="position: relative; top: 0px;">
            <div class="no-vis-table">
                {% if (entry.variants[0].stock == 0) or (entry.variants[0].stock == 1) %}
                <div class="js-variant-5834 js-variant">
                <div class="alert-exists">��� � �������</div>
                <div class="btn-not-avail">
                <button class="infoBut isDrop" type="button" data-drop=".drop-report">
                </button>
                </div>
                </div>
                {% elseif (entry.variants[0].stock == 5) %}
                <!-- Start. Collect information about Variants, for future processing -->
                <div class="frame-count-buy js-variant-{{entry.id}} js-variant">
                    <div class="btn-buy btn-cart d_n">
                        <button type="button" data-id="{{entry.id}}" class="btnBuy">
                            <span class="icon_cleaner icon_cleaner_buy"></span>
                            <span class="text-el">� �������</span>
                        </button>
                    </div>
                    <div class="btn-buy btn-green">
                        <button type="button" class="btnBuy orderBut" data-id="{{ entry.id }}">
                            <span class="icon_cleaner icon_cleaner_buy"></span>
                            <span class="text-el">������</span>
                        </button>
                    </div>
                </div>
                {% endif %}
                
            </div>
</div>
<!-- End. Collect information about Variants, for future processing -->
<div class="frame-without-top" style="position: relative; top: 40px;">
    <!-- Wish List & Compare List buttons -->
    <div class="frame-wish-compare-list no-vis-table t-a_j">
                <div class="frame-btn-comp">
            <!-- Start. Compare List button -->
            <div class="btn-compare">
                <div class="toCompare btnCompare {% if (entry.compare) %}active{% endif %}" data-id="{{entry.id}}" type="button" data-title="��������" data-firtitle="��������" data-sectitle="� ���������" data-rel="tooltip">
                <span class="niceCheck nstcheck {% if (entry.compare) %}active{% endif %}">
                    <input type="checkbox" {% if (entry.compare) %}cheked="cheked"{% endif %}>
                </span>
                <span class="text-el d_l">��������</span>
            </div>
        </div>
        <!-- End. Compare List button -->
    </div>
            <!-- Start. Wish list buttons -->
            <!--
        <div class="frame-btn-wish js-variant-5853 js-variant d_i-b_">
        <div class="btnWish btn-wish" data-id="5853">
    <button class="toWishlist isDrop" type="button" data-rel="tooltip" data-title="� ���������" data-drop="#dropAuth">
        <span class="icon_wish"></span>
        <span class="text-el d_l">� ���������</span>
    </button>
    <button class="inWishlist" type="button" data-rel="tooltip" data-title="� ��������x" style="display: none;">
        <span class="icon_wish"></span>
        <span class="text-el d_l">� ��������x</span>
    </button>
</div>
<script>
langs["Create list"] = '������� ������';
langs["Wrong list name"] = '�������� ��� ������';
langs["Already in Wish List"] = '��� � ������ �������';
langs["List does not chosen"] = '������ �� �����';
langs["Limit of Wish List finished "] = '����� ������� ��������� ��������';
</script>    </div> -->
        <!-- End. wish list buttons -->
    </div>
<!-- End. Wish List & Compare List buttons -->
</div>
<!-- End. Collect information about Variants, for future processing -->
<div class="frame-without-top" style="position: relative; top: 62px;">
    <div class="no-vis-table">
        <!--Start. Description-->
                <div class="short-desc">
             {{entry.annotation|truncateHTML(30,'...')}}        </div>
                <!-- End. Description-->
    </div>
</div>
</div>
<!-- Start. Remove buttons if compare-->
<!-- End. Remove buttons if compare-->

<!-- Start. For wishlist page-->
<!-- End. For wishlist page-->
<div class="decor-element" style="height: 335px; width: 100%; position: absolute; right: auto; left: 0px; bottom: auto; top: 15px;"></div>
</li>
{% endfor %}
                </ul>
                
                {% if (pages.true) %}
                <!-- render pagination-->
                <div class="pagination"><ul class="f-s_0">

                {% if (prevlink.true) %}
                <li class="prev-page">
                    {{ prevlink.link }}
                </li>
                {% endif %}

                {{ pages.print }}

                {% if (nextlink.true) %}
                <li class="next-page">
                    {{ nextlink.link }}
                </li>
                {% endif %}

                </ul></div>
                {% endif %}
            </div>
        </div>
    </div>
