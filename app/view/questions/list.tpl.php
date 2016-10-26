<h1><?=$title?></h1>

<table class="questions_overview">
<?php foreach ($questions as $q) : ?>

<tr>
    <td><?=$q->numAnswers?> answers</td>
<td>
    <a href='<?=$this->url->create('questions/view/' . $q->id)?>'>
        <?=$q->topic?>
    </a>
</td>
    <td><?=$q->userAcronym?></td>
    <td><?=$q->created?></td>
</tr>

<?php endforeach; ?>
</table>

<p><a href='<?=$this->url->create('questions/ask')?>'>Ask a question</a></p>
<p><a href='<?=$this->url->create('')?>'>Home</a></p>