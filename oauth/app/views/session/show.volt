{{ stylesheet_link('css/show.css') }}

<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="show">OAuth認証サンプル</a>
        <!-- {{ linkTo(["session/show", "OAuth認証サンプル", "local":true, "navbar-brand", "title":"show"]) }} -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
            <!-- <span class="navbar-toggler-icon"></span> -->
            {{ image(user['avatar_url'], "alt": "nav_user_avatar", "class":"nav-user-avatar") }}
            {{ user['login'] }} さん
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <!-- <a class="nav-link" href="#">ログアウト</a> -->
                {{ linkTo(["session/logout", "ログアウト", "local":true, "class":"nav-link", "title":"logout"]) }}
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
                {{ image(user['avatar_url'], "alt": "user_avatar", "class":"user-avatar") }}<br>
                {{ user['login'] }}<br>
                {{ user['bio'] }}<br><br>
                jwt : <br>
                {{ jwt }}<br>

            </div>
            <div class="col-8 bg-light">
                <table class="table">
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
                            <th>{{ item['name'] }}</th>
                            <td>{{ item['description'] }}</td>
                            <td>{{item['price']}}</td>
                        </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>