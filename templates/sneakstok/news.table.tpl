{% for entry in data %}
{{ entry }}
{% else %}
<div class="alert alert-info">
    <strong>Информация</strong>
    {{ lang['msgi_no_news'] }}
</div>
{% endfor %}

<div class="ui container">
    <div class="ui divider" style="margin-top:1.5em;border-color:#eee;border-bottom:0"></div>
    <div class="ui pagination menu floated right shadow-none radius-none">
        {{ pagination }}
    </div>
</div>
