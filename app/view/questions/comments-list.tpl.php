<h1><?=$title?></h1>
<article class='article1'>
<?php foreach ($list as $q) : ?>
<p>
    <?=htmlentities($q->content)?>
</p>
<?php endforeach; ?>
</article>