<?php

/* @var $this yii\web\View */

$this->title = 'Calendar';
?>

<div class="body-content">

    <div class="row">
        <div class="col-md-3">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>События</th>
                </tr>
                <?php foreach ($calendarList as $k => $calendar) { ?>
                <tr>
                    <td><?= ++$k ?></td>
                    <td><?= $calendar ?></td>
                </tr>
                <?php } ?>
            </table> 
        </div>
        <div class="col-md-9">
            <pre>
                <?php print_r($listEvents); ?>
            </pre>
        </div>

    </div>

</div>
