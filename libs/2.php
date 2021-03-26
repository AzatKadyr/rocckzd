<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Личный кабинет сотрудника ресторана">

    <title>Аудит</title>
    <link href="http://bootstrap-4.ru/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://bootstrap-4.ru/docs/4.1/examples/album/album.css" rel

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="css/alert.css" />
        <link href="https://rocc.kz/libs/awesome/all.min.css" type="text/css" rel="stylesheet" />
        <link rel="stylesheet" href="https://rocc.kz/css/colorbox.css" />
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>

<script>
  var OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    OneSignal.init({
      appId: "10694bdc-79b0-4f11-8240-790969d111c3",
    });
  });
</script>
<script>
 $(document).ready(function() { 
    
            $('#photoimg').die('click').live('change', function()     { 
                 //$("#preview").html('');
          
        $("#imageform").ajaxForm({target: '#preview', 
             beforeSubmit:function(){ 
          
          console.log('ttest');
          $("#imageloadstatus").show();
           $("#imageloadbutton").hide();
           }, 
          success:function(){ 
            console.log('test');
           $("#imageloadstatus").hide();
           $("#imageloadbutton").show();
          }, 
          error:function(){ 
          console.log('xtest');
           $("#imageloadstatus").hide();
          $("#imageloadbutton").show();
          } }).submit();
          
    
      });
        }); 
</script>

<style>
.form-row {
  margin-bottom: 15px;
}
.form-row label {
  display: block;
  color: #777;
  margin-bottom: 5px;
}
.form-row input[type="text"] {
  width: 100%;
  padding: 5px;
  box-sizing: border-box;
}
 
/* Стили для вывода превью */
.img-item {
  display: inline-block;
  margin: 0 20px 20px 0;
  position: relative;
  user-select: none;
}
.img-item img {
  border: 1px solid #767676;
}
.img-item a {
  display: inline-block;
  background: url(/remove.png) 0 0 no-repeat;
  position: absolute;
  top: -5px;
  right: -9px;
  width: 20px;
  height: 20px;
  cursor: pointer;
}
</style>
</head>
<body>

    <header>
      <!-- /inc/header.php -->
    <?php
include_once "inc/header.php"; // это подключит a.php
?>
    </header>

  <main role="main">
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="card card-body">
            <div>

  <form method="post" action="/save_reviews.php">
  <div class="form-row">
    <label>Изображения:</label>
    <div class="img-list" id="js-file-list"></div>
    <input id="js-file" type="file" name="file[]" multiple accept=".jpg,.jpeg,.png,.gif">
  </div>
</form>
 
<script>
$("#js-file").change(function(){
  if (window.FormData === undefined) {
    alert('В вашем браузере загрузка файлов не поддерживается');
  } else {
    var formData = new FormData();
    $.each($("#js-file")[0].files, function(key, input){
      formData.append('file[]', input);
    });
 
    $.ajax({
      type: 'POST',
      url: '/upload_image.php',
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      dataType : 'json',
      success: function(msg){
        msg.forEach(function(row) {
          if (row.error == '') {
            $('#js-file-list').append(row.data);
          } else {
            alert(row.error);
          }
        });
        $("#js-file").val(''); 
      }
    });
  }
});
 
/* Удаление загруженной картинки */
function remove_img(target){
  $(target).parent().remove();
}
</script>

<a href="/3.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Далее</a>

<a href="/1.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Заполните форму</a>

</div>
          </div>
        </div>
      </div>
    </div>
  </main>
<footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Наверх</a>
        </p>
        <p>Техническая поддержка <?php echo $_SESSION['R_RREPORTID']; ?> &copy; Azat Kadyr</p>
      </div>
    </footer>
 </body>
</html>