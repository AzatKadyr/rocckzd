<?php
$datea = date("Y-m-d");
$dateb = date("Y-m-01");
?>
<div>
<p><button type="button" id="startroccurl" class="btn btn-primary btn-sm">Новый аудит</button>
<button type="button" class="btn btn-secondary btn-sm" id="filterbtnn">Фильтр</button>
<!--Показать: <a href="#" id="filterall">Все</a> |
<a href="#" id="filterstart">Незвершенные</a> |
<a href="#" id="filterfinal">Завершенные</a> |
<a href="#" id="filtertel">Быстрые аудиты</a> |-->
<a href="#" data-toggle="modal" data-target="#generatetokentelegram">Токен</a> |
<a href="/index.php?menu=restall">Рестораны</a> |
<a href="/index.php?menu=userall">Пользователи</a> |
<a href="/index.php?menu=my">Мой профиль</a>
</p>
<p>
    <div class="card"  id="filter">
  <div class="card-body">
    <div >
Период: <input type="datetime" id="datea" value="<?php  echo $dateb;?> 00:00:00"> <input type="datetime" id="dateb" value="<?php global $datea; echo $datea;?> 23:59:59"> 
Показать: <select id="typed" class="">
    <option value="all">Все</option>
    <option value="start">Незавершенные</option>
    <option value="final">Завершенные</option>
    <option value="telegram">Быстрые аудиты</option>
</select>
Ресторан: <select id="filterrest" class="">
    <option value="all">Все</option>
</select>
<button type="button" id="filterbtn" class="btn btn-primary btn-sm">Применить</button>
</div>
</p>
  </div>
</div>
</div>
<table class="table table-striped table-bordered" id="datab">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Ресторан</th>
      <th scope="col">Дата</th>
      <th scope="col">Статус</th>
      <th scope="col">Аудитор</th>
      <th scope="col">Действия</th>
    </tr>
  </thead>
  <tbody id="resultreport">
  </tbody>
</table>
<div class="row" id="pagem">

</div>
</div>