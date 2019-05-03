<?php
// Необходимы:
// $urlAction
// $yearStart
// $monthStart
// $yearEnd
// $monthEnd
// $eventFilterId

$monthArr = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

?>
<form  class="event-filter" action="<?= $urlAction ?>" method="GET">
    <input type="hidden" name="id" value="<?= $eventFilterId ?>"/>
    <div>
        <div class="note">От: </div>
        <input type="number" class="form-control" name="yearStart" step="1" value="<?= $yearStart ?>"/>
        <select class="form-control" size="1" name="monthStart"/>
        <?php foreach($monthArr as $num => $month) {
            $selected = '';
            if ($monthStart == $num + 1) {
                $selected = 'selected';
            }
        ?>
            <option <?= $selected ?> value="<?= $num + 1 ?>"><?= $month ?></option>
        <?php } ?>
        </select>
    </div>
    <div>
        <button type="submit" class="btn btn-success">Показать</button>
    </div>
    <div style="width: 100%"></div>
    <div>
        <div class="note">До: </div>
        <input type="number" class="form-control" name="yearEnd" step="1" value="<?= $yearEnd ?>"/>
        <select class="form-control" size="1" name="monthEnd"/>
        <?php foreach($monthArr as $num => $month) {
            $selected = '';
            if ($monthEnd == $num + 1) {
                $selected = 'selected';
            }
        ?>
            <option <?= $selected ?> value="<?= $num + 1 ?>"><?= $month ?></option>
        <?php } ?>
        </select>
    </div>
    <div>
        <button type="submit" value="reset" class="btn btn-warning">Сброс</button>
    </div>
</form>
<script>
    $('.event-filter .btn').click(function(event) {
        event.preventDefault();

        var yearSart = $('.event-filter [name="yearStart"]');
        var yearEnd = $('.event-filter [name="yearEnd"]');
        var monthStart = $('.event-filter [name="monthStart"]');
        var monthEnd = $('.event-filter [name="monthEnd"]');

        if ($(this).val() === 'reset') {
            yearSart.val('');
            yearEnd.val('');
            monthStart.val('');
            monthEnd.val('');
        }

        if (yearSart.val() + monthStart.val() > yearEnd.val() + monthEnd.val()) {
            alert('"От" не может быть старше "До"');
            return;
        }
        $('form.event-filter').submit();
    })
</script>
    