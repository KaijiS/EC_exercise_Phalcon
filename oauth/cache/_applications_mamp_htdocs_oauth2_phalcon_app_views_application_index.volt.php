<div class="page-header">
    <h1>Congratulations!</h1>
</div>

<p>loginしました</p>

<?= $user['login'] ?> <br>
<?= $user['bio'] ?> <br>
<?= $this->tag->linkto(['session/logout', 'ログアウト', 'local' => false, 'class' => 'btn', 'title' => 'logout']) ?>