<input value="<?php echo $_GET['id'];?>" id="reportid" readonly hidden>
<input  id="acplanid" readonly hidden>
<div class="" role="alert" id="acplann_message">
    <button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
<div class="btn-group">
  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Действие
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" href="#">Отменить</a>
    <a class="dropdown-item" href="#">Удалить</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="#">Скачать</a>
  </div>
</div>
<p></p>
 <div class="table-responsive">
               <table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">ID report</th>
      <th scope="col">Ресторан</th>
      <th scope="col">Дата</th>
      <th scope="col">Пользователь</th>
      <th scope="col">Статус</th>
    </tr>
  </thead>
  <tbody id="acplaninfo"></tbody>
</table>
               <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Фото</th>
      <th scope="col">Короткая информация</th>
      <th scope="col">  </th>
      <th scope="col">  </th>
      <th scope="col">Действия</th>
    </tr>
  </thead>
  <tbody id="result">
  </tbody>
</table>
</div>
</div>