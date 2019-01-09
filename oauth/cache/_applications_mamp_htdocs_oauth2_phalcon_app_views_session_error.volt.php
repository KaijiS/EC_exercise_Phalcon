<div class="page-header">
    <h1>Session Error</h1>
    <h3>ログインされていません</h3>

    <?= $this->tag->linkto(['session', 'TOPへ', 'local' => true, 'class' => 'btn', 'title' => 'error2top']) ?>
    <?= $this->tag->linkto(['session/login', 'ログインする', 'local' => true, 'class' => 'btn', 'title' => 'error2login']) ?>
</div>