{% if (entries) %}

<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <h3 class="ui header" style="margin-top:10px;">Товары по акции</h3>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>

<div class="ui stackable four column grid container" >

{% for entry in entries %}
    <div class="column ">
        <div class="product-preview">
            <div class="buttons">
                    <a class="buy" href="{{entry.fulllink}}">Купить</a>
                    <!--<a class="prev" href="/buy">Быстрый просмотр</a>-->
            </div>
            {% if (entry.images[0].filepath) %}<a class="image" style="background:url({{home}}/uploads/eshop/products/{{entry.id}}/thumb/{{entry.images[0].filepath}}) 50% 50% /cover #eee"></a>{% else %}<a class="image" style="background:url({{home}}/engine/plugins/eshop/tpl/img/img_none.jpg) 50% 50% /cover #eee"></a>{% endif %}            
            <a class="name">{{ entry.name }} </a>
            <div class="prices">
                {% if (entry.variants[0].price) %}
                    <span class="current">{{ (entry.variants[0].price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span>
                {% endif %}
                {% if (not (entry.variants[0].compare_price == '0.00')) and (not (entry.variants[0].compare_price == '')) %}
                    <span class="old">{{ (entry.variants[0].compare_price * system_flags.eshop.currency[0].rate_from / system_flags.eshop.current_currency.rate_from)|number_format(2, '.', '') }} {{ system_flags.eshop.current_currency.sign }}</span>
                {% endif %}
            </div>
        </div>
    </div>
{% endfor %}
</div>

{% if (pages.true) %}
<div class="ui container">
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
    <div class="ui pagination menu floated right shadow-none radius-none">
        {% if (prevlink.true) %}
            {{ prevlink.link }}
        {% endif %}
        
        {{ pages.print }}
        
        {% if (nextlink.true) %}
            {{ nextlink.link }}
        {% endif %}
        
    </div>
</div>
{% endif %}

{% else %}

<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <span><b>Акционных товаров не найдено</b></span>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>

{% endif %}

