<h1><?=$title?></h1>
<article class='article1'>
<?php foreach ($tags as $tag) : ?>
<p><span class="tags">
<a href="tags/id/<?=htmlentities($tag->id)?>"><i class="fa fa-tag fa-fw" style="color:white"></i><?=htmlentities($tag->name)?></a>
</span> - <?=htmlentities($tag->text)?></p>
<?php endforeach; ?>
</article>