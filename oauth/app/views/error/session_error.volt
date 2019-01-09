<div class="page-header">
        <div class="page-header">
            <nav class="navbar navbar-dark bg-dark">
                <a class="navbar-brand" href="#">OAuth認証サンプル</a>
                <!-- {{ linkTo(["#", "OAuth認証サンプル", "local":true, "navbar-brand", "title":"show"]) }} -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
                    <!-- <span class="navbar-toggler-icon"></span> -->
                    ログイン
                </button>
        
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <!-- <a class="nav-link" href="#">ログアウト</a> -->
                        {{ linkTo(["session/login", "GitHubでログイン", "local":ture, "class":"nav-link","title":"login"]) }}
                    </li>
                    </ul>
                </div>
            </nav>
        </div>
        <h1>Session Error</h1>
        <h3>ログインされていません</h3>
        <br>
    
        {{ linkTo(["session", "TOPへ", "local":true, "class":"btn btn-lg", "title":"error2top"]) }}
    </div>