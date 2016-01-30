{% macro recursiveCategory(category, level, cnt) %}
    {% if (level == 0) %}<div class="two wide column"><h4><a href="{{ category.cat_link }}">{{ category.name }} {{ cnt.count[category.id] }}</a></h4>{% elseif (level == 1) %}<a href="{{ category.cat_link }}">{{ category.name }} {{ cnt.count[category.id] }}</a>{% endif %}
        {% if category.children|length %}
            {% set level = level + 1 %}
                {% for child in category.children %}
                    {{ _self.recursiveCategory(child, level, cnt) }}
                {% endfor %}
        {% endif %}

{% endmacro %}

{% if tree %}
<div class="submenu hide">
    {% for category in tree %}
        {% set level = 0 %}
        {{ _self.recursiveCategory(category, level, cnt) }}
        </div>
    {% endfor %}
</div>
{% endif %}