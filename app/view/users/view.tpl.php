<h1><?= $title ?></h1>
<article class='article1'>
<?php
if ($users->deleted != null) {
    $style = "color:red";
    $title = "Soft-deleted User";
} elseif ($users->active == null) {
    $style = "color:grey";
    $title = "Inactive User";
} else {
    $style = "color:green";
    $title = "Active User";
}
$id = htmlentities($users->id);
$acronym = htmlentities($users->acronym);
$name = htmlentities($users->name);

$table = <<<EOD
        <table>
        <thead>
            <tr>
                <th>id</th>
                <th>akronym</th>
                <th>namn</th>
                <th>status</th>
                <th>gravatar</th>
            </tr>
        </thead><tbody>
            <tr>
                <td>$id</td>
                <td>$acronym</td>
                <td>$name</td>
                <td><i class="fa fa-user fa-fw" style=$style title=$title></i></td>
                <td>$users->gravatar</td>
            </tr>
EOD;
        
        $table .= "</tbody></table>";
?>
<?= $table ?>
 
<p><a href='<?=$this->url->create('users')?>'>Tillbaka</a></p>
</article>