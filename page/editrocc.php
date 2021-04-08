<?php
if(isset($_GET['id'])){
    reReportid($_GET['id']);
    $reportid = $_GET['id'];
}else{
    $reportid = $_COOKIE[r_reportid];
}
$restid = 1;
?>

<input value="<?php echo $reportid;?>" id="reportid" readonly hidden>
<input  id="resultrocc" readonly hidden>

<div class="" role="alert" id="roccedit_message">
    
<!--<button type="button" class="btn btn-primary btn-sm">Архивировать</button>
<button type="button" class="btn btn-secondary btn-sm">PDF отчет</button>-->
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
<div class="btn-group">
  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Действие
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" onclick="clearOtkl(<?php echo $reportid; ?>)" href="#">Очистить все</a>
    <a class="dropdown-item" onclick="deleteRocc(<?php echo $reportid; ?>)" href="#">Удалить аудит</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" id="finalbtn" href="#">Завершить</a>
  </div>
</div>
<div class="btn-group">
  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Добавить отклонение
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" onclick="actionA('one')" href="#">Едичная загрузка</a>
   
    
    <div class="dropdown-divider"></div>
 <a class="dropdown-item" onclick="actionA('all')" href="#">Массовая загрузка</a>
  </div>
</div>
<p></p>
 <div class="table-responsive">
<div class="alert alert-primary" role="alert" id="uploadall">
    <?php
    session_start();
    $session_id = '1'; /* Создание сессии */
?>
<div id='preview'></div>
<form id="imageform" method="post" enctype="multipart/form-data" action='ajaxImageUpload.php' style="clear:both">
    Массовая загрузка: 
    <div id='imageloadstatus' style='display:none'><img src="loader.gif" alt="Загрузка ...."/></div>
    <div id='imageloadbutton'>
        <input type="file" name="photos[]" id="photoimg" multiple="true" />
        <input type="text" name="restid" id="restid" value="<?php echo $restid;?>" hidden>
        <input type="text" name="reportid" id="reportid" value="<?php echo $reportid;?>" hidden>
    </div>
</form>
</div>


               <table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">ID report</th>
      <th scope="col">Ресторан</th>
      <th scope="col">Дата</th>
      <th scope="col">Аудитор</th>
      <th scope="col">Статус</th>
    </tr>
  </thead>
  <tbody id="roccinfo"></tbody>
</table>

<div class ="row">
    
    <div class="col-7" id="countreport"></div>
    <div class="col-5"></div>
</div>


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
  <tbody id="otklresulta">
  </tbody>
</table>
</div>
</div>


<!-- Модальное окно // Загрузка фото -->
<div class="modal fade" id="uploadonemodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Единичная загрузка: </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="addedotkl">
         <div class="form-group">
    <label for="mintext">Краткое описание</label> 
    <input id="mintext" name="mintext" type="text" class="form-control" required="required">
  </div>
  <div class="form-group">
    <label for="fulltext">Полное описание</label> 
    <textarea id="fulltext" name="fulltext" cols="40" rows="5" class="form-control" required="required"></textarea>
  </div>
  <div class="form-group">
    <label for="category">Категория отклонения</label> 
    <div>
      <select id="category" name="category" class="custom-select">
        <option value="rabbit">Rabbit</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="subcategory">Подкатегория</label> 
    <div>
      <select id="subcategory" name="subcategory" class="custom-select">
        <option value="rabbit">Rabbit</option>
        <option value="duck">Duck</option>
        <option value="fish">Fish</option>
      </select>
    </div>
  </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary">Сохранить изменения</button>
      </div>
    </div>
  </div>
</div>
<!-- Модальное окно // Загрузка фото -->

<!-- Модальное окно // Edit фото -->
<div class="modal fade" id="editImgModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирвание изображения</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"  >
          <div id="roccimgedit">
              
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div><!-- Модальное окно // Edit фото -->

<!-- Модальное окно // Edit comment -->
<div class="modal fade" id="editTextModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирвание текста отклонения</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"  >
           <div class="form-group">
    <label for="mintext">Краткий текст</label>
    <input id="editotklid" name="editotklid" type="text" class="form-control" required="required" hidden>
    <input id="editmintext" name="editmintext" type="text" class="form-control" required="required">
  </div>
  <div class="form-group">
    <label for="fulltext">Полный текст</label> 
    <textarea id="editfulltext" name="editfulltext" cols="40" rows="5" class="form-control"></textarea>
  </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" id="save-edit-otkl" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div><!-- Модальное окно // Edit фото -->


<!-- Модальное окно // Edit comment -->
<div class="modal fade" id="editLevelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Редактирвание отклонения</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"  >
    <input id="editotklid-cat" name="editotklid-cat" type="text" class="form-control" required="required" hidden>
            <div class="form-group">
    <label for="edit-category">Категория отклонения</label> 
    <div>
      <select id="editcategory" name="edit-category" class="custom-select"></select>
    </div>
  </div>
  <div class="form-group">
    <label for="subcat-edit">Подкатегория</label> 
    <div>
      <select id="editsubcat" name="editsubcat" class="custom-select"></select>
    </div>
  </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" id="save-edit-otkl-catlevel" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div><!-- Модальное окно // Edit фото -->