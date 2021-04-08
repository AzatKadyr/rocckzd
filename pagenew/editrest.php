<div class="" role="alert" id="editrest_message">
<div>
<button type="button" id="editrest" class="btn btn-primary btn-sm">Редактировать</button>
<button type="button"  onclick="history.back();" class="btn btn-secondary btn-sm">Назад</button>
</div>
<p></p>
<div class="row">
    <div class="col-5">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
 
  	 <?php
                                            

            $items = getRoccImg($_GET['restid']);
            print_r($items);
            foreach($items as $item){  
        ?>
         <div class="carousel-inner" id="imglist">
    <div class="carousel-item active">
      <img src="https://static.tildacdn.com/lib/unsplash/ead7e191-14ba-cd77-3c76-1e48a8d84f8d/photo.jpg" class="d-block w-100" alt="...">
    </div>
    </div>
    <?php
}
    ?>
  
  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Предыдущий</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Следующий</span>
  </a>
</div>
    </div>
    <div class="col-7">
        <table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Ресторан:</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Адрес:</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <td>Email</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <td>Директор</td>
      <td>@mdo</td>
    </tr>
    <tr>
      <td>Компания</td>
      <td>@mdo</td>
    </tr>
     <tr>
      <td>Рейтинг</td>
      <td>@mdo</td>
    </tr>
  </tbody>
</table>
    </div>
</div>
<div class="row">
<div class="col-12">
    </div></div>
<div class="row">

    <div class="col-4">
        <div id="chart_div"></div>
    </div>
    <div class="col-8">
        <table class="table table-striped" id="datab">
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
    </div>
</div>
</div>