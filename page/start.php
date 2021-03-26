<input class="form-control" type="text" placeholder="" value="<?php echo $_COOKIE['userid'] ?>" name="admin" id="admin" readonly hidden>
      <p>
         <div class="" id="message" role="alert"></div>
        <p><strong>Ресторан:</strong></p> 
      <select class="form-control" name="restid" required id="restid">
      </select> 
  </p>
  <p>
    <p><strong>Дата проведения:</strong></p>  
    <input type="text" class="form-control" required="" name="date" value = "<?php date_default_timezone_set('Asia/Almaty');
echo $date = date('Y-m-d');?>" readonly>
  </p>
     <p><strong>Тип аудита:</strong></p> 
      <select class="form-control" name="vid" id="spisok" required>
      </select> 
  </p>

  <p>
    <button type="submit" id = "start" class="btn btn-lg btn-primary btn-block">Начать аудит</button>
  </p>