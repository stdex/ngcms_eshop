{% macro recursiveCategory(category, level) %}
    <li {% if (level == 0) %}class="column_0"{% elseif (level == 1) %}class="column2_0"{% endif %}>
        <a {% if (level == 0) %}class="title-category-l1 is-sub"{% elseif (level == 1) %}{% endif %} href="{{ category.cat_link }}">{% if (level == 0) %}<span class="helper"></span> <span class="text-el">{% elseif (level == 1) %}{% endif %}{{ category.name }}{% if (level == 0) %}</span>{% elseif (level == 1) %}{% endif %}</a>
        {% if category.children|length %}
            <div class="frame-l2">
                <ul class="items">
                {% for child in category.children %}
                    {% set level = level + 1 %}
                    {{ _self.recursiveCategory(child, level) }}
                {% endfor %}
                </ul>
            </div>
        {% endif %}
    </li>
{% endmacro %}

{% if tree %}
<ul class="items">
    {% for category in tree %}
        {% set level = 0 %}
        {{ _self.recursiveCategory(category, level) }}
    {% endfor %}
</ul>
{% endif %}
