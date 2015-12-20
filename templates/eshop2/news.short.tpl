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
                    <li class="btn-crumb">
                        <button typeof="v:Breadcrumb" disabled="disabled">
                        <span class="text-el">{{ news.categories.list[0].name }}</span>
                        </button>
                    </li>
                    {% endif %}
                </ul>
            </div>
        </div>
    </div>
    
    <div class="frame-inside page-text">
        <div class="container">
            <div class="text-right">
                <h1>Новости</h1>
                <ul class="items items-text-category">
                    <li>
                        <a href="{{ news.url.full }}" class="frame-photo-title ">
                            <span class="d_b">
                                <span class="date f-s_0 d_b">
                                    <span class="icon_time"></span><span class="text-el"></span>
                                    <span class="day">{{ news.dateStamp|date("d") }} </span>
                                    <span class="month">{{ news.dateStamp|date("M") }} </span>
                                    <span class="year">{{ news.dateStamp|date("Y") }} </span>
                                </span>
                                <span class="title">{{ news.title }}</span>
                            </span>
                        </a>
                        <div class="description">
                            <p>
                            {{ news.short }}
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>
[/TWIG]
