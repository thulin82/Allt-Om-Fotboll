<h1><?=$this->textFilter->doFilter($title, 'shortcode')?></h1>
<h2><?=$this->textFilter->doFilter($question->text, 'shortcode')?></h2>
<div class='article1'>
    <table class='widthwhite'>
        <tr>
            <td class='twentyfive'></td>
            <td class='twentyfive'><?=htmlentities($question->points)?>
                <a href="../../questions/upVote/<?=htmlentities($question->id)?>"><i class="fa fa-thumbs-up fa-fw" style="color:green"></i></a>
                <a href="../../questions/downVote/<?=htmlentities($question->id)?>"><i class="fa fa-thumbs-down fa-fw" style="color:red"></i></a>
            </td>
            <td class='fifty right'>asked by <?=htmlentities($question->userAcronym)?> at <?=htmlentities($question->created)?></td>
        </tr>
    </table>

    <?php foreach ($question->comments as $qc) : ?>

            <div class="comment">
                <?=$this->textFilter->doFilter($qc->content, 'shortcode')?>
                <div class="right">
                by <?=htmlentities($qc->userAcronym)?> at <?=htmlentities($qc->created)?>
                </div>    
            </div>

    <?php endforeach; ?>

    <p><a href='<?=$this->url->create('comments/commentQuestion/' . $question->id)?>'>Add a comment</a></p>
</div>
<article class='article1'>
<h3><?=count($answers)?> answers:</h3>
<?php foreach ($answers as $a) : ?>
    <div class='answer'>  
        <?=$this->textFilter->doFilter($a->text, 'shortcode')?>
        <table class='widthgrey'>
            <tr>
                <td class='twentyfive'></td>
                <td class='twentyfive'><?=htmlentities($a->points)?>
                    <a href="../../answers/upVote/<?=htmlentities($a->id)?>/<?=htmlentities($question->id)?>"><i class="fa fa-thumbs-up fa-fw" style="color:green"></i></a>
                    <a href="../../answers/downVote/<?=htmlentities($a->id)?>/<?=htmlentities($question->id)?>"><i class="fa fa-thumbs-down fa-fw" style="color:red"></i></a>
                </td>
                <td class='fifty right'>by <?=htmlentities($a->userAcronym)?> at <?=htmlentities($a->created)?></td>
            </tr>
        </table>
    </div>

    <?php foreach ($a->comments as $ac) : ?>

            <div class="comment">
                <?=$this->textFilter->doFilter($ac->content, 'shortcode')?>
                <div class="right">
                    by <?=htmlentities($ac->userAcronym)?> at <?=htmlentities($ac->created)?>
                </div>
            </div>

    <?php endforeach; ?>

    <p><a href='<?=$this->url->create('comments/commentAnswer/' . $a->id . '/' . $question->id)?>'>Add a comment</a></p>
<?php endforeach; ?>

<?php foreach ($question->tags as $tag) : ?>
<span class="tags">
<a href="../../tags/id/<?=htmlentities($tag->id)?>"><i class="fa fa-tag fa-fw" style="white"></i><?=htmlentities($tag->name)?></a>
</span>
<?php endforeach; ?>
<p><a href='<?=$this->url->create('questions/answer/' . $question->id)?>'>Add an answer</a></p>
<p><a href='<?=$this->url->create('questions/')?>'>Questions</a></p>
</article>