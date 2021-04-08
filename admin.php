<?php
require_once 'oop.php';
$data = getLastOtkl();
?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
<script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="js/admin.js"></script>
  <title>Административная часть</title>
  <style>
    .bd-placeholder-img {
      font-size: 1.125rem;
      text-anchor: middle;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }
    
    @media (min-width: 768px) {
      .bd-placeholder-img-lg {
        font-size: 3.5rem;
      }
    }
  </style>

  <!-- Custom styles for this template -->
  <link href="https://bootstrap-4.ru/docs/4.6/examples/dashboard/dashboard.css" rel="stylesheet">
</head>

<body>

  <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
    <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="#">Админка | rocc.kz </a>
    <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <input class="form-control form-control-dark w-100" type="text" placeholder="Поиск" aria-label="Поиск">
    <ul class="navbar-nav px-3">
      <li class="nav-item text-nowrap">
        <a class="nav-link" href="/index.php?menu=reportall">Перейти на сайт</a>
      </li>
    </ul>
  </nav>

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="sidebar-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="/admin.php?menu=main">
                <span data-feather="home"></span> Панель управления <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=allaudits">
                <span data-feather="file"></span> Список аудитов
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=company">
                <span data-feather="layers"></span> Управление компаниями
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=restall">
                <span data-feather="shopping-cart"></span> Рестораны
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=userall">
                <span data-feather="users"></span> Пользователи
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=reports">
                <span data-feather="bar-chart-2"></span> Отчеты
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=mon">
                <span data-feather="bar-chart-2"></span> Мониторинг
              </a>
            </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Последние отклонения</span>
          <a class="d-flex align-items-center text-muted" href="#" aria-label="Добавить">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
          <ul class="nav flex-column mb-2" id="lastotkla">
              <?php
              
            foreach($data as $item){  
              ?>
            <li class="nav-item">
              <a class="nav-link" href="/admin.php?menu=view&type=otkl&id=<?php echo $item['otkl_id'];?>">
                <span data-feather="file-text"></span><span class="badge badge-<?php echo $item['otkl_css'];?>"> <?php echo $item['otkl_subcategory'];?></span> <?php echo $item['otkl_mintext'];?>
                
              </a>
            </li>
          <?php
            } 
          ?>
          </ul>
        </div>
      </nav>

      <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
         <?php
        if(isset($_GET['menu'])){
            if(isset($_COOKIE['auth'])){
            $aza = $_GET['menu'];
            }else{
            $aza = "auth";
            }

            include 'admin/'.$aza.'.php';
        }else{
         
            header("Location:/admin.php?menu=main");
          
        }
        ?>
      </main>
    </div>
  </div>

<script>
    window.jQuery || document.write('<script src="https://bootstrap-4.ru/docs/4.6/dist/js/bootstrap.bundle.min.js"><\/script>')
  </script>
  <script src="https://bootstrap-4.ru/docs/4.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
  <script src="/js/chart.js"></script>
</body>

</html>