<?php
$data = getRocc($_GET['id']);
//print_r($data);
?>
<input value="<?php echo $_GET['id'];?>" id="reportid" readonly hidden>
<div class="" role="alert" id="rocc_message">
    
<!--<button type="button" class="btn btn-primary btn-sm">Архивировать</button>
<button type="button" class="btn btn-secondary btn-sm">PDF отчет</button>-->
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
<div class="btn-group">
  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Действие
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" onclick="editRocc('<?php echo $_GET['id'];?>','start')" href="#">Отменить</a>
    <a class="dropdown-item" href="#">Удалить</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" onclick="generatePdf('reportpdf','<?php echo $_GET['id'];?>')" href="#">Скачать</a>
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
      <th scope="col">Аудитор</th>
      <th scope="col">Статус</th>
    </tr>
  </thead>
  <tbody id="roccinfo"></tbody>
</table>

<div id="countreport">
    
</div>
             <table class="table table-striped">
  <center><thead>
    <tr>
      <th scope="col">  </th>
      <th scope="col"><center>Фото</center></th>
      <th scope="col"><center>Короткая информация</center></th>
      <th scope="col">  </th>
    </tr>
  </thead>
  <tbody id="result">
      <?php
            
            foreach($data['otkl'] as $item){  
        ?>
    <tr>
      <td><center><img src="<?php echo $item['otkl_img_cat'];?>" height="35px" width="35px"></center></td>
      <td><center>
      	<?php
      	if($item['otkl_img']==null){}else{
      		?>
      		<img src="/uploads/<?php echo $item['otkl_img'];?>" height="200px" width="200px">
      		<?php
      	}
      	?>
      </center></td>
      <td><center><?php echo $item['otkl_mintext'];?></center></td>
      <td><center><img src="<?php echo $item['otkl_img_subcat'];?>" height="35px" width="35px"></center></td>
    </tr>

    <?php
            }
        ?>
  </tbody>
</div>
</div>