[TWIG]
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{{ lang['langcode'] }}" lang="{{ lang['langcode'] }}" dir="ltr">
  <head>
    <meta http-equiv="content-type" content="text/html; charset={{ lang['encoding'] }}" />
    <meta http-equiv="content-language" content="{{ lang['langcode'] }}" />
    <meta name="generator" content="{{ what }} {{ version }}" />
    <meta name="document-state" content="dynamic" />
    {{ htmlvars }}
    
    <link rel="stylesheet" href="{{ tpl_url }}/css/style.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/colorscheme.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/color.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/adaptive.css">
    {% if pluginIsActive('rss_export') %}
    <link href="{{ home }}/rss.xml" rel="alternate" type="application/rss+xml" title="RSS" />
    {% endif %}
    <script src="{{ tpl_url }}/js/jquery-1.8.3.min.js">
    </script>
    
    <script type="text/javascript">
      var locale = "";
    </script>
    
    <script type="text/javascript">
      var curr = '$',
          cartItemsProductsId = ["1035"],
          nextCs = '€',
          nextCsCond = nextCs == '' ? false : true,
          pricePrecision = parseInt(''),
          checkProdStock = "", //use in plugin plus minus
          inServerCompare = parseInt("0"),
          inServerWishList = parseInt("0"),
          countViewProd = parseInt("6"),
          theme = "http://fluid.imagecmsdemo.net/templates/fluid/",
          siteUrl = "http://fluid.imagecmsdemo.net/",
          colorScheme = "css/color_scheme_1",
          isLogin = "0" === '1' ? true : false,
          typePage = "main",
          typeMenu = "row";
      text = {
        search: function(text) {
          return 'Введите более' + ' ' + text + ' символов';
        }
        ,
        error: {
          notLogin: 'В список желаний могут добавлять только авторизированные пользователи',
          fewsize: function(text) {
            return 'Выберите размер меньше или равно' + ' ' + text + ' пикселей';
          }
          ,
          enterName: 'Введите название'
        }
      }
        
        text.inCart = 'В корзине';
  text.pc = 'шт.';
  text.quant = 'Кол-во:';
  text.sum = 'Сумма:';
  text.toCart = 'Купить';
  text.pcs = 'Количество:';
  text.kits = 'Комплектов:';
  text.captchaText = 'Код протекции';
  text.plurProd = ['товар', 'товара', 'товаров'];
  text.plurKits = ['набор', 'набора', 'наборов'];
  text.plurComments = ['отзыв', 'отзыва', 'отзывов'];
      </script>
      
      <script src="{{ tpl_url }}/js/settings.js"></script>
      <script src="{{ tpl_url }}/js/_adaptive.js"></script>
      <script src="{{ tpl_url }}/js/jquery.cycle.all.min.js"></script>
      
      <script type="text/javascript" src="{{ scriptLibrary }}/functions.js">
      </script>
      <script type="text/javascript" src="{{ scriptLibrary }}/ajax.js">
      </script>
      <title>
        {{ titles }}
      </title>
      <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=0">
      <!--[if lte IE 9]>
<script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js">
</script>
<![endif]-->
      <!--[if lte IE 8]>
<link rel="stylesheet" type="text/css" href="{{ tpl_url }}/css/lte_ie_8.css" />
<![endif]-->
      <!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="{{ tpl_url }}/css/ie_7.css" />
<![endif]-->
  </head>
  
  <body class="isChrome not-js main">
    <script>
      if ('ontouchstart' in document.documentElement)
        document.body.className += ' isTouch';
      else
        document.body.className += ' notTouch';
    </script>
    
    <script>
      var langs = new Object();
      function lang(value) {
        return  langs[value];
      }
    </script>
    
    
    <div id="loading-layer">
      <img src="{{ tpl_url }}/img/loading.gif" alt="" />
    </div>
    <div class="main-body">
      <div class="fon-header">
        <header>
          <div class="top-header">
            <div class="container">
              <nav class="left-header">
                <ul class="nav nav-default-inline mq-w-768 mq-max mq-block" data-mq-max="768" data-mq-min="0" data-mq-target="#topMenuInMainMenu">
                  
                  <li>
                    <a href="{{home}}/o-magazine" target="_self" title="О компании">
                      О компании
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}/dostavka" target="_self" title="Доставка">
                      Доставка
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}/oplata" target="_self" title="Оплата">
                      Оплата
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}/novosti" target="_self" title="Новости">
                      Новости
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}/shop/brand" target="_self" title="Бренды">
                      Бренды
                    </a>
                  </li>
                  
                  <li>
                    <a href="{{home}}/kontakty" target="_self" title="Контакты">
                      Контакты
                    </a>
                  </li>
                  
                </ul>
                <ul class="nav nav-default-inline mq-w-768 mq-min mq-block">
                  <li>
                    <button type="button" data-drop="#topMenuInMainMenu" data-place="noinherit" data-overlay-opacity="0" class="isDrop">
                      Меню                    
                  </button>
                  <ul class="drop drop-style" id="topMenuInMainMenu">
                  </ul>
              </li>
          </ul>
      </nav>
      <ul class="items items-user-toolbar-top">
        <li>
          
          <div class="wish-list-btn tinyWishList">
            <button data-href="{{home}}/wishlist" class="isDrop">
              <span class="js-empty empty" style="display: block">
                <span class="icon_wish_list">
                </span>
                <span class="text-el">
                  Избранные 
                </span>
              </span>
              <span class="js-no-empty no-empty" style="display: none;">
                <span class="icon_wish_list">
                </span>
                <span class="text-el">
                  Избранные 
                </span>
                <span class="wishListCount">
                  0
                </span>
              </span>
            </button>
          </div>
          
          
        </li>
        <li class="compare-button">
          <div class="compare-list-btn tinyCompareList">
            <button data-href="{{home}}/shop/compare" class="isDrop">
              <span class="js-empty empty" style="display: block">
                <span class="icon_compare_list">
                </span>
                <span class="text-el">
                  Сравнение 
                </span>
              </span>
              <span class="js-no-empty no-empty" style="display: none;">
                <span class="icon_compare_list">
                </span>
                <span class="text-el">
                  Сравнение 
                </span>
                <span class="compareListCount">
                  0
                </span>
              </span>
            </button>
          </div>
          <div class="drop drop-info drop-info-compare">
            <span class="helper">
            </span>
            <span class="text-el">
              Ваш список 
              <br>
              “Список сравнения” пуст    
            </span>
          </div>
        </li>
        <!--Start. Top menu and authentication data block-->
        <li class="btn-personal-area">
          <button type="button" id="loginButton" data-drop=".drop-enter" data-source="{{home}}/auth" class="isDrop">
            <span class="icon_enter">
            </span>
            <span class="text-el">
              Вход
            </span>
          </button>
        </li>
        <!--Else show link for personal cabinet -->
        <!--End. Top menu and authentication data block-->
      </ul>
                          </div>
                      </div>
                      <div class="content-header">
                        <div class="container">
                          <div class="left-content-header t-a_j">
                            <!--        Logo-->
                            <div class="logo-out">
                              <span class="logo">
                                <img src="{{ tpl_url }}/img/logo.png" alt="logo">
                              </span>
                            </div>
          <!--                Start. contacts block-->
          <div class="top-search">
            <button class="small-search-btn">
              <span class="icon_small_search">
              </span>
            </button>
            <form name="search" method="post" action="{{home}}/eshop/search/" class="search-form">
              <input type="text" class="input-search" id="inputString" name="keywords" autocomplete="off" value="" placeholder="Поиск по сайту">
              <span class="btn-search">
                <button type="submit">
                  <span class="icon_search">
                  </span>
                </button>
              </span>
              <div class="icon_times_drop">
              </div>
              <div id="suggestions" class="drop drop-search">
              </div>
            </form>
          </div>
          <div class="phones-header">
            <div class="frame-ico">
              <span class="icon_phone_header">
              </span>
            </div>
            <div>
              <div class="f-s_0">
                <span class="phone">
                  <span class="phone-number">
                    0 (800) 567-43-21
                  </span>
                  <span class="phone-number">
                    0 (800) 567-43-21
                  </span>
                </span>
              </div>
              <div class="btn-order-call">
                <button data-drop="#ordercall" data-tab="true" data-source="{{home}}/shop/callback" class="isDrop">
                  <span class="icon_order_call">
                  </span>
                  <span class="text-el d_l">
                    Заказать звонок
                  </span>
                </button>
              </div>
            </div>
          </div>
          <div class="frame-time-work">
            <div class="frame-ico">
              <span class="icon_work">
              </span>
            </div>
                          <div>
                            <div>
                              Работаем: 
                              <span class="text-el">
                                Пн–Пт 09:00–20:00,
                                <br>
                                Сб 09:00–17:00, Вс выходной
                              </span>
                            </div>
                            
                          </div>
          </div>
          <!-- End. Contacts block-->
          <!-- Start. Include cart data template-->
          <div id="tinyBask" class="frame-cleaner">
            
            {{ plugin_ebasket }}

          </div>
          <!-- End. Include cart data template-->
      </div>
  </div>
                      </div>
                  </header>
                  <div class="frame-menu-main horizontal-menu">
                    <!--    menu-row-category || menu-col-category-->
                    <div class="container">
                      <div class="menu-main menu-row-category">
                        <nav>
                          <table>
                            <tbody>
                              <tr>
                                <td class="mq-w-480 mq-min mq-table-cell">
                                  <div class="frame-item-menu-out frameItemMenu">
                                    <div class="frame-title is-sub">
                                      <span class="title title-united">
                                        <span class="helper" style="height: 49px;">
                                        </span>
                                        <span class="text-el">
                                          Каталог
                                        </span>
                                      </span>
                                      <span class="icon-is-sub">
                                      </span>
                                    </div>
                                    <div class="frame-drop-menu left-drop" id="unitedCatalog" style="display: none; left: 0px;">
                                    </div>
                                  </div>
                                </td>
                                <td data-mq-max="768" data-mq-min="0" data-mq-target="#unitedCatalog" class="mq-w-480 mq-max mq-table-cell">
                                  <table>
                                    <tbody>
                                      <tr>
                                        <td>
                                          <div class="frame-item-menu frameItemMenu">
                                            <div class="frame-title is-sub">
                                              <a class="title">
                                                <span class="helper" style="height: 49px;">
                                                </span>
                                                <span class="text-el">
                                                  Категории
                                                </span>
                                              </a>
                                            </div>
                                            <div class="frame-drop-menu left-drop" style="display: none; left: 0px;">
                                            
                                              {{ callPlugin('eshop.show_catz_tree') }}
                                              <!--
                                              <ul class="items">
                                                <li class="column_0">
                                                  <a href="{{home}}/shop/category/telefoniia-pleery-gps/telefony" title="Телефоны" class="title-category-l1  is-sub">
                                                    <span class="helper">
                                                    </span>
                                                    <span class="text-el">
                                                      Телефоны
                                                    </span>
                                                  </a>
                                                  <div class="frame-l2">
                                                    <ul class="items">
                                                      <li class="column2_0">
                                                        <a href="{{home}}/shop/category/telefoniia-pleery-gps/telefony/mobilnye-telefony" title="Мобильные телефоны">
                                                          Мобильные телефоны
                                                        </a>
                                                      </li>
                                                    </ul>
                                                  </div>
                                                </li>
                                              </ul>
                                              -->
                                            
                                            </div>
                                            
                                          </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}/shop/category/domashnee-video" title="Домашнее видео" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Домашнее видео
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}/shop/category/detskie-tovary" title="Детские товары" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Детские товары
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}/shop/category/aktivnyi-otdyh-i-turizm" title="Активный отдых и туризм" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Активный отдых и туризм
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      <td>
                                        <div class="frame-item-menu frameItemMenu">
                                          <div class="frame-title">
                                            <a href="{{home}}/shop/category/muzykalnye-instrumenty" title="Музыкальные инструменты" class="title">
                                              <span class="helper" style="height: 49px;">
                                              </span>
                                              <span class="text-el">
                                                Музыкальные инструменты
                                              </span>
                                            </a>
                                          </div>
                                          
                                        </div>
                                      </td>
                                      
                                  </tr>
                              </tbody>
                          </table>
                      </td>
                  </tr>
              </tbody>
          </table>
      </nav>
  </div>
                      </div>
                      <script>
                        function beforeShowSearch(el, drop) {
                          el.closest('td').prevAll().hide();
                          el.css('position', 'static');
                          drop.removeAttr('style');
                        }
                        function afterHideSearch(el, drop) {
                          el.closest('td').find('form').hide();
                          el.closest('td').prevAll().show().css('display', '');
                          el.css('position', '');
                        }
                        function closedSearch(el, drop){
                          drop.removeAttr('style');
                        }
                      </script>
                  </div>
                {% if isHandler('news:main') %}
                  <div class="frame-baner frame-baner-start_page">
                    <section class="carousel-js-css baner container cycleFrame resize is-small" style="height: 218px;">
                      <!--remove class="resize" if not resize-->
                      <div class="p_r" style="height: 100%;">
                        <div class="content-carousel">
                          <div class="f_l">
                            <p>
                              <a href="{{home}}/shop/product/mobilnyi-telefon-sony-xperia-v-lt25i-black">
                                <img src="{{ tpl_url }}/img/1.jpg" alt="">
                              </a>
                            </p>
                          </div>
                          <div class="f_r">
                            <p>
                              <a href="{{home}}/shop/product/karta-pamiati-kingston-microsd-16-gb-sdc4-16gb">
                                <img src="{{ tpl_url }}/img/3.jpg" alt="">
                              </a>
                            </p>
                            <p>
                              <a href="{{home}}/shop/product/garnitura-samsung-bhm1100-black">
                                <img src="{{ tpl_url }}/img/4.jpg" alt="">
                              </a>
                            </p>
                          </div>
                          <ul class="cycle">
                            <!--remove class="cycle" if not cycle-->
                            <li>
                              <a href="{{home}}/shop/product/mobilnyi-telefon-lg-nexus-4-e960-black">
                                <img src="{{ tpl_url }}/img/2.jpg" alt="" style="display: inline;">
                              </a>
                            </li>
                          </ul>
                          
                          <div class="pager">
                          </div>
                          <script>
                            langs["Message"] = 'Сообщение';
                            langs["Banner (s) successfully removed"] = 'Баннер (ы) успешно удален(ы)';
                          </script>
                          
                        </div>
                      </div>
                      <div class="group-button-carousel">
                        <button type="button" class="prev arrow">
                          <span class="icon_arrow_p">
                          </span>
                        </button>
                        <button type="button" class="next arrow">
                          <span class="icon_arrow_n">
                          </span>
                        </button>
                      </div>
                    </section>
                  </div>
                {% endif %}
            </div>
            
            
            <div class="content">
              {% if isHandler('news:main') %}
              <div class="page-main">
                <div class="container f-s_0">
                  <ul class="tabs tabs-special-proposition">
                    <li class="active">
                      <button data-href="#popular_products" type="button">
                        Хиты
                      </button>
                    </li>
                    <li>
                      <button data-href="#new_products" type="button">
                        Новинки
                      </button>
                    </li>
                    <li>
                      <button data-href="#action_products" type="button">
                        Спецпредложения
                      </button>
                    </li>
                  </ul>
                  <div class="frame-tabs-ref">
                    <div id="popular_products">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="carousel-js-css items-carousel jcarousel-container jcarousel-container-horizontal iscarousel" style="position: relative; display: block;">
                              <div class="content-carousel container">
                                <div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;">
                                  <ul class="items items-catalog items-h-carousel items-product animateListItems jcarousel-list jcarousel-list-horizontal" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 1904px;">
                                    
                                    {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'stocked', 'template': 'block_eshop'}) }}

                                  </ul>
                                </div>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                    
                    <div id="new_products" style="">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="items-carousel carousel-js-css">
                              <div class="content-carousel container">
                                <ul class="items items-catalog items-h-carousel items-product animateListItems">
                                  
                                  {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'last', 'template': 'block_eshop'}) }}

                                </ul>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                    
                    <div id="action_products" style="">
                      <div class="horizontal-carousel">
                        <section class="special-proposition">
                          <div class="big-container">
                            <div class="items-carousel carousel-js-css">
                              <div class="content-carousel container">
                                <ul class="items items-catalog items-h-carousel items-product animateListItems">
                                  
                                  {{ callPlugin('eshop.show', {'number' : 10, 'mode' : 'featured', 'template': 'block_eshop'}) }}

                                </ul>
                              </div>
                              <div class="group-button-carousel">
                                <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                                  <span class="icon_arrow_p">
                                  </span>
                                </button>
                                <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                                  <span class="icon_arrow_n">
                                  </span>
                                </button>
                              </div>
                            </div>
                          </div>
                        </section>
                      </div>
                    </div>
                  </div>
                  <div class="frame-news">
                    <div class="title-news">
                      <div class="frame-title">
                        <div class="title">
                          <a href="{{home}}/novosti" class="t-d_n f-s_0 s-all-d">
                            <span class="text-el">
                              Новости и акции
                            </span>
                            <span class="icon_arrow">
                            </span>
                          </a>
                        </div>
                      </div>
                    </div>
                    <ul class="items items-news">
                      <li class="is-photo">
                        <a href="{{home}}/novosti/8r-biznes-v-seti" class="frame-photo-title">
                          <span class="photo-block">
                            <span class="helper">
                            </span>
                            <img src="{{ tpl_url }}/img/new1.jpg" alt="">
                          </span>
                          <span class="title">
                            8Р - Бизнес в сети
                          </span>
                        </a>
                        <div class="description">
                          <p>
                            Редкий предприниматель в наше время не задается вопросом: Как с помощью интернета увеличить продажи?
                          </p>
                          
                          <div class="date f-s_0">
                            <span class="icon_time">
                            </span>
                            <span class="text-el">
                            </span>
                            <span class="day">
                              02 
                            </span>
                            <span class="month">
                              Марта 
                            </span>
                            <span class="year">
                              2013 
                            </span>
                          </div>
                        </div>
                      </li>
                      <li class="is-photo">
                        <a href="{{home}}/novosti/kak-dobavit-sait-v-iandeks-i-gugl-sovety-nachinaiushchim-vebmasteram" class="frame-photo-title">
                          <span class="photo-block">
                            <span class="helper">
                            </span>
                            <img src="{{ tpl_url }}/img/new2.jpg" alt="">
                          </span>
                          <span class="title">
                            Как добавить сайт в Яндекс и Гугл. Советы начинающим вебмастерам
                          </span>
                        </a>
                        <div class="description">
                          <p>
                            Создание сайта само по себе является нелегким и довольно продолжительным процессом.
                          </p>
                          
                          <div class="date f-s_0">
                            <span class="icon_time">
                            </span>
                            <span class="text-el">
                            </span>
                            <span class="day">
                              02 
                            </span>
                            <span class="month">
                              Марта 
                            </span>
                            <span class="year">
                              2013 
                            </span>
                          </div>
                        </div>
                      </li>
                      <li class="is-photo">
                        <a href="{{home}}/novosti/kak-raskrutit-sait-metody-poiskovogo-prodvizheniia" class="frame-photo-title">
                          <span class="photo-block">
                            <span class="helper">
                            </span>
                            <img src="{{ tpl_url }}/img/new3.jpg" alt="">
                          </span>
                          <span class="title">
                            Как раскрутить сайт? Методы поискового продвижения
                          </span>
                        </a>
                        <div class="description">
                          <p>
                            Наличие корпоративного сайта уже стало стандартом де-факто и знаком хорошего тона любой компании, а не только известных игроков рынка.
                          </p>
                          
                          <div class="date f-s_0">
                            <span class="icon_time">
                            </span>
                            <span class="text-el">
                            </span>
                            <span class="day">
                              02 
                            </span>
                            <span class="month">
                              Марта 
                            </span>
                            <span class="year">
                              2013 
                            </span>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              {% else %}
              {{ mainblock }}
              {% endif %}
              
              {% if isHandler('news:main') %}
              <div class="container">
                <div id="ViewedProducts">
                  
                  
                  <div class="horizontal-carousel">
                    <section class="special-proposition frame-view-products">
                      <div class="title-proposition-v">
                        <div class="frame-title">
                          <div class="title">
                            Вы уже смотрели
                          </div>
                        </div>
                      </div>
                      <div class="carousel-js-css items-carousel jcarousel-container jcarousel-container-horizontal iscarousel" style="position: relative; display: block;">
                        <div class="content-carousel container">
                          <div class="jcarousel-clip jcarousel-clip-horizontal" style="position: relative;">
                            <ul class="items items-catalog items-h-carousel items-product jcarousel-list jcarousel-list-horizontal" style="overflow: hidden; position: relative; top: 0px; margin: 0px; padding: 0px; left: 0px; width: 2568px;">
                              
                              
                              
                              
                              <li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-1 jcarousel-item-1-horizontal" data-pos="top" jcarouselindex="1" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/mobilnyi-telefon-lg-nexus-4-e960-black" class="frame-photo-title" title="Мобильный телефон LG Nexus 4 E960 black">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/1105_main_origin.jpg" alt="" class="vImg">
                                    <span class="product-status hit">
                                    </span>
                                    
                                  </span>
                                  <span class="title">
                                    Мобильный телефон LG Nexus 4 E960 black
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <div class="frame-star f-s_0">
                                    <div class="star">
                                      <div id="star_rating_1105" class="productRate star-small">
                                        <div style="width: 100%">
                                        </div>
                                      </div>
                                    </div>
                                    <a href="{{home}}/shop/product/mobilnyi-telefon-lg-nexus-4-e960-black#comment" class="count-response">
                                      5                отзывов            
                                    </a>
                                  </div>
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            6.619
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            5.295
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                              
                              
                              
                              <li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-2 jcarousel-item-2-horizontal" data-pos="top" jcarouselindex="2" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/nokia-lumia-920-white" class="frame-photo-title" title="Nokia Lumia 920 White">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/1108_main_origin.jpg" alt="" class="vImg">
                                    <span class="product-status nowelty">
                                    </span>
                                    
                                  </span>
                                  <span class="title">
                                    Nokia Lumia 920 White
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            5.445
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            4.356
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                              
                              
                              
                              <li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-3 jcarousel-item-3-horizontal" data-pos="top" jcarouselindex="3" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/mobilnyi-telefon-sony-xperia-v-lt25i-black" class="frame-photo-title" title="Мобильный телефон Sony Xperia V LT25i Black ">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/1104_main_origin.jpg" alt="" class="vImg">
                                    <span class="product-status hit">
                                    </span>
                                    
                                  </span>
                                  <span class="title">
                                    Мобильный телефон Sony Xperia V LT25i Black 
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <div class="frame-star f-s_0">
                                    <div class="star">
                                      <div id="star_rating_1104" class="productRate star-small">
                                        <div style="width: 80%">
                                        </div>
                                      </div>
                                    </div>
                                    <a href="{{home}}/shop/product/mobilnyi-telefon-sony-xperia-v-lt25i-black#comment" class="count-response">
                                      14                отзывов            
                                    </a>
                                  </div>
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            6.249
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            4.999
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                              
                              
                              
                              <li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-4 jcarousel-item-4-horizontal" data-pos="top" jcarouselindex="4" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/smartfon-samsung-gt-s7562-galaxy-s-duos-zka-black" class="frame-photo-title" title="Смартфон Samsung GT-S7562 Galaxy S Duos ZKA Black">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/12045_main_origin.jpg" alt="Смартфон Samsung GT-S7562 Galaxy S Duos ZKA Black" class="vImg">
                                  </span>
                                  <span class="title">
                                    Смартфон Samsung GT-S7562 Galaxy S Duos ZKA Black
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            356
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            285
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                              
                              
                              
                              <li class="globalFrameProduct to-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-5 jcarousel-item-5-horizontal" data-pos="top" jcarouselindex="5" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/elektrogitara-seriyi-badwater-as820br" class="frame-photo-title" title="АКУСТИЧНА ГІТАРА СЕРІЇ STANDARD D350CEG">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/4959_main_origin.jpg" alt="АКУСТИЧНА ГІТАРА СЕРІЇ STANDARD D350CEG" class="vImg">
                                  </span>
                                  <span class="title">
                                    АКУСТИЧНА ГІТАРА СЕРІЇ STANDARD D350CEG
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            2.426
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            1.941
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                              
                              
                              
                              <li class="globalFrameProduct in-cart jcarousel-item jcarousel-item-horizontal jcarousel-item-6 jcarousel-item-6-horizontal" data-pos="top" jcarouselindex="6" style="float: left; list-style: none; width: 411px;">
                                <!-- Start. Photo & Name product -->
                                <a href="{{home}}/shop/product/3d-led-televizor-samsung-ue65es8007uxua" class="frame-photo-title" title="3D LED телевизор Samsung UE65ES8007UXUA">
                                  <span class="photo-block">
                                    <span class="helper">
                                    </span>
                                    <img src="{{ tpl_url }}/img/937_main_origin.jpg" alt="" class="vImg">
                                    <span class="product-status action">
                                    </span>
                                    
                                  </span>
                                  <span class="title">
                                    3D LED телевизор Samsung UE65ES8007UXUA
                                  </span>
                                </a>
                                <!-- End. Photo & Name product -->
                                <div class="description">
                                  <!-- Start. article & variant name & brand name -->
                                  <!-- End. article & variant name & brand name -->
                                  <!-- Start. Prices-->
                                  <div class="frame-prices f-s_0">
                                    <!-- Start. Check old price-->
                                    <span class="price-discount">
                                      <span>
                                        <span class="price priceOrigVariant">
                                          45.424
                                        </span>
                                        $
                                      </span>
                                    </span>
                                    <!-- End. Check old price-->
                                    <!-- Start. Product price-->
                                    <span class="current-prices f-s_0">
                                      <span class="price-new">
                                        <span>
                                          <span class="price priceVariant">
                                            32.988
                                          </span>
                                          $
                                        </span>
                                      </span>
                                      <span class="price-add">
                                        <span>
                                          <span class="price addCurrPrice">
                                            26.390
                                          </span>
                                          €
                                        </span>
                                      </span>
                                    </span>
                                    <!-- End. Product price-->
                                  </div>
                                  <!-- End. Prices-->
                                  
                                  <!-- End. Collect information about Variants, for future processing -->
                                </div>
                                <!-- Start. Remove buttons if compare-->
                                <!-- End. Remove buttons if compare-->
                                
                                <!-- Start. For wishlist page-->
                                <!-- End. For wishlist page-->
                              </li>
                            </ul>
                          </div>
                        </div>
                        <div class="group-button-carousel">
                          
                          
                          <button type="button" class="prev arrow jcarousel-prev jcarousel-prev-horizontal jcarousel-prev-disabled jcarousel-prev-disabled-horizontal" disabled="disabled" style="display: inline-block;">
                            <span class="icon_arrow_p">
                            </span>
                          </button>
                          <button type="button" class="next arrow jcarousel-next jcarousel-next-horizontal" style="display: inline-block;">
                            <span class="icon_arrow_n">
                            </span>
                          </button>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
              {% endif %}
            </div>
            <div class="h-footer footer-main" data-mq-prop="height" data-mq-prop-pool="height" data-mq-elem-pool="footer" style="height: 456px;">
            </div>
          </div>
          <footer class="footer-main" data-mq-prop="margin-top" data-mq-prop-pref="-" data-mq-prop-pool="height" data-mq-elem-pool="footer" style="margin-top: -456px;">
            {% if isHandler('news:main') %}
            <div class="content-footer">
              <div class="container">
                <div class="frame-box23">
                  <div class="box-3">
                    <div class="inside-padd">
                      <div class="title-h1">
                        <a href="{{home}}/shop/brand" class="t-d_n f-s_0 s-all-d">
                          <span class="text-el">
                            Бренды
                          </span>
                          <span class="icon_arrow">
                          </span>
                        </a>
                      </div>
                      <div class="horizontal-carousel">
                        <div class="big-container frame-brands">
                          <div class="items-carousel">
                            <div class="frame-brands-carousel">
                              <div class="content-carousel">
                                <ul class="items items-brands">
                                  <li>
                                    <a href="{{home}}/shop/brand/pioneer" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img data-original="{{home}}/uploads/shop/brands/pioneer.png" src="{{ tpl_url }}/img/blank.gif" title="Pioneer" alt="Pioneer" class="lazy">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}/shop/brand/sony" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img data-original="{{home}}/uploads/shop/brands/sony.png" src="{{ tpl_url }}/img/blank.gif" title="Sony" alt="Sony" class="lazy">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}/shop/brand/apple" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img data-original="{{home}}/uploads/shop/brands/apple.png" src="{{ tpl_url }}/img/blank.gif" title="Apple" alt="Apple" class="lazy">
                                      </span>
                                    </a>
                                  </li>
                                  <li>
                                    <a href="{{home}}/shop/brand/samsung" class="frame-photo-title">
                                      <span class="photo-block">
                                        <span class="helper">
                                        </span>
                                        <img data-original="{{home}}/uploads/shop/brands/samsung.png" src="{{ tpl_url }}/img/blank.gif" title="Samsung" alt="Samsung" class="lazy">
                                      </span>
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </div>
                            <div class="group-button-carousel">
                              <button type="button" class="prev arrow">
                                <span class="icon_arrow_p">
                                </span>
                              </button>
                              <button type="button" class="next arrow">
                                <span class="icon_arrow_n">
                                </span>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!--Start. Load menu in footer-->
                <div class="box-1">
                  <div class="inside-padd">
                    <div class="frame-seo-text">
                      <div class="text seo-text">
                        <h1>
                          Интернет-магазин электронной техники
                        </h1>
                        <p>
                          Интернет-магазин – это интерактивный сайт с каталогом, в котором представляются товары и услуги, а также корзиной для формирования заказа. В правильном интернет-магазине обязательно должны присутствовать : рекламируемые товары и услуги, контактная информация, предложения различных вариантов оплаты, предоставление счета. Работа интернет-магазина похожа на работу простого магазина. Клиент, просматривая каталог, помещает интересующую его позицию в корзину.
                        </p>
                        <p>
                          Интернет-магазин – это интерактивный сайт с каталогом, в котором представляются товары и услуги, а также корзиной для формирования заказа. В правильном интернет-магазине обязательно должны присутствовать : рекламируемые товары и услуги, контактная информация, предложения различных вариантов оплаты, предоставление счета. Работа интернет-магазина похожа на работу простого магазина. Клиент, просматривая каталог, помещает интересующую его позицию в корзину.
                        </p>
                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            {% endif %}
            <div class="footer-footer">
              <div class="container">
                <div class="inside-padd t-a_j">
                
                <!--
                <div>
                    <div class="footer-social">
                        <div class="footer-title">Мы в соцсетях</div>
                        <div class="f-s_0">
                                                        <a rel="nofollow" href="#" class="icon-vk-link"></a>
                                                                                    <a rel="nofollow" href="#" class="icon-fb-link"></a>
                                                                                    <a rel="nofollow" href="#" class="icon-tw-link"></a>
                                                    </div>
                    </div>
                    <div class="footer-payment">
                        <div class="footer-title">Мы принимаем</div>
                        <div class="f-s_0">
                            <span class="icon-visa"></span>
                            <span class="icon-mastercard"></span>
                        </div>
                    </div>
                </div>
                -->
                
                  <div class="frame-type-company">
                    <div class="c_b f-w_b">
                      © YourStore 2015
                    </div>
                    <div class="c_9">
                      Все права защищены.
                    </div>
                    <div class="engine c_9">
                    </div>
                  </div>
                
                  
                  <div class="footer-menu-stat">
                    <ul class="nav nav-vertical">
                      
                      <li>
                        <a href="{{home}}/o-magazine" target="_self" title="О компании">
                          О компании
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}/dostavka" target="_self" title="Доставка">
                          Доставка
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}/oplata" target="_self" title="Оплата">
                          Оплата
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}/novosti" target="_self" title="Новости">
                          Новости
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}/shop/brand" target="_self" title="Бренды">
                          Бренды
                        </a>
                      </li>
                      
                      <li>
                        <a href="{{home}}/kontakty" target="_self" title="Контакты">
                          Контакты
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  <div class="footer-category-menu">
                    <ul class="nav nav-vertical">
                      <li>
                        <a href="{{home}}/shop/category/telefoniia-pleery-gps" title="Телефония, МР3-плееры, GPS" class="title">
                          Телефония, МР3-плееры, GPS
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}/shop/category/domashnee-video" title="Домашнее видео" class="title">
                          Домашнее видео
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}/shop/category/detskie-tovary" title="Детские товары" class="title">
                          Детские товары
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}/shop/category/aktivnyi-otdyh-i-turizm" title="Активный отдых и туризм" class="title">
                          Активный отдых и туризм
                        </a>
                      </li>
                      <li>
                        <a href="{{home}}/shop/category/muzykalnye-instrumenty" title="Музыкальные инструменты" class="title">
                          Музыкальные инструменты
                        </a>
                      </li>
                      
                    </ul>
                  </div>
                  
                  <div class="items-contact-footer">
                    <ul class="items-contact">
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            0 (800) 567-43-21, 
                          </span>
                          <span class="text-el">
                            0 (800) 567-43-21
                          </span>
                        </span>
                      </li>
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            E-mail: partner@imagecms.net
                          </span>
                        </span>
                      </li>
                      <li>
                        <span class="f-s_0">
                          <span class="text-el">
                            Skype: imagecms
                          </span>
                        </span>
                      </li>
                    </ul>
                  </div>
                  <div class="work-footer">
                    <div>
                      Работаем: 
                      <span class="text-el">
                        Пн–Пт 09:00–20:00,
                        <br>
                        Сб 09:00–17:00, Вс выходной
                      </span>
                    </div>
                  </div>
                  <div class="footer-profile">
                    <ul class="nav nav-vertical">
                      <li>
                        <button type="button" data-trigger="#loginButton" title="Вход">
                          Вход
                        </button>
                      </li>
                      <li>
                        <button onclick="location = &#39;{{home}}/auth/register&#39;" title="Регистрация">
                          Регистрация
                        </button>
                      </li>
                      <li>
                        <button type="button" data-trigger="[data-drop=&#39;#ordercall&#39;]" title="Обратный звонок">
                          Обратный звонок
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              
              <!--
                <div class="frame-type-company">
                    <span class="f_r">
                        Магазин работает на 
                        <a href="http://www.imagecms.net/">ImageCMS<span class="icon-imagecms"></span></a>
                    </span>
                    <span>
                        © 2014-2015, Интернет-магазин Full Market. Все права защищены.
                    </span>
                </div>
              -->
              </div>
            </div>
          </footer>
          
          
          <script src="{{ tpl_url }}/js/united_scripts.js">
          </script>
          
  
          <script type="text/javascript">
            init();
            //initDownloadScripts(['united_scripts'], 'init', 'scriptDefer');
          </script>
          
          <button type="button" id="showCartPopup" data-drop="#popupCart" style="display: none;" class="isDrop">
          </button>
          <div class="drop-bask drop drop-style" id="popupCart">
          </div>
          
          <span class="tooltip">
          </span>
          <div class="apply">
            <div class="content-apply">
              <a href="{{home}}/#">
                Фильтр
              </a>
              <div class="description">
                Найдено 
                <span class="f-s_0">
                  <span id="apply-count">
                    5
                  </span>
                  <span class="text-el">
                    &nbsp;
                  </span>
                  <span class="plurProd">
                  </span>
                </span>
              </div>
            </div>
            <button type="button" class="icon_times_drop icon_times_apply">
            </button>
          </div>
          <div class="drop drop-style" id="notification">
            <div class="drop-content-notification">
              <div class="inside-padd notification">
                
              </div>
            </div>
            <div class="drop-footer">
            </div>
          </div>
          <button style="display: none;" type="button" data-drop="#notification" data-modal="true" data-effect-on="fadeIn" data-effect-off="fadeOut" class="trigger isDrop">
          </button>
          
          <div class="drop drop-style" id="confirm">
            <div class="drop-header">
              <div class="title">
                Подтвердите
              </div>
            </div>
            <div class="drop-content-confirm">
              <div class="inside-padd cofirm w-s_n-w">
                <div class="btn-def">
                  <button type="button" data-button-confirm="" data-modal="true">
                    <span class="text-el">
                      Подтвердить
                    </span>
                  </button>
                </div>
                <div class="btn-cancel">
                  <button type="button" data-closed="closed-js">
                    <span class="text-el d_l_1">
                      Отменить
                    </span>
                  </button>
                </div>
              </div>
            </div>
            <div class="drop-footer">
            </div>
          </div>
          <button style="display: none;" type="button" data-drop="#confirm" data-confirm="true" data-effect-on="fadeIn" data-effect-off="fadeOut" class="isDrop">
          </button>
          
          <div class="drop drop-style" id="dropAuth">
            <button type="button" class="icon_times_drop" data-closed="closed-js">
            </button>
            <div class="drop-content t-a_c" style="width: 350px;min-height: 0;">
              <div class="inside-padd">
                Для того, что бы добавить товар в список желаний, Вам нужно 
                <button type="button" class="d_l_1 isDrop" data-drop=".drop-enter" data-source="{{home}}/auth">
                  авторизоваться
                </button>
              </div>
            </div>
          </div>
          
          {{ plugin_ebasket_notify }}

          <script>
            (function($){
              var methods = {
                init: function(options){
                  var settings = $.extend({
                    pasteAfter: $(this),wrapper: $('body'),pasteWhat: $('[data-rel="whoCloneAddPaste"]'),evPaste: 'click',effectIn: 'fadeIn',effectOff: 'fadeOut',wherePasteAdd: this,whatPasteAdd: '<input type="hidden">',duration: 300,before: function(el){
                      return;
                    },after: function(el, elInserted){
                      return;
                    }}
                                          , options);
                  var $this = $(this),wrapper = settings.wrapper,pasteAfter = settings.pasteAfter,pasteWhat = settings.pasteWhat,evPaste = settings.evPaste,effectIn = settings.effectIn,effectOff = settings.effectOff,duration = settings.duration,wherePasteAdd = settings.wherePasteAdd,whatPasteAdd = settings.whatPasteAdd,before = settings.before,after = settings.after;
                  pasteAfter = pasteAfter.split('.');
                  $this.unbind(evPaste).bind(evPaste, function(){
                    var $this = $(this);
                    pasteAfter2 = $this;
                    $.each(pasteAfter, function(i, v){
                      pasteAfter2 = pasteAfter2[v]();
                    });
                    var insertedEl = pasteAfter2.next(),pasteAfterEL = pasteAfter2;
                    before($this);
                    if (!pasteAfterEL.hasClass('already')){
                      pasteAfterEL.after(wrapper.find(pasteWhat).clone().hide().find(wherePasteAdd).prepend(whatPasteAdd).end()).addClass('already');
                      $(document).trigger({'type': 'comments.beforeshowformreply','el': pasteAfterEL.next()}
                                         );
                      pasteAfterEL.next()[effectIn](duration, function(){
                        $(document).trigger({'type': 'comments.showformreply','el': $(this)}
                                           );
                      });
                      after($this, pasteAfterEL.next());
                    }else if (insertedEl.is(':visible')){
                      $(document).trigger({'type': 'comments.beforehideformreply','el': insertedEl}
                                         );
                      insertedEl[effectOff](duration, function(){
                        $(document).trigger({'type': 'comments.hideformreply','el': $(this)}
                                           );
                      });
                    }else if (!insertedEl.is(':visible')){
                      $(document).trigger({'type': 'comments.beforeshowformreply','el': insertedEl}
                                         );
                      insertedEl[effectIn](duration, function(){
                        $(document).trigger({'type': 'comments.showformreply','el': $(this)}
                                           );
                      });
                    }}
                                            );
                }}
                  ;$.fn.cloneAddPaste = function(method){
                    if (methods[method]){
                      return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
                    } else if (typeof method === 'object' || !method){
                      return methods.init.apply(this, arguments);
                    } else {
                      $.error('Method ' + method + ' does not exist on jQuery.cloneaddpaste');
                    }}
                    ;}
            )(jQuery);
            (function($){
              var methods = {
                init: function(options){
                  var settings = $.extend({
                    width: 0,afterClick: function(){
                      return true;
                    }}
                                          , options);
                  var width = settings.width;
                  this.each(function(){
                    var $this = $(this);
                    if (!$this.hasClass('disabled')){
                      $this.hover(function(){
                        $(this).append("<span></span>");
                      },function(){
                        $(this).find("span").remove();
                      });
                      var rating;
                      $this.mousemove(function(e){
                        if (!e){
                          e = window.event;
                        }if (e.pageX){
                          x = e.pageX;
                        } else if (e.clientX){
                          x = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft)- document.documentElement.clientLeft;
                        }var posLeft = 0;
                        var obj = this;
                        while (obj.offsetParent){
                          posLeft += obj.offsetLeft;
                          obj = obj.offsetParent;
                        }var offsetX = x - posLeft,modOffsetX = 5 * offsetX % this.offsetWidth;
                        rating = parseInt(5 * offsetX / this.offsetWidth);
                        if (modOffsetX > 0){
                          rating += 1;
                        }jQuery(this).find("span").eq(0).css("width", rating * width);
                      });
                      $this.click(function(){
                        settings.afterClick($this, rating);
                        return false;
                      });
                    }}
                           );
                }}
                  ;$.fn.starRating = function(method){
                    if (methods[method]){
                      return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
                    } else if (typeof method === 'object' || !method){
                      return methods.init.apply(this, arguments);
                    } else {
                      $.error('Method ' + method + ' does not exist on jQuery.starRating');
                    }}
                    ;}
            )(jQuery);
            var Comments = {
              toComment: function(el, drop){
                $('html, body').scrollTop(drop.offset().top - 20);
                drop.find(':input:first').focus();
              },initComments: function(wrapper){
                $(".star-big").starRating({
                  width: 17,afterClick: function(el, value){
                    if (el.hasClass("clicktemprate")){
                      $('.productRate > div.for_comment').css("width", value * 20 + '%');
                      $('.ratec').attr('value', value);
                    }}
                });
                $('[data-rel="cloneAddPaste"]').cloneAddPaste({
                  wrapper: wrapper,pasteAfter: 'parent.parent',pasteWhat: $('[data-rel="whoCloneAddPaste"]'),evPaste: 'click',effectIn: 'slideDown',effectOff: 'slideUp',duration: 300,wherePasteAdd: 'form',whatPasteAdd: '',before: function(el){
                    el.parent().toggleClass('active');
                  },after: function(el, elInserted){
                    $(elInserted).find('input[name=comment_parent]').val(el.data('parid'));
                    $('#comments form').submit(function(){
                      return false;
                    });
                  }}
                                                             );
                $('.comments form').submit(function(e){
                  e.preventDefault();
                });
                $('.usefullyes').bind('click', function(){
                  var comid = $(this).attr('data-comid');
                  $.ajax({
                    type: "POST",data: "comid=" + comid,dataType: "json",url: '/comments/commentsapi/setyes',success: function(obj){
                      if (obj !== null){
                        $('.yesholder' + comid).each(function(){
                          $(this).html("(" + obj.y_count + ")");
                        });
                      }}
                  });
                });
                $('.usefullno').bind('click', function(){
                  var comid = $(this).attr('data-comid');
                  $.ajax({
                    type: "POST",data: "comid=" + comid,dataType: "json",url: '/comments/commentsapi/setno',success: function(obj){
                      if (obj !== null){
                        $('.noholder' + comid).each(function(){
                          $(this).html("(" + obj.n_count + ")");
                        });
                      }}
                  });
                });
              },renderPosts: function(el, data, visible){
                var dataSend = "";
                if (data != undefined){
                  dataSend = data;
                }el = $(el);
                $.ajax({
                  url: locale + "/comments/commentsapi/renderPosts",dataType: "json",data: dataSend,type: "post",success: function(obj){
                    el.each(function(){
                      $(this).empty();
                    });
                    if (obj !== null){
                      var tpl = obj.comments;
                      var elL = el.length;
                      el.each(function(i, n){
                        $(this).append(tpl);
                        if (i + 1 == elL){
                          Comments.initComments($(this));
                        }}
                             );
                      if (parseInt(obj.commentsCount)!= 0){
                        $('#cc').html('');
                        $('#cc').html(parseInt(obj.commentsCount)+ ' ' + pluralStr(parseInt(obj.commentsCount), text.plurComments));
                      }if (visible && _useModeration){
                        visible = isNaN(visible)? $(visible): $('[data-place="' + visible + '"]');
                        visible.empty().append($('#useModeration').html());
                        setTimeout(function(){
                          el.find('.usemoderation').hide();
                        }, 3000);
                      }}
                    $(document).trigger({'type': 'rendercomment.after','el': el}
                                       );
                  }}
                      );
              },post: function(el, data, place){
                $.ajax({
                  url: "/comments/commentsapi/newPost",data: $(el).closest('form').serialize()+'&action=newPost',dataType: "json",beforeSend: function(){
                    $(el).closest('.forComments').append('<div class="preloader"></div>');
                  },type: "post",complete: function(){
                    $(el).closest('.forComments').find(preloader).remove();
                  },success: function(obj){
                    var form = $(el).closest('form');
                    if (obj.answer === 'sucesfull'){
                      $('.comment_text').each(function(){
                        $(this).val('');
                      });
                      $('.comment_plus').val('');
                      $('.comment_minus').val('');
                      Comments.renderPosts($(el).closest('.forComments'), data, place ? place : +form.find('[name="comment_parent"]').val());
                    }else {
                      form.find('.error_text').remove();
                      form.prepend('<div class="error_text">' + message.error(obj.validation_errors)+ '</div>');
                      drawIcons(form.find('.error_text').find(selIcons));
                      $(el).closest('.patch-product-view').removeAttr('style').css('max-height', 'none');
                    }}
                });
              }}
                ;$(document).on('scriptDefer', function(){
                  Comments.initComments();
                });
          </script>
          <div id="fancybox-loading">
            <div>
            </div>
          </div>
          <div id="fancybox-loading">
            <div>
            </div>
          </div>
  </body>
</html>
[/TWIG]
