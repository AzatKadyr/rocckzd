<div>
<button type="button" id="addrest" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop" >+Пользователь</button>
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
</div>
<p></p>
<div class="table-responsive">
<table class="table table-striped" id="datab">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Ресторан</th>
      <th scope="col">Email</th>
      <th scope="col">Компания</th>
      <th scope="col">Телефон</th>
    </tr>
  </thead>
  <tbody id="resultuser">
  </tbody>
</table>
</div>
</div>

<!-- Модальное окно -->
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Добавление пользователя</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="" id="addrest_message" role="alert"></div>
          <div class="form-group">
    <label for="restname">Имя</label> 
    <input id="username" name="username" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="rest">Ресторан</label> 
    <div>
      <select id="userrest" name="userrest" class="custom-select">
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="restemail">Email</label> 
    <input id="useremail" name="useremail" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="dirname">Должность</label> 
    <div>
      <select id="uservac" name="uservac" class="custom-select">
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="adress">Пароль</label> 
    <input id="userpassword" name="userpassword" type="text" class="form-control">
  </div> 
 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" id="adduserbtn" class="btn btn-primary">Добавить</button>
      </div>
    </div>
  </div>
</div>