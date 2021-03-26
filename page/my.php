<?php
$user = userInfo($_COOKIE['userid']);
$data = countProcent($user['user']['user_restid']);
//print_r($user);
?>
<div class="" role="alert" id="my_message">
    <div class="row">
    <div class="col-5">
        <img src="/uploads/img/<?php echo $user['user']['user_img']; ?>" height="320" width="240" class="img-fluid" alt="...">
        <p></p>
        <p><center><a id="photobtn" href="#">Загрузить фото</a></center></p>
        <div class="alert alert-primary" id="uploadphoto" role="alert">
   <p><form method="post" action="api.php" enctype="multipart/form-data">
      <input type="file" name="file">
      <input type="submit" value="Загрузить">
    </form></p>
</div>
       
    </div>
    <div class="col-7">
         <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Имя: </th>
      <th scope="col"><?php echo $user['user']['user_name']; ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Должность:</td>
      <td><?php echo $user['user']['user_vacname']; ?></td>
    </tr>
    <tr>
      <td>Компания</td>
      <td><?php echo $user['user']['user_companyname']; ?></td>
    </tr>
    <tr>
      <td>Ресторан</td>
      <td><?php echo $user['user']['user_restname']; ?></td>
    </tr>
    <tr>
      <td>Email</td>
      <td><?php echo $user['user']['user_email']; ?></td>
    </tr>
     <tr>
      <td>Телефон</td>
      <td><?php echo $user['user']['user_phone']; ?></td>
    </tr>
  </tbody>
</table>
    </div>
    </div>
 <?php
 if($user['user']['user_vac']=="2"){
 	?>
  <div class="card">
  <div class="card-body">
  	<p>Кол аудитов: <?php echo $data['obw'];?> | At standart: <?php echo $data['count_a'];?> | Marginal: <?php echo $data['count_m'];?> | Underperfoming: <?php echo $data['count_u'];?></p>
<div class="progress">
  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $data['percent_a'];?>%" aria-valuenow="<?php echo $data['percent_a'];?>" aria-valuemin="0" aria-valuemax="100"> <?php echo $data['percent_a'];?>%</div>
  <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $data['percent_m'];?>%" aria-valuenow="<?php echo $data['percent_m'];?>" aria-valuemin="0" aria-valuemax="100"><?php echo $data['percent_m'];?>%</div>
  <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $data['percent_u'];?>%" aria-valuenow="<?php echo $data['percent_u'];?>" aria-valuemin="0" aria-valuemax="100"><?php echo $data['percent_u'];?>%</div>
</div>
</div>
</div>
 	<?php
 }
 ?>
    
<div class="container">
              <div class="row">
                <div class="col-xs-12 ">
                  <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                      <a class="nav-item nav-link" onclick="getUserinfo('<?php echo $_COOKIE['userid'];?>');" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Аудиты ресторана</a>
                      <a class="nav-item nav-link" onclick="getUserinfo('<?php echo $_COOKIE['userid'];?>');" id="nav-audt-tab" data-toggle="tab" href="#nav-audt" role="tab" aria-controls="nav-audt" aria-selected="true">Проведенные аудиты</a>
                      <a class="nav-item nav-link" onclick="getUserinfo('<?php echo $_COOKIE['userid'];?>');" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Рестораны</a>
                      <a class="nav-item nav-link" onclick="getUserinfo('<?php echo $_COOKIE['userid'];?>');" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Телеграм</a>
                      <a class="nav-item nav-link" onclick="getUserinfo('<?php echo $_COOKIE['userid'];?>');" id="nav-about-tab" data-toggle="tab" href="#nav-about" role="tab" aria-controls="nav-about" aria-selected="false">История авторизаций</a>
                    </div>
                  </nav>
                  <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                      <table class="table table-striped table-bordered" id="datab">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Ресторан</th>
      <th scope="col">Дата</th>
      <th scope="col">Статус</th>
      <th scope="col">Аудитор</th>
      <th scope="col">Результат </th>
    </tr>
  </thead>
  <tbody id="restauditsall">
  </tbody>
</table>
                      </div>
                    <div class="tab-pane fade" id="nav-audt" role="tabpanel" aria-labelledby="nav-audt-tab">
                     <table class="table table-striped table-bordered" id="datab">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Ресторан</th>
      <th scope="col">Дата</th>
      <th scope="col">Статус</th>
      <th scope="col">Результат </th>
    </tr>
  </thead>
  <tbody id="auditorroccall">
  </tbody>
</table>
                     </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <table class="table table-striped" id="datab">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Ресторан</th>
      <th scope="col">Email</th>
      <th scope="col">Адрес</th>
    </tr>
  </thead>
  <tbody id="userrest">
  </tbody>
</table>
                    </div>
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                     <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Время авторизации</th>
      <th scope="col">Действия</th>
    </tr>
  </thead>
  <tbody id="telegramresult">
  </tbody>
</table>
                    </div>
                    <div class="tab-pane fade" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                     <!--- auth history  -->
                      <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col"></th>
      <th scope="col">Время авторизации</th>
      <th scope="col">Клиент</th>
      <th scope="col">IP/регион</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody id="sessionresult">
  </tbody>
</table>
                     <!-- auth history -->
                  </div>
                
                </div>
              </div>
        </div>
      </div>
</div>
</div>