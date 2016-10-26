<article class="article1">
 
<?=$content?>
 
<?php if(isset($bylineInfo)) : ?>
<footer class="byline">
<figure class='photo'> 
    <img src='<?=$this->url->asset("img/me/me-small.jpg")?>' alt='logo'/>
    <figcaption><?=$bylineName?></figcaption>
</figure>
<?=$bylineInfo?>
</footer>
<?php endif; ?>
 
</article>