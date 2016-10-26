<h1><?= $title ?></h1>
<article class='article1'>
<?php
$table = <<<EOD
        <table>
        <thead>
            <tr>
                <th>id</th>
                <th>image</th>
                <th>akronym</th>
                <th>namn</th>
                <th>status</th>
                <th>redigera</th>
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
            $gravatar = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($dt->email)));
            $id = htmlentities($dt->id);
            $acronym = htmlentities($dt->acronym);
            $name = htmlentities($dt->name);
            $table .=
            "<tr>
                <td><a href='users/id/$id'>$id</a></td>
                <td><img class='avatar' src='$gravatar'></td> 
                <td>$acronym</td>
                <td>$name</td>
                <td><i class='fa fa-user fa-fw' style=$style title='$title'></i></td>
                <td>
                    <a href='users/edit/$id' title='Editera'><i class='fa fa-pencil fa-fw' style='color:black'></i></a>
                </td>
            </tr>";

        }
        $table .= "</tbody></table>";
?>
<?=$table?>
</article>