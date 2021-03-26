
<?php
require_once 'oop.php';
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Аудит</title>
    <link href="http://bootstrap-4.ru/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://bootstrap-4.ru/docs/4.1/examples/album/album.css" rel

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
      <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/pdf.js"></script>
        <link href="libs/awesome/all.min.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="css/colorbox.css" />
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="js/jquery.wallform.js"></script>
<script src="js/jquery.colorbox.js"></script>
<!--<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>-->
<link href="https://fonts.googleapis.com/css?family=Fira+Sans|Open+Sans+Condensed:300&display=swap" rel="stylesheet">
<style>
body{
    background-image:url(http://beta.rocc.kz/image/fon.jpg);
    font-family: 'Fira Sans', sans-serif;
    -moz-background-size: 100%; /* Firefox 3.6+ */
    -webkit-background-size: 100%; /* Safari 3.1+ и Chrome 4.0+ */
    -o-background-size: 100%; /* Opera 9.6+ */
    background-size: 100%; 
    zoom: 85%;
}
    ul{
  list-style:none;
  margin:0;
  padding:0;
  text-align:center;
}
li{
  display:inline-block;
  color: #6C747C;
  padding:1px;
  font-size:25px;
}
li:hover{
  display:inline-block;
  color: #4287f5;
  padding:1px;
  font-size:25px;
  margin: 0 3 0 3;
}
#preview
{
color:#cc0000;
font-size:12px
}
.imgList 
{
max-height:150px;
margin-left:5px;
border:1px solid #dedede;
padding:4px;  
float:left; 
}
.category1{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://beta.rocc.kz/image/pb.png);
border-radius:0;
background-size: cover;
}
.category2{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://beta.rocc.kz/image/sb.png);
    border-radius:0;
    background-size: cover;
}
.category3{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://beta.rocc.kz/image/lb.png);
    border-radius:0;
    background-size: cover;
}
.level1{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://rocc.kz/image/l1.png);
    border-radius:0;
    background-size: cover;
}
.level2{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://rocc.kz/image/l2.png);
    border-radius:0;
    background-size: cover;
}
.level3{
  height: 35px;
  width: 35px;
 color:#fff;
background-image: url(http://rocc.kz/image/l3.png);
    border-radius:0;
    background-size: cover;
}
#qr{
    margin-top: 25px; /* Отступ сверху */
}
</style>
</head>
<body>

    <header>
      
<div class="row">
  <div class="col-10"><img src="http://rocc.kz/image/logoreport.png" class="img-fluid" alt="ROCC онлайн система"></div>
 <div class="col-2" id="qr">
  <!--<img src="http://api.rocc.kz/img/report/<?php echo $_GET['reportid'] ?>.png " class="img-fluid" alt="ROCC онлайн система"> -->
  </div>
</div>
    </header>
<input type="text" id="reportid" value="<?php $aza = $_GET['reportid']; echo $aza;?>" hidden>
  <main role="main">

<?php
$data = getRocc($_GET['reportid']);
$qrcode = generateQr($_GET['reportid']);
//print_r($data);
?>
           
            <!---->
               
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
  <tbody id="roccinfo">
       <tr>
      <td><?php echo $data['info']['rocc_id']?></td>
      <td><?php echo $data['info']['restid']?> | <?php echo $data['info']['rest_name']?></td>
      <td><?php echo $data['info']['rocc_opentime']?></td>
      <td><?php echo $data['info']['auditor_name']?></td>
      <td><?php echo $data['info']['rocc_statusname']?> </td>
    </tr>
  </tbody>
</table>
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
</table></center>
<hr>
<h6>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-2">
            <img src="/<?php echo $qrcode; ?>" class="img-fluid" alt="ROCC онлайн система">
            <!--<br>Для быстрого доступа к заполнению экшн плана просканируйте qr код-->
        </div>
        <div class="col-9">
            
        </div>
    </div>
</h6>
<hr>  
  </main>
<footer class="text-muted">
      <div class="container">
        <p class="float-right">
        </p>
        <p>Техническая поддержка &copy; Azat Kadyr</p>
      </div>
    </footer>
 </body>
</html>