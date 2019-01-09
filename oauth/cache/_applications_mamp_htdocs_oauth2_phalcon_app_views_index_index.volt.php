<div class="page-header">
    <h1>Congratulations!</h1>
</div>

<p>You're now flying with Phalcon. Great things are about to happen!</p>

<p>This page is located at <code>views/index/index.phtml</code></p>


<?= $this->tag->linkto(['session/login', 'githubでログイン', 'local' => false, 'class' => 'btn-primary', 'title' => 'login']) ?>
<!-- 
    そのほかのオプション
    "target":"_blank"  新しいタブで開く
 -->
