<h1><?=$title?></h1>
<article class='article1'>
<?php foreach ($list as $q) : ?>
<p>
    <a href=<?=$this->url->create('questions/view/' . $q->questionid)?>><?=htmlentities($q->text)?></a>
</p>
<?php endforeach; ?>
</article>