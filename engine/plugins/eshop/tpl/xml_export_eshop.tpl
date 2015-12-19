<?xml version="1.0" encoding="windows-1251"?>
<yml_catalog date="{{ 'now'|date("Y-m-d H:i") }}">
    <shop>
        <name>{{ system_flags.info.title.header }}</name>
        <url>{{ home }}</url>
        <currencies>
            <currency id="{{ system_flags.eshop_currency[0].code }}" rate="{{ system_flags.eshop_currency[0].rate_from }}" />
        </currencies>
        <categories>
            {% for cat in cat_info %}
            <category id="{{ cat.id }}"{% if not (cat.parent_id == 0) %} parentId="{{ cat.parent_id }}"{% endif %}>{{ cat.name }}</category>
            {% endfor %}
        </categories>
        <offers>
            {% for entry in entries %}
            <offer id="{{ entry.id }}" available="{% if (entry.stock == 0) or (entry.stock == 1) %}false{% elseif (entry.stock == 5) %}true{% endif %}">
                <url>{{ home }}{{ entry.fulllink }}</url>
                <price>{{ entry.price }}</price>
                <baseprice>{{ entry.compare_price }}</baseprice>
                <currencyId>{{ system_flags.eshop_currency[0].code }}</currencyId>
                <categoryId>{{ entry.cid }}</categoryId>
                {% if (entry.images) %}
                    {% for img in entry.images %}
                    {% if (loop.index < 10) %}<picture>{{home}}/uploads/eshop/products/thumb/{{img.filepath}}</picture>{% endif %}
                    {% endfor %}
                {% endif %}
                <name>{{ entry.name }}</name>
                <description>{{ entry.annotation }}</description>
                {% if (entry.features) %}
                    {% for feature in entry.features %}
                    <param name="{{feature.name}}">{{feature.value}}</param>
                    {% endfor %}
                {% endif %}
            </offer>
            {% endfor %}
        </offers>
    </shop>
</yml_catalog>
