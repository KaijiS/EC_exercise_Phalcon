{{ stylesheet_link('css/add.css') }}

<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <!-- <a class="navbar-brand" href="./../">OAuth認証サンプル</a> -->
        {{ linkTo(["session/", "OAuth認証サンプル", "class":"btn btn-default btn-lg", "local":true, "navbar-brand", "title":"show"]) }}
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
                {{ form('session/edit/'~item_info["id"], 'enctype': "multipart/form-data",'class':'form-post') }}
                    {% for message in error_message %}
                        {{ message }}
                    {% endfor %}
                    <!-- 商品名入力エリア -->
                    <div class="form-group">
                        <label>商品名</label>
                        <!-- <input type="text" name="name" class="form-control" maxlength="100" placeholder="商品名" required> -->
                        {{ text_field("name", "class":"form-control", "maxlength":100, "placeholder":"商品名", "value":item_info["name"], "required") }}
                    </div>
                    <!-- 説明文入力エリア -->
                    <div class="form-group">
                        <label>説明文</label>
                        <!-- <textarea name="description" class="form-control" rows="3" maxlength="500" placeholder="説明文" required></textarea> -->
                        {{ text_area("description", "class":"form-control", "row":4, "maxlength":500, "placeholder":"説明文", "value":item_info["description"], "required") }}
                    </div>
                    <!-- 価格入力エリア -->
                    <div class="form-group">
                        <label>価格</label>
                        <!-- <input type="number" name="price" class="form-control" min=0 required> -->
                        {{numeric_field("price", "class":"form-control","min":0, "value":item_info["price"], "required")}}
                    </div>
                    <!-- 画像があったら今の画像を表示 -->
                    {% if item_info['mime'] and item_info['raw_data']%}
                        {{ image("data:"~item_info['mime']~";base64,"~item_info['raw_data'], "alt": "item_img", "class":"item_img") }}<br>
                        画像を変更する
                    {% endif %}
                    <!-- ファイルアップロード -->
                    <div class="form-group">
                        <label>商品画像</label>
                        {{file_field("img")}}
                    </div>
                    <!-- 送信ボタン -->
                    {{ submit_button("編集", "class":"btn btn-primary") }}
                {{ end_form() }}
            </div>
        </div>
    </div>

</body>