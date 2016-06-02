<div class="content">
    <div class="frame-inside page-text">
        <div class="container">
            <div class="text-right">
            <h1>{{ lang.uprofile['profile_of'] }} {{ user.name }} {% if (user.flags.isOwnProfile) %}[ <a href="/profile.html">редактировать</a> ]{% endif %}</h1>
                <div class="text">
                    <div class="block-user-info">
                        <div class="user-info">
                            <table class="table" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td><img src="{{ user.photo_thumb }}" alt=""/></td>
                                    <td class="second"><img src="{{ user.avatar }}" alt=""/></td>
                                </tr>
                                <tr>
                                    <td>Пользователь:</td>
                                    <td class="second">{{ user.name }} [id: {{ user.id }}]</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['status'] }}:</td>
                                    <td class="second">{{ user.status }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['regdate'] }}:</td>
                                    <td class="second">{{ user.reg }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['last'] }}:</td>
                                    <td class="second">{{ user.last }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['from'] }}:</td>
                                    <td class="second">{{ user.from }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['about'] }}:</td>
                                    <td class="second">{{ user.info }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['icq'] }}:</td>
                                    <td class="second">{{ user.icq }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['site'] }}:</td>
                                    <td class="second">{{ user.site }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['all_news'] }}:</td>
                                    <td class="second">{{ user.news }}</td>
                                </tr>
                                <tr>
                                    <td>{{ lang.uprofile['all_comments'] }}:</td>
                                    <td class="second">{{ user.com }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>


