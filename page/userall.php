<div>
<button type="button" id="addrest" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staticBackdrop" >+Пользователь</button>
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
</div>
<p></p>

<table class="table table-striped" id="datab">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Ресторан</th>
      <th scope="col">Email</th>
      <th scope="col">Директор</th>
      <th scope="col">Адрес</th>
    </tr>
  </thead>
  <tbody id="resultrest">
  </tbody>
</table>
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
    <label for="restname">Название ресторана</label> 
    <input id="restname" name="restname" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="restid">ID ресторана</label> 
    <input id="restid" name="restid" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="restemail">Email</label> 
    <input id="restemail" name="restemail" type="text" class="form-control">
  </div>
  <div class="form-group">
    <label for="dirname">Директор</label> 
    <div>
      <select id="dirname" name="dirname" class="custom-select">
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="adress">Адрес ресторана</label> 
    <input id="adress" name="adress" type="text" class="form-control">
  </div> 
 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" id="addrestbtn" class="btn btn-primary">Добавить</button>
      </div>
    </div>
  </div>
</div>