<div class="page-header">
    <div class="page-header">
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="#">OAuth認証サンプル</a>
            <!-- <?= $this->tag->linkto(['#', 'OAuth認証サンプル', 'local' => true, 'navbar-brand', 'title' => 'show']) ?> -->
        </nav>
    </div>
    <h1>Route Error</h1>
    <h3>指定されたURLがありません</h3>
    <br>
    <!-- ログインしていたならばログイン後の画面、していなければログイン前の画面へ -->
    <?php if ($session_access_token) { ?>
        <?= $this->tag->linkto(['session', 'TOPへ', 'local' => true, 'class' => 'btn btn-lg', 'title' => 'error2top']) ?>
    <?php } else { ?>
        <?= $this->tag->linkto(['session/show', 'TOPへ', 'local' => true, 'class' => 'btn btn-lg', 'title' => 'error2show']) ?>
    <?php } ?>
</div>