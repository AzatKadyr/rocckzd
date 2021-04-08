 <?php
 $otkl = getOtklId($_GET['id']);
 
 ?>
 <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><?php echo $otkl[0]['otkl_mintext'];?></h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
              <button type="button" class="btn btn-sm btn-outline-secondary"></button>
              <button type="button" class="btn btn-sm btn-outline-secondary">Экспорт</button>
            </div>
          </div>
        </div>
<p>
</p>
        <div class="table-responsive">
            <p><?php echo $otkl[0]['otkl_category'];?> <span class="badge badge-<?php echo $otkl[0]['otkl_css'];?>"><?php echo $otkl[0]['otkl_subcategory'];?></span></p>
            <p>Ресторан: <?php echo $otkl[0]['otkl_restname'];?> | Дата добавления: <?php echo $otkl[0]['otkl_date'];?> | Пользователь: <?php echo $otkl[0]['otkl_auditor'];?> </p>
          <img src="/uploads/<?php echo $otkl[0]['otkl_img'];?>" class="img-fluid" alt="...">
        </div>