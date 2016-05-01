<div class="ui container" id="helpPanel" style="margin-top:1em;">
    <div class="ui stackable grid">
        <div class="twelve wide column category_links">
            {% for ch_cat in cat_info.children %}
                    <a href="{{ ch_cat.cat_link }}" class="link">{{ ch_cat.name }}</a>
            {% endfor %}
        </div>
        <div class="four wide column" style="text-align:right">
            <form method="post" id="catalogForm">
                <select class="dropdown" id="sort" name="order" onchange="this.form.submit()">
                    <option value="date_desc" {% if (filter.order == 'date_desc') %}selected{% endif %}>�� ����</option>
                    <option value="name_asc" {% if (filter.order == 'name_asc') %}selected{% endif %}>�� �������� (�-�)</option>
                    <option value="price_asc" {% if (filter.order == 'price_asc') %}selected{% endif %}>�� ������� � �������</option>
                    <option value="price_desc" {% if (filter.order == 'price_desc') %}selected{% endif %}>�� ������� � �������</option>
                    <option value="stock_desc" {% if (filter.order == 'stock_desc') %}selected{% endif %}>�� �������</option>
                    <option value="likes_desc" {% if (filter.order == 'likes_desc') %}selected{% endif %}>�� ������������</option>
                </select>
            </form>      
        </div>
    </div>
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
</div>

<div class="ui stackable four column grid container" >
{% if (info) %}
    <div class="feed-me">
    {{info}}
    </div>
{% endif %}

{% for entry in entries %}
    <div class="column ">
        <div class="product-preview">
            <div class="buttons">
                    <a class="buy" href="{{entry.fulllink}}">������</a>
                    <!--<a class="prev" href="/buy">������� ��������</a>-->
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
