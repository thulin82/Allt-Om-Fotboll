<h1><?=$title?></h1>
<article class='article1'>
<?php foreach ($users as $u) : ?>
<p><a href="users/id/<?=htmlentities($u->id)?>"><i class="fa fa-user fa-fw"></i><?=htmlentities($u->acronym)?></a></p>
<?php endforeach; ?>
</article>