<?= $this->tag->stylesheetLink('css/show.css') ?>

<div class="page-header">
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="show">OAuth認証サンプル</a>
        <!-- <?= $this->tag->linkto(['session/show', 'OAuth認証サンプル', 'local' => true, 'navbar-brand', 'title' => 'show']) ?> -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
            <!-- <span class="navbar-toggler-icon"></span> -->
            <?= $this->tag->image([$user['avatar_url'], 'alt' => 'nav_user_avatar', 'class' => 'nav-user-avatar']) ?>
            <?= $user['login'] ?> さん
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <!-- <a class="nav-link" href="#">ログアウト</a> -->
                <?= $this->tag->linkto(['session/logout', 'ログアウト', 'local' => true, 'class' => 'nav-link', 'title' => 'logout']) ?>
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
            <div class="col-4">
                <?= $this->tag->image([$user['avatar_url'], 'alt' => 'user_avatar', 'class' => 'user-avatar']) ?><br>
                <?= $user['login'] ?><br>
                <?= $user['bio'] ?><br>
            </div>
            <div class="col-8 bg-light">
                    コンテンツ
            </div>
        </div>
    </div>

</body>