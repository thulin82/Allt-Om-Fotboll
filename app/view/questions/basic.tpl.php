<h1><?=$title?></h1>
<article class='article1'>
<table class="questions_overview">
<tr>
    <th>Fråga</th>
    <th>Ställd av</th>
    <th>Skapad</th>
    <th>Antal svar</th>
    <th>Antal taggar</th>
    <th>Antal Poäng</th>
</tr>
<?php foreach ($questions as $q) : ?>
<tr>
    <td><a href='<?=$this->url->create('questions/view/' . $q->id)?>'><?=htmlentities($q->topic)?></a></td>
    <td><?=htmlentities($q->userAcronym)?></td>
    <td><?=htmlentities($q->created)?></td>
    <td><?=htmlentities($q->numAnswers)?></td>
    <td><?=htmlentities($q->numTags)?></td>
    <td><?=htmlentities($q->points)?></td>
</tr>

<?php endforeach; ?>
</table>

<p><a href='<?=$this->url->create('questions/ask')?>'>Ask a question</a></p>
<p><a href='<?=$this->url->create('')?>'>Home</a></p>
</article>