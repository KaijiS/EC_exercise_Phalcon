{{ stylesheet_link('css/show.css') }}

<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <!-- <a class="navbar-brand" href="./../">OAuth認証サンプル</a> -->
        {{ linkTo(["session/", "OAuth認証サンプル", "class":"btn btn-default btn-lg", "local":true, "navbar-brand", "title":"show"]) }}
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
            <!-- <span class="navbar-toggler-icon"></span> -->
            {% if jwt %}
                {{ image(user['avatar_url'], "alt": "nav_user_avatar", "class":"nav-user-avatar") }}
                {{ user['login'] }} さん
            {% else %}
                ログイン
            {% endif %}
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                {% if jwt %}
                    <!-- <a class="nav-link" href="#">ログアウト</a> -->
                    {{ linkTo(["session/logout", "ログアウト", "local":true, "class":"nav-link", "title":"logout"]) }}
                {% else %}
                    {{ linkTo(["session/login", "GitHubでログイン", "local":ture, "class":"nav-link","title":"login"]) }}
                {% endif %}
            </li>
            </ul>
        </div>
    </nav>
</div>

<body>

    <div>
        <br>
    </div>


    <div class="container-fluid">
        <div class="row">
            <div class="col-4" style="word-wrap:break-word;">
                {% if jwt %}
                    {{ image(user['avatar_url'], "alt": "user_avatar", "class":"user-avatar") }}<br>
                    {{ user['login'] }}<br>
                    {{ user['bio'] }}<br><br>
                    jwt : <br>
                    {{ jwt }}<br>
                {% else %}
                    Guest
                {% endif %}
            </div>
            <div class="col-8 bg-light">
                <h3>{{ item['name'] }}</h3>
                <h4>価格 : {{ item['price'] }} 円</h4><br>
                {{ item['description'] }}<br><br>
                {% if item['mime'] and item['raw_data']%}
                    {{ image("data:"~item['mime']~";base64,"~item['raw_data'], "alt": "item_img", "class":"item_img") }}<br>
                {% endif %}
                <br>
                {% if jwt %}
                    <div class="right">
                        {{ linkTo(["session/edit/"~item['id'], " 編 集 ", "local":true, "class":"btn btn-success", "title":"index"]) }}
                    </div><br>
                    <div class="right">
                        {{ linkTo(["session/delete/"~item['id'], "この商品を削除", "local":true, "class":"btn btn-danger btn-sm", "title":"index"]) }}
                    </div><br>
                {% endif %}
            </div>
        </div>
    </div>

</body>