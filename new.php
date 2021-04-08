
<!DOCTYPE html>

<html lang="ru-RU">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Rocc.kz - аудиты ресторана</title>
    <link href="http://bootstrap-4.ru/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/8.6/styles/github-gist.min.css" rel="stylesheet">
  <link href="//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,300&amp;subset=latin,cyrillic-ext" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://gostash.it/css/overrides.css?v=1576484664" rel="stylesheet">
  <link href="https://gostash.it/css/font-awesome.min.css?v=1458044087" rel="stylesheet">
  <link href="https://gostash.it/css/site.css?v=1576484664" rel="stylesheet">
  <link href="https://gostash.it/css/perfect-scrollbar.min.css?v=1458044087" rel="stylesheet">
  <link href="https://gostash.it/css/bootstrap-select.min.css?v=1458044087" rel="stylesheet"> 
  <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
    <meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="application-name" content="Rocc.kz">
<meta name="apple-mobile-web-app-title" content="Rocc.kz">
<meta name="theme-color" content="#0B1907">
<meta name="msapplication-navbutton-color" content="#0B1907">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="msapplication-starturl" content="https://dev.roccc.top/">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script defer src="/js/brands.js"></script>
    <script defer src="/js/solid.js"></script>
    <script defer src="/js/fontawesome.js"></script>
     <script type="text/javascript" src="js/jquery.wallform.js"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $("#photoimg").change(function() {
          var A = $("#imageloadstatus");
          var B = $("#imageloadbutton");
          $("#imageform").ajaxForm({
            target: '#preview',
            beforeSubmit: function() {
              A.show();
              B.hide();
            },
            success: function() {
              A.hide();
              B.show();
              getRocc($("#reportid").val());
            },
            error: function() {
              A.hide();
              B.show();
            }
          }).submit();
        });
      });
    </script>

  </head>

</head>
<body class="windows-font" style="">
  <div class="wrap">

    <nav class="navbar-inverse navbar navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/"><img src="https://gostash.it/img/logo.svg" /></a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-collapse">
          <!-- Search form !-->
          
          <!-- /Search form !-->

          <ul id="w1" class="navbar-nav nav">
            <li><a href="/index.php?menu=reportall" class="nav-action icon-btn"><i class="material-icons nav-icon">view_day</i> <span>Все аудиты</span></a></li>
            <li><a href="/ru/stashes/favorite" class="nav-action icon-btn"><i class="material-icons nav-icon">star</i> <span>Избранное</span></a>
              <li><a href="/ru/vacancies" class="nav-action icon-btn"><i class="material-icons nav-icon">public</i> <span>Работа</span></a>
                <li class="visible-lg">
                  <a href="#" data-toggle="dropdown" class="dropdown" style="padding-left: 8px;padding-right: 8px;"><i class="material-icons nav-icon">more_horiz</i></a>
                  <ul id="w0" class="dropdown-menu">
                    <li class="dropdown-header">Вакансии</li>
                    <li><a href="/ru/vacancies" tabindex="-1">Все вакансии</a></li>
                    <li style="height: 10px">&nbsp</li>
                    <li class="dropdown-header">По языкам:</li>
                    <li><a href="/ru/vacancies?query=php" tabindex="-1">PHP</a></li>
                    <li><a href="/ru/vacancies?query=JavaScript" tabindex="-1">JS</a></li>
                    <li class="divider"></li>
                    <li class="dropdown-header">Сообщество</li>
                    <li><a href="/ru/users" tabindex="-1">Пользователи</a></li>
                    <li class="divider"></li>
                    <li><a href="/ru/support" tabindex="-1">Написать нам</a></li>
                  </ul>
                </li>
          </ul>
          <ul id="w3" class="navbar-nav nav navbar-right">
            <li class="hidden-sm"><a href="#" class="nav-action icon-btn" data-toggle="dropdown" class="dropdown" style="padding-left: 8px;padding-right: 8px;"><i class="material-icons nav-icon">add</i></a>
              <ul id="w2" class="dropdown-menu">
                <li><a href="/index.php?menu=start" tabindex="-1">Создать аудит</a></li>

                <li class="divider"></li>
                <li><a href="" tabindex="-1">+Ресторан</a></li>
              </ul>
            </li>
            <li class="hidden-sm"><a href="/ru/notifications" class="nav-action icon-btn" style="padding-left: 8px;padding-right: 8px;"><i class="material-icons nav-icon">notifications_none</i></a></li>
            <li><a href="/ru/login">Войти</a></li>
            <li><a href="/ru/signup">Регистрация</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="text-center h4 message-unregistered">
      Для получения полного доступа
      <br><a href="/ru/signup">зарегистрируйтесь</a>.
    </div>
    <div class="container">
      <h4>
    Вакансии    <small><a href="/ru/vacancy/create" style="margin-left: 15px;">
            <i class="fa fa-external-link-square "></i> Опубликуйте <b>Вакансию</b></a>
    </small>
</h4>
      <div class="vacancies">
        <a class=" hide btn btn-primary btn-sm pull-right" href="/ru/vacancy/create" style="line-height: 20px"><i class="material-icons sm">add</i>Добавить вакансию</a>
        <div class="page-content">
          <div style="font-size: .95em;">
            <h4 style="font-size: 16px;">Фильтр</h4>
            <div >
Период: <input type="datetime" id="datea" value="<?php  echo $dateb;?> 00:00:00"> <input type="datetime" id="dateb" value="<?php global $datea; echo $datea;?> 23:59:59"> 
Показать: <select id="typed" class="">
    <option value="all">Все</option>
    <option value="start">Незавершенные</option>
    <option value="final">Завершенные</option>
    <option value="telegram">Быстрые аудиты</option>
</select>
Ресторан: <select id="filterrest" class="">
    <option value="all">Все</option>
</select>
<button type="button" id="filterbtn" class="btn btn-primary btn-sm">Применить</button>
</div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12">

            <div class="page-content">
              <section class="list">
                <div class="items">
                       <!---->
              <?php
        if(isset($_GET['menu'])){
            if(isset($_COOKIE['auth'])){
            $aza = $_GET['menu'];
            }else{
            $aza = "auth";
            }

            include 'pagenew/'.$aza.'.php';
        }else{
          if(isset($_COOKIE['r_reportid'])){
            header("Location:/index.php?menu=reportall");
          }else{
            header("Location:/index.php?menu=start");
          }
        }
        ?>
                </div>
              </section>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
                <!-- Модальное окно -->
                <div class="modal fade" id="loadmodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Пожалуйста подождите</h5>

                      </div>
                      <div class="modal-body" id="load_message">
                        <center><img src="loader.gif" alt="Идет загрузка...." /></center>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closemod">Закрыть</button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="modal fade" id="telegramrest" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Выберите ресторан для продолжения</h5>

                      </div>
                      <div class="modal-body" id="load_message">
                        <p><strong>Ресторан:</strong></p>
                        <select class="form-control" name="restidd" required id="restidd">
                        </select>
                      </div>
                      <div class="modal-footer">
                        <button type="button" id="telegramrestbtn" class="btn btn-primary btn-lg btn-block">Продолжить</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Генерация токена -->
                <div class="modal fade" id="generatetokentelegram" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Генерация токена телеграм</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <center><img src="https://img.icons8.com/cute-clipart/256/000000/telegram-app.png" /></center>
                        <p></p>
                        <div id="tokentelegram"></div>
                        <div class="card">
                          <div class="card-body">
                            1. Сгенерируйте токен
                          </div>
                        </div>
                        <p></p>
                        <div class="card">
                          <div class="card-body">
                            2.Найдите наш бот в телеграме <a href="http://t.me/rocckzbot">@rocckzbot</a>
                          </div>
                        </div>
                        <p></p>
                        <div class="card">
                          <div class="card-body">
                            3. Введите токен в чате и можете начинать аудит
                          </div>
                        </div>
                        <p></p>

                        <input name="tokentelegram" type="text" class="form-control form-control-lg" hidden>

                      </div>
                      <div class="modal-footer">
                        <button type="button" id="btngenerate" class="btn btn-primary btn-lg btn-block" onclick="generateToken()">Сгенерировать</button>
                      </div>
                    </div>
                  </div>
                </div>
 
</body>

</html>