[TWIG]
<!DOCTYPE html>
<html xml:lang="{{ lang['langcode'] }}" lang="{{ lang['langcode'] }}">
<head>
    <meta http-equiv="content-type" content="text/html; charset={{ lang['encoding'] }}" />
    <meta http-equiv="content-language" content="{{ lang['langcode'] }}" />
    <meta name="generator" content="{{ what }} {{ version }}" />
    <meta name="document-state" content="dynamic" />
    {{ htmlvars }}

    {% if pluginIsActive('rss_export') %}
    <link href="{{ home }}/rss.xml" rel="alternate" type="application/rss+xml" title="RSS" />
    {% endif %}

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ tpl_url }}/css/reset.css">
    <link rel="stylesheet" href="{{ tpl_url }}/css/main.css">
    <link rel="stylesheet" href="{{ tpl_url }}/semantic/dist/semantic.min.css">


    <script src="{{ tpl_url }}/js/jquery-1.8.3.min.js"></script>
    <script src="{{ tpl_url }}/semantic/dist/semantic.js"></script>
    <script src="{{ tpl_url }}/js/main.js"></script>

    <script type="text/javascript" src="{{ scriptLibrary }}/functions.js"></script>
    <script type="text/javascript" src="{{ scriptLibrary }}/ajax.js"></script>
    
</head> 
<body>
    
<div id="loading-layer">
  <img src="{{ tpl_url }}/img/loading.gif" alt="" />
</div>
    
<!-- Header -->
<header>
     
    <div class="ui stackable grid container" style="padding-top:1.8em">
        <div class="row">
            <div class="four wide column">
                <img src="{{ tpl_url }}/img/logo.png" alt="">
            </div>
            <div class="seven wide column right floated" style="text-align:right">
                 <span id="searchShow">Поиск<i class="icon search inline-block"></i></span>
                 <div class="ui right aligned category search" style="display:none">
                  <div class="ui icon input">
                    <input class="prompt" type="text" placeholder="Поиск по сайту..." style="border-radius:0">
                    <i class="icon search"></i>
                  </div>
                  <i class="icon remove" style="color:#000;font-size:1.1em;margin-left:0.2em"></i>
                  <div class="results"></div>
                </div>
            </div>
        </div>
    </div>  
    <nav class="ui container">
        <ul id="mainMenu">
            <li>
                <a class="dropdownItem" href="#">Категории <i class="angle down icon"></i></a>
                    <div class="submenu hide">
                        <div class="two wide column">
                            <h4>Кроссовки</h4>
                            <a href="/">Nike</a>
                            <a href="/">New Balance</a>
                            <a href="/">Addidas</a>
                            <a href="/">Asics</a>
                        </div>
                        <div class="two wide column">
                            <h4>Кеды</h4>
                            <a href="/">Converse</a>
                            <a href="/">Lacoste</a>
                            <a href="/">Vans</a>
                        </div>
                        <div class="two wide column">
                            <h4>Ботинки</h4>
                            <a href="/">Timberland</a>       
                        </div>
                        <div class="three wide column">
                            <h4>Другое</h4>
                            <a href="/">Распродажа</a>
                            <a href="/">Новые поступления</a>
                            <a href="/">Предложение сезона</a>
                            <a href="/">Показать все</a>
                        </div>
                    </div>                                           
            </li>
            <li><a href="#" class="dropdownItem">Оплата и доставка <i class="angle down icon"></i></a>
                <div class="submenu hide">
                    <div class="two wide column">
                        <a href="/">Доставка по Москве</a>
                    </div>
                    <div class="two wide column">
                        <a href="/">Доставка по России</a>
                    </div>
                    <div class="three wide column">
                        <a href="/">Доставка в страны СНГ</a>
                    </div>       
                </div>  
            </li>
            <li><a href="#">Блог</a></li>
            <li><a href="#">Lookbook</a></li>
            <li><a href="#">Контакты</a></li>
            <li><a href="#" style="color:#EF9A00">Скидки</a></li>

        </ul>
        <div style="float:right;margin-top:-1.25em;text-align:center"> 
                <span style="font-weight:bold;font-size:1em">КОРЗИНА (0)</span> <i  style="margin-left:0.2em;font-size:1.2em" class="icon cart"></i>
        </div>
        <div class="dropdownMenu ui stackable grid" style="display:none"></div>
    </nav>
        
</header>
<!-- Stop header -->

<!-- Help Panel-->
<!--
<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <div class="twelve wide column category_links">
            <a href="/" class="link">Air Max 90</a>
            <a href="/" class="link">Air Max Zero</a>
            <a href="/" class="link">Air Force</a>
            <a href="/" class="link">Cortez</a>
            <a href="/" class="link">Cortez</a>
            <a href="/" class="link active">Roshe Run</a>
            <a href="/" class="link">Air Max 90</a>
            <a href="/" class="link">Air Max Zero</a>

        </div>      
        <div class="four wide column" style="text-align:right">
            <select class="dropdown" name="sort"  >
                <option value="1">Подешевле</option>
                <option value="2">Подороже</option>
                <option value="3">Новые</option>
                <option value="4">Старые</option>
            </select>       
        </div>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>
-->
<!-- Stop help panel-->

<!-- Products -->
<div class="ui stackable four column grid container" id="mainProductsPreview">
    {% if not isHandler('news:main') %}
    {{ mainblock }}
    {% endif %}
</div>
<!-- End Products-->
<div class="ui container">
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
    <div class="ui pagination menu floated right shadow-none radius-none" id="mainPagesPreview">
    </div>
</div>

<footer style="background:#fafafa;margin-top:8em; padding:2em">
    <div class="ui stackable three column grid container">
        <div class="column">
            <h3 class="ui header">МАГАЗИН</h3>
            <a href="">Бренды</a>
            <a href="">Каталог</a>
            <a href="">Распродажа</a>
            
        </div>
        <div class="column">
            <h3 class="ui header">ИНФОРМАЦИЯ</h3>
            <a href="/">Оплата и доставка</a>
            <a href="/">Как определить размер</a>
            <a href="/">Узнать состояние заказа</a>
            <a href="/">Блог</a>
            <a href="/">Lookbook</a>
        </div>
        <div class="column">
            <h3 class="ui header">МЫ</h3>
            <p>
                © {{ now|date("Y") }} "Сниксток" 
                <a href=""><i class="icon mail" style="color:#555"></i>info@sneakstok.ru</a>
                <div class="socials bordered">
                    <a href="https://vk.com/sneakstok"><i class="icon vk"></i></a>
                    <a hrref=""><i class="icon instagram"></i></a>
                </div>
            </p>
        </div>
    </div>  
</footer>


<script>
$(document).ready(function() {

    rpcEshopRequest('eshop_amain', {'action': 'show', 'number':8, 'mode':'last', 'page':0 }, function (resTX) {
        if ((resTX['data']['prd_main']>0)&&(resTX['data']['prd_main'] < 100)) {
            $("div#mainProductsPreview").html(""+resTX['data']['prd_main_text']+"");
            $("div#mainPagesPreview").html(""+resTX['data']['prd_main_pages_text']+"");
        } else {
            $("div#mainProductsPreview").html(""+resTX['data']['prd_main_text']+"");
            $("div#mainPagesPreview").html(""+resTX['data']['prd_main_pages_text']+"");
        }
    });
    
});    

</script>
</body>
</html>
[/TWIG]
