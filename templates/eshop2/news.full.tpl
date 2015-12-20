[TWIG]
<div class="content">
    <div class="frame-crumbs">
        <div class="crumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
            <div class="container">
                <ul class="items items-crumbs">
                    <li class="btn-crumb">
                        <a href="{{ home }}" typeof="v:Breadcrumb">
                            <span class="text-el">Главная</span>
                            <span class="divider">/</span>
                        </a>
                    </li>
                    {% if (news.categories.count > 0) %}
                        {% for cat in news.categories.list %}
                        <li class="btn-crumb">
                            <a href="{{ cat.url }}" typeof="v:Breadcrumb">
                                <span class="text-el">{{ cat.name }}</span>
                                <span class="divider">/</span>
                            </a>
                        </li>
                        {% endfor %}
                    {% endif %}

                    <li class="btn-crumb">
                        <button typeof="v:Breadcrumb" disabled="disabled">
                        <span class="text-el">{{ news.title }}</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="frame-inside page-text">
        <div class="container">
            <div class="text-right">
            <h1>{{ news.title }}</h1>
                <div class="text">
                    <p>{{ news.short }}{{ news.full }}</p>
                    {% if (news.flags.hasPagination) %}
                        <div class="pagination">
                            <ul>
                                {{ news.pagination }}
                            </ul>
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>
[/TWIG]
