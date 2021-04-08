<div>
<button type="button" id="addrest" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop" >+Пользователь</button>
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
</div>
<p></p>
<div class="table-responsive">
<?php
$data = getPlanned('1');

$events = array(
	'16.04'    => 'Заплатить ипотеку',
	'23.04' => 'День защитника Отечества',
	'08.04' => 'Международный женский день',
	'08.04' => 'Международный женский день f',
	'08.04' => 'Международный женский деньff',
	'30.04' => 'Новый год'
);

//echo Calendar::getMonth(date('n'), date('Y'), $events);
echo Calendar::getInterval(date('03.Y'), date('05.Y'), $events);

?>
</div>
