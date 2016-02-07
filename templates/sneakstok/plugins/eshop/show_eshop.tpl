<!-- Product -->
<div id="product">
    <div class="ui grid stackable container">
        <div class="nine wide column">
            {% if (entriesImg) %}
            <div id="sliderProduct" style="dislay: block;width:100%; height:540px; border:1px solid #eee">
            {% for entry in entriesImg %}
                <div data-slidr="{{ loop.index }}" style="text-align:center">
                    <img src="{{home}}/uploads/eshop/products/{{entry.filepath}}" title="{{ name }}" alt="">
                </div>
            {% endfor %}
            </div>
            {% endif %}
            
            <!-- 
                <div class="ui breadcrumb">
                  <a class="section">Главная</a>
                  <div class="divider"> / </div>
                  <a class="section">Nike</a>
                  <div class="divider"> / </div>
                  <a class="section" href="/">Roshe Run</a>
                  <div class="divider"> / </div>
                  <div class="section">Roshe Run (grey/yellow)</div>
                </div>
            -->
        </div>
        <div class="seven wide column" style="height:600px;">
            <h3 class="ui header">{{ name }}</h3>
            <div style="color:#999; font-size:0.9em">Артикул: {{ code }}</div>
            <div class="ui medium list params radius-none">
                {{ annotation }}
                {{ body }}
                <br/>
                {% for feature in entriesFeatures %}
                    <div class="item">
                        {{ feature.name }}:<span>{{ feature.value }}</span>
                    </div>
                {% endfor %}
            </div>
            <!--
            <div class="ui form" style="margin-top:2em;width:55%">
                <div class="field">
                    <label>Выберите размер</label>
                    <select class="dropdown" name="" id="">
                        <option>-- Размер (Euro) --</option>
                        <option value="36">36</option>
                        <option value="37">37</option>
                    </select>
                </div>
            </div>
            -->
            <div class="prices">
                {% if (not (entriesVariants[0].compare_price == '0.00')) and (not (entriesVariants[0].compare_price == '')) %}
                    <span class="old_price">{{ (entriesVariants[0].compare_price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }})
                    </span>
                {% endif %}
                {% if (entriesVariants[0]) %}
                <span class="current_price">{{ (entriesVariants[0].price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}
                </span>
                {% endif %}
                <div class="ui large button buy radius-none orderBut" data-id="{{ id }}" style="margin-left:1em">КУПИТЬ </div>
            </div>      

        </div>
    </div>
    <!--
    <div class="ui mobile reversed two column grid container" style="margin-top:2em">
        <div class="column" style="text-align:left">
            <span class="ui tiny button radius-none"> <i class="icon arrow left"></i>Предыдущий</span>
        </div>
        <div class="column" style="text-align:right">
            <span class="ui tiny button radius-none">Следующий <i class="icon arrow right"></i></span>
        </div>
    </div>
    -->
</div>
<!-- End Product -->
{% if (entriesRelated) %}
<div class="ui grey horizontal divider container" style="margin:1em 0 "><h3 class="header">ПОХОЖИЕ ТОВАРЫ</h3></div>
<div class="ui stackable four column grid container" id="productsPreview">
    {% for entry in entriesRelated %}
    <div class="column ">
        <div class="product-preview">
            <div class="buttons">
                    <a class="buy" href="{{entry.fulllink}}">Купить</a>
                    <!--<a class="prev" href="/buy">Быстрый просмотр</a>-->
            </div>
            <a class="image" style="background:url({% if (entry.images[0].filepath) %}{{home}}/uploads/eshop/products/thumb/{{entry.images[0].filepath}}{% else %}{{home}}/engine/plugins/eshop/tpl/img/img_none.jpg{% endif %}) 50% 50% /cover #eee"></a>
            <a class="name">{{ entry.name }}</a>
            <div class="prices">
                {% if (entry.variants[0].price) %}<span class="old">{{ (entry.variants[0].price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span>{% endif %}
                {% if (not (entry.variants[0].compare_price == '0.00')) and (not (entry.variants[0].compare_price == '')) %}<span class="current">{{ (entry.variants[0].compare_price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span>{% endif %}
            </div>
        </div>
    </div>
    {% endfor %}
</div>
{% endif %}
<script>
$(document).ready(function() {
    var slider = slidr.create('sliderProduct',{
        breadcrumbs: false,
        controls: 'border',
        direction: 'h',
        keyboard: true,
        overflow: false,
        pause: false,
        touch: true,
        transition: 'linear'
    });
    var slides = [];
    $('#sliderProduct').find('div').each(function(){
        slides[slides.length] = $(this).data('slidr');
    });
    slides[slides.length] = slides[0];

    slider.add("h",slides,"linear").start();
});
</script>

<script>
$(document).ready(function() {

    $(".orderBut").click(function(e){
        var id = $(this).attr('data-id');
        var count = $("input[name='quantity']").attr('value');
        if( count == undefined) {
            count = 1;
        }

        rpcEshopRequest('eshop_ebasket_manage', {'action': 'add', 'ds':1,'id':id,'count':count }, function (resTX) {
            document.getElementById('tinyBask').innerHTML = resTX['update'];
            $('.message-add-to-cart').removeClass('hidden');
            $('.message-add-to-cart').addClass('visible');
            setTimeout(function(){$('.message-add-to-cart').removeClass('visible');$('.message-add-to-cart').addClass('hidden');},2000);
            e.preventDefault();
        });

    });
    
    br.storage.prependUnique('page_stack', {{ id }}, 25);

});
</script>