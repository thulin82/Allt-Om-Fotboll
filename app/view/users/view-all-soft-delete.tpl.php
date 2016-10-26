<h1><?= $title ?></h1>
<?php
$table = <<<EOD
        <table>
        <thead>
            <tr>
                <th>id</th>
                <th>akronym</th>
                <th>namn</th>
                <th>status</th>
                <th>undo</th>
            </tr>
        </thead><tbody>
EOD;
        foreach ($users as $dt) {
            if ($dt->deleted != null) {
                $style = "color:red";
                $title = "Soft-deleted User";
            } elseif ($dt->active == null) {
                $style = "color:grey";
                $title = "Inactive User";
            } else {
                $style = "color:green";
                $title = "Active User";
            }
            $id      = htmlentities($dt->id); 
            $acronym = htmlentities($dt->acronym);
            $name    = htmlentities($dt->name);

            $table .= <<<EOD
            <tr>
                <td>$id</td>
                <td>$acronym</td>
                <td>$name</td>
                <td><i class="fa fa-user fa-fw" style=$style title=$title></i></td>
                <td><a href="undosoftdelete/$dt->id"><i class="fa fa-undo fa-fw" style="color:red" title="Undo soft-delete"></i></a></td>
            </tr>
EOD;
        }
        $table .= "</tbody></table>";
?>
<?= $table ?>

<p><a href='<?=$this->url->create('users')?>'>Tillbaka</a></p>