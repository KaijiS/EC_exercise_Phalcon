<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="session">OAuth認証サンプル</a>
        <!-- <?= $this->tag->linkto(['session', 'OAuth認証サンプル', 'local' => true, 'navbar-brand', 'title' => 'index']) ?> -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
            <!-- <span class="navbar-toggler-icon"></span> -->
            ログイン
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mr-auto">
            <!-- アクティブ状態で表示したい時 -->
            <!-- <li class="nav-item active">
                <a class="nav-link" href="#">ホーム <span class="sr-only">(現位置)</span></a>
            </li> -->
            <li class="nav-item">
                <!-- <a class="nav-link" href="#">ログアウト</a> -->
                <?= $this->tag->linkto(['session/login', 'GitHubでログイン', 'local' => $ture, 'class' => 'nav-link', 'title' => 'login']) ?>
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

