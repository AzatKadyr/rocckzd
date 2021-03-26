<?php
require_once 'oop.php';
?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Rocc аудит">

    <title>Аудит</title>
    <link href="http://bootstrap-4.ru/docs/4.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://bootstrap-4.ru/docs/4.1/examples/album/album.css" rel>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/app.js"></script>
    <link href="libs/awesome/all.min.css" type="text/css" rel="stylesheet" />
    <link rel="stylesheet" href="css/colorbox.css" />
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="/css/fontawesome.css" rel="stylesheet">
    <link href="/css/brands.css" rel="stylesheet">
    <link href="/css/new.css" rel="stylesheet">
    <link href="/css/solid.css" rel="stylesheet">
    <script defer src="/js/brands.js"></script>
    <script defer src="/js/solid.js"></script>
    <script defer src="/js/fontawesome.js"></script>
    <script type="text/javascript">
      // Load the Visualization API and the corechart package.
      google.charts.load('current', {
        'packages': ['corechart']
      });
      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);
      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['Mushrooms', 3],
          ['Onions', 1],
          ['Olives', 1],
          ['Zucchini', 1],
          ['Pepperoni', 2]
        ]);
        // Set chart options
        var options = {
          'title': 'How Much Pizza I Ate Last Night',
          'width': 400,
          'height': 300
        };
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
    <!--<link href="https://fonts.googleapis.com/css?family=Fira+Sans|Open+Sans+Condensed:300&display=swap" rel="stylesheet">-->
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

  <body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light static-top mb-5 shadow">
      <div class="container">
        <a class="navbar-brand" href="#"><img src="/uploads/logo/logo.png" height="45" width="100"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
              <a class="nav-link" href="/index.php?menu=reportall">Все аудиты
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?menu=restall">Рестораны</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?menu=userall">Пользователи</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?menu=start">Начать аудит</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/index.php?menu=my">Мой профиль</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main role="main">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="card card-body">
              <div class="" role="alert" id="message"></div>
              <!---->
              <?php
        if(isset($_GET['menu'])){
            if(isset($_COOKIE['auth'])){
            $aza = $_GET['menu'];    
            }else{
            $aza = "auth"; 
            }
          
            include 'page/'.$aza.'.php';
        }else{
          if(isset($_COOKIE['r_reportid'])){
            header("Location:/index.php?menu=reportall");
          }else{
            header("Location:/index.php?menu=start");
          }
        }
        ?>
                <!---->
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
                            2.Найдите наш бот в телеграме <a href="http://t.me/rocckzkzbot">@rocckzkzbot</a>
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

            </div>
          </div>
        </div>
      </div>
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