<div class="frame-crumbs">
    <div class="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
    <div class="container">
        <ul class="items items-crumbs">
            <li class="btn-crumb">
                <a href="" typeof="v:Breadcrumb">
                    <span class="text-el">Главная</span>
                    <span class="divider">/</span>
                </a>
            </li>
                             <li class="btn-crumb">
                                            <button typeof="v:Breadcrumb" disabled="disabled">
                            <span class="text-el">Поиск</span>
                        </button>
                                    </li>
                    </ul>
    </div>
</div></div>

{% if (entries) %}
<div class="frame-inside">
    <div class="container">
                    <div class="left-catalog">
                <form method="GET" action="" id="catalogForm">
                    <input type="hidden" name="order" value="">
                    <input type="hidden" name="text" value="Fly">
                    <input type="hidden" name="category" value="">
                    <input type="hidden" name="user_per_page" value="">
                </form>
                <div class="frame-category-menu layout-highlight">
                    <div class="title-menu-category">
                        <div class="title-default">
                            <div class="title">Категории:</div>
                        </div>
                    </div>
                    <div class="inside-padd">
                        <!--
                        <nav>
                                                            <ul class="nav nav-vertical nav-category">
                                    <li>
                                        <span>Телефония, МР3-плееры, GPS</span>
                                    </li>
                                                                                                                        <li>
                                                <a rel="nofollow" data-id="930" href="http://fluid.imagecmsdemo.net/shop/search?text=Fly&amp;category=930"><span class="text-el">Мобильные телефоны</span> <span class="count">(18)</span></a>
                                                                                    </li>
                                                                    </ul>
                                                    </nav>
                                                    -->
                    </div>
                </div>
            </div>
                <div class="right-catalog">
                            <div class="f-s_0 title-category">
                    <div class="frame-title">
                        <h1 class="title"><span class="s-t">Результаты поиска</span> <span class="what-search">«{{ search_request }}»</span></h1>
                    </div>
                    <span class="count">(Найдено 18 товаров)</span>
                </div>
                                        <div class="frame-header-category">
        <div class="header-category f-s_0">
            <div class="inside-padd">
                <!-- Start. Order by block -->
                <div class="frame-sort d_i-b v-a_t">
                    <div class="lineForm">
                        <div class="cusel sort" id="cuselFrame-sort" style="width:176px" tabindex="0"><div class="cuselFrameRight"></div><div class="cuselText">Акции</div><div class="cusel-scroll-wrap" style="display: none; visibility: visible;"><div class="jScrollPaneContainer" style="height: 192px; width: 180px;"><div class="cusel-scroll-pane" id="cusel-scroll-sort" style="overflow: hidden; width: 180px; height: 192px; padding: 0px;">
                                                                                        <span val="action" class="cuselActive">Акции</span>
                                                            <span val="price">От дешевых к дорогим</span>
                                                            <span val="price_desc">От дорогих к дешевым</span>
                                                            <span val="hit">Популярные</span>
                                                            <span val="rating">Рейтинг</span>
                                                            <span val="hot">Новинки</span>
                                                            <span val="views">По количеству просмотров</span>
                                                            <span val="name">По названию (А-Я)</span>
                                                    </div></div></div><input type="hidden" id="sort" name="order" value="action"></div>
                    </div>
                </div>
                <!-- End. Order by block -->
                <!--         Start. Product per page  -->
                <div class="frame-count-onpage d_i-b v-a_t">
                                                                                    <div class="lineForm">
                        <!--                Load settings-->
                                                <div class="cusel" id="cuselFrame-sort2" style="width:159px" tabindex="0"><div class="cuselFrameRight"></div><div class="cuselText">12 товаров на странице</div><div class="cusel-scroll-wrap" style="display: none; visibility: visible;"><div class="cusel-scroll-pane" id="cusel-scroll-sort2">
                                                            <span selected="selected" val="12" class="cuselActive">12 товаров на странице</span>
                                                            <span val="24">24 товара на странице</span>
                                                            <span val="48">48 товаров на странице</span>
                                                    </div></div><input type="hidden" id="sort2" name="user_per_page" value="12"></div>
                    </div>
                </div>
                <!--         End. Product per page  -->
                <!--        Start. Show products as list or table-->
                <nav class="frame-catalog-view d_i-b v-a_t">
                    <ul class="tabs tabs-list-table" data-elchange="#items-catalog-main" data-cookie="listtable">
                        <li class="active">
                            <button type="button" data-href="tablemini" data-title="Мини таблица" data-rel="tooltip">
                                <span class="icon_tablemini_cat"></span><span class="text-el">Мини таблица</span>
                            </button>
                        </li>
                        <li class="">
                            <button type="button" data-href="list" data-title="Список" data-rel="tooltip">
                                <span class="icon_list_cat"></span><span class="text-el">Список</span>
                            </button>
                        </li>
                                            </ul>
                </nav>
                <!--        End. Show products as list or table-->
            </div>
            <!--                Start. if $CI->uri->segment(2) == "search" then show hidden field-->
                            <input type="hidden" name="text" value="Fly">
                        <!--                End. if $CI->uri->segment(2) == "search" then show hidden field-->
        </div>
    </div>
               <!-- Start.If count products in category > 0 then show products list and pagination links -->
                                <ul class="animateListItems items items-catalog items-product tablemini" id="items-catalog-main">
                    <!-- Include template for one product item-->
                    


{% for entry in entries %}
<li class="globalFrameProduct to-cart" data-pos="top">
    <!-- Start. Photo & Name product -->
    <a href="{{entry.fulllink}}" class="frame-photo-title" title="Мобильный телефон Fly E141 TV Dual SIM Black">
        <span class="photo-block">
            <span class="helper"></span>
            {% if (entry.image_filepath) %}<img src='{{home}}/uploads/eshop/products/thumb/{{entry.image_filepath}}' class="vImg">{% else %}<img src='{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg' class="vImg">{% endif %}
                                            </span>
        <span class="title">{{ entry.name }}</span>
    </a>
    <!-- End. Photo & Name product -->
    <div class="description">
        <!-- Start. article & variant name & brand name -->
                <div class="frame-variant-name-code">
                        <span class="frame-variant-code frameVariantCode">Артикул:
                <span class="code js-code">
                                        {{ entry.code }}                                    </span>
            </span>
                                                            <span class="frame-item-brand">Бренд:
                <span class="code js-code">
                                        <a href="http://fluid.imagecmsdemo.net/shop/brand/fly">
                        Fly                    </a>
                                    </span>
            </span>
                    </div>
                <!-- End. article & variant name & brand name -->
                        <div class="frame-star f-s_0">
                <div class="star">
        <div id="star_rating_5623" class="productRate star-small">
            <div style="width: 80%"></div>
        </div>
    </div>
            <a href="http://fluid.imagecmsdemo.net/shop/product/mobilnyi-telefon-fly-e141-tv-dual-sim-black#comment" class="count-response">
                4                отзыва            </a>
        </div>
                        <!-- Start. Prices-->
        <div class="frame-prices f-s_0">
                                                <!-- Start. Product price-->
            <span class="current-prices f-s_0">
                <span class="price-new">
                    <span>
<span class="price priceVariant">{% if (entry.price) %}{{ entry.price }}{% else %}0{% endif %}</span> $
                    </span>
                </span>
                                <span class="price-add">
                    <span>
<span class="price addCurrPrice">{% if (entry.compare_price) %}{{ entry.compare_price }}{% else %}0{% endif %}</span> €
                    </span>
                </span>
                            </span>
                        <!-- End. Product price-->
        </div>
        <!-- End. Prices-->
                        
                <div class="funcs-buttons frame-without-top" style="position: relative; top: 0px;">
            <div class="no-vis-table">
                <!-- Start. Collect information about Variants, for future processing -->
                                                                                                <div class="frame-count-buy js-variant-5853 js-variant">
                    <form method="POST" action="/shop/cart/addProductByVariantId/5853">
                        <div class="btn-buy btn-cart d_n">
                            <button type="button" data-id="5853" class="btnBuy">
                            <span class="icon_cleaner icon_cleaner_buy"></span>
                            <span class="text-el">В корзине</span>
                        </button>
                    </div>
                    <div class="btn-buy">
                        <button type="button" onclick="Shop.Cart.add($(this).closest(&quot;form&quot;).serialize(), &quot;5853&quot;)" class="btnBuy infoBut" data-id="5853" data-name="Мобильный телефон Fly E141 TV Dual SIM Black" data-vname="Мобильный телефон Fly E141 TV Dual SIM Black" data-number="200236" data-price="69" data-add-price="55" data-orig-price="" data-medium-image="
                                                /uploads/shop/products/medium/5623_main_origin.jpg                        " data-img="
                                                /uploads/shop/products/small/5623_main_origin.jpg                        " data-url="http://fluid.imagecmsdemo.net/shop/product/mobilnyi-telefon-fly-e141-tv-dual-sim-black" data-maxcount="1">
                        <span class="icon_cleaner icon_cleaner_buy"></span>
                        <span class="text-el">Купить</span>
                    </button>
                </div>
                <input type="hidden" value="c6766dff44774539893ae7b98460a47f" name="cms_token">            </form>
        </div>
            </div>
</div>
<!-- End. Collect information about Variants, for future processing -->
<div class="frame-without-top" style="position: relative; top: 40px;">
    <!-- Wish List & Compare List buttons -->
    <div class="frame-wish-compare-list no-vis-table t-a_j">
                <div class="frame-btn-comp">
            <!-- Start. Compare List button -->
            <div class="btn-compare">
                <div class="toCompare btnCompare" data-id="5623" type="button" data-title="Сравнить" data-firtitle="Сравнить" data-sectitle="В сравнении" data-rel="tooltip">
                <span class="niceCheck nstcheck">
                    <input type="checkbox">
                </span>
                <span class="text-el d_l">Сравнить</span>
            </div>
        </div>
        <!-- End. Compare List button -->
    </div>
            <!-- Start. Wish list buttons -->
        <div class="frame-btn-wish js-variant-5853 js-variant d_i-b_">
        <div class="btnWish btn-wish" data-id="5853">
    <button class="toWishlist isDrop" type="button" data-rel="tooltip" data-title="В избранные" data-drop="#dropAuth">
        <span class="icon_wish"></span>
        <span class="text-el d_l">В избранные</span>
    </button>
    <button class="inWishlist" type="button" data-rel="tooltip" data-title="В избранныx" style="display: none;">
        <span class="icon_wish"></span>
        <span class="text-el d_l">В избранныx</span>
    </button>
</div>
<script>
langs["Create list"] = 'Создать список';
langs["Wrong list name"] = 'Неверное имя списка';
langs["Already in Wish List"] = 'Уже в Списке Желаний';
langs["List does not chosen"] = 'Список не обран';
langs["Limit of Wish List finished "] = 'Лимит списков пожеланий исчерпан';
</script>    </div>
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

{% else %}

<div class="frame-inside">
    <div class="container">
                <div class="right-catalog" style="width:100% !important;margin-left: 0;">
                                        <div class="msg layout-highlight layout-highlight-msg">
                    <div class="info">
                        <span class="icon_info"></span>
                        <span class="text-el">Не найдено товаров</span>
                    </div>
                </div>
                                                <!--Start. Pagination -->
                        <!-- End pagination -->
        </div>
    </div>
</div>

{% endif %}
