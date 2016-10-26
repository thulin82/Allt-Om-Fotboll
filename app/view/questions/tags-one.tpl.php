<h1><?=$title?></h1>
<article class='article1'>
    <p><b>ID: </b><?=htmlentities($tag->id)?></p>
    <p><b>Name: </b><?=htmlentities($tag->name)?></p>
    <p><b>Description: </b><?=htmlentities($tag->text)?></p>
    <b>Releated Questions: </b>
    <?php foreach ($tag->related_questions as $value) : ?>
    	<?php foreach ($value as $one => $two) : ?>
    <p><a href='<?=$this->url->create('questions/view/' . "$one")?>'><?=htmlentities($two)?></a></p>
    <?php endforeach; ?>
    <?php endforeach; ?>
    <hr>
    <p><a href='<?=$this->url->create('tags/edit/' . "$tag->id")?>'>Edit tag</a></p>
    <p><a href='<?=$this->url->create('tags/')?>'>Back to tags</a></p>
</article>