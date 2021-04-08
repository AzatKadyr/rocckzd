<?php
$datea = date("Y-m-d");
$dateb = date("Y-m-01");
?>
<div>

<p>
    <div class="card">
  <div class="card-body">
       
    <div >
Период: <input type="datetime" id="datea" value="<?php  echo $dateb;?> 00:00:00"> <input type="datetime" id="dateb" value="<?php global $datea; echo $datea;?> 23:59:59"> 
Ресторан: <select id="filterrest" class="">
    <option value="all">Все</option>
</select>
<button type="button" id="genrepbtn" class="btn btn-primary btn-sm">Сформировать отчет</button>
</div>
</p>
  </div>
</div>
</div>
<div class="table-responsive">
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
</div>