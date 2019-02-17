{{ stylesheet_link('css/index.css') }}

<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <!-- <a class="navbar-brand" href="./">OAuth認証サンプル</a> -->
        {{ linkTo(["session", "OAuth認証サンプル", "class":"btn btn-default btn-lg", "local":true, "navbar-brand", "title":"index"]) }}

        <!-- <form class="form-inline my-2 my-lg-0"> -->
        {{ form('session/search', "method":"get", "class":"form-inline my-2 my-lg-0") }}
            <input type="search" name="key" class="form-control mr-sm-2" placeholder="検索..." aria-label="検索...">
            <button type="submit" class="btn btn-info my-2 my-sm-0">検索</button>
        <!-- </form> -->
        {{ end_form() }}

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
            <!-- アクティブ状態で表示したい時 -->
            <!-- <li class="nav-item active">
                <a class="nav-link" href="#">ホーム <span class="sr-only">(現位置)</span></a>
            </li> -->
            <li class="nav-item">
                {% if jwt %}
                    <!-- <a class="nav-link" href="#">ログアウト</a> -->
                    {{ linkTo(["session/logout", "ログアウト", "local":true, "class":"nav-link", "title":"logout"]) }}
                {% else %}
                    {{ linkTo(["session/login", "GitHubでログイン", "local":ture, "class":"nav-link","title":"login"]) }}
                {% endif %}
                <!-- 
                    ちなみにメモ：
                    そのほかのオプション
                    "target":"_blank"  新しいタブで開く
                -->
            </li>
            <!-- 無効にしたいとき -->
            <!-- <li class="nav-item">
                <a class="nav-link disabled" href="#">無効</a>
            </li> -->
            <!-- ドロップダウン使いたい時 -->
            <!-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ドロップダウン</a>
                <div class="dropdown-menu" aria-labelledby="dropdown">
                <a class="dropdown-item" href="#">リンク1</a>
                <a class="dropdown-item" href="#">リンク2</a>
                <a class="dropdown-item" href="#">リンク3</a>
                </div>
            </li> -->
            </ul>
            <!-- 検索欄 -->
            <!-- <form class="form-inline my-2 my-md-0">
            <input class="form-control" type="search" placeholder="検索..." aria-label="検索...">
            </form> -->
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
            <div class="col-8">
                <!-- 追加ボタン -->
                {% if jwt %}
                    <div class="right">
                        {{ linkTo(["session/add", "商品を追加", "local":true, "class":"btn btn-primary", "title":"index"]) }}
                    </div><br>
                {% endif %}

                <!-- 検索結果表示 -->
                {% if data|length == 0 %}
                    <b>"{{ key }}"</b>を含む商品名はありませんでした。

                {% else %}
                    "<b>{{ key }}</b>"の検索結果  <b>{{ data|length }}</b>件
                    <table class="table table-hover bg-light">
                        <thead>
                            <tr>
                                <th>商品名</th>
                                <th>説明</th>
                                <th>価格</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for item in data %}
                            <tr>
                                <td>{{ linkTo(["session/show/"~item['id'], item['name'], "local":true, "class":"btn btn-default", "title":"show"]) }}</td>
                                <td>{{ item['description'] }}</td>
                                <td>{{ item['price' ]}}</td>
                            </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    
                {% endif %}
            </div>
        </div>
    </div>

</body>