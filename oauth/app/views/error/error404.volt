<div class="page-header">
    <div class="page-header">
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">OAuth認証サンプル</a>
            <!-- {{ linkTo(["#", "OAuth認証サンプル", "local":true, "navbar-brand", "title":"show"]) }} -->
        </nav>
    </div>
    <h1>Route Error</h1>
    <h3>指定されたURLがありません</h3>
    <br>
    <!-- ログインしていたならばログイン後の画面、していなければログイン前の画面へ -->
    {% if session_access_token %}
        {{ linkTo(["session", "TOPへ", "local":true, "class":"btn btn-lg", "title":"error2top"]) }}
    {% else %}
        {{ linkTo(["session/show", "TOPへ", "local":true, "class":"btn btn-lg", "title":"error2show"]) }}
    {% endif %}
</div>