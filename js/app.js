    $(document).ready(function(){
    getRest($("#datea").val(),$("#dateb").val(),'all');
    getSpisok();
    getAllUser();
    getActionPlan($("#reportid").val());
    getRocc($("#reportid").val());
    $("#uploadall").hide();   
    $("#closemod").hide(); 
    $("#filter").hide();
    $("#uploadphoto").hide();
    
    $('.button--excel').attr('disabled', false);
    var excel_data = $('#report_table').html(); 
    $('#xls_data').val(excel_data);
    
    $("#filterstart").click(function(){
     getRest($("#datea").val(),$("#dateb").val(),'start');
     });
     
     $("#photobtn").click(function(){
     uploadPhoto();
     });
     
     $("#filterfinal").click(function(){
     getRest($("#datea").val(),$("#dateb").val(),'final');
     });
     
     $("#filterall").click(function(){
     getRest($("#datea").val(),$("#dateb").val(),'all');
     });
     
     $("#filtertel").click(function(){
     getRest($("#datea").val(),$("#dateb").val(),'telegram');
     });
     
     $("#filterbtn").click(function(){
    getRest($("#datea").val(),$("#dateb").val(),$("#typed").val());
     $("#filter").hide();
     });
     
      $("#filterbtnn").click(function(){
     $("#filter").show();
     });
     
    $("#start").click(function(){
     proverkaRocc($("#restid").val(),$("#spisok").val());
     });
     
     $("#finalbtn").click(function(){
     editRocc($("#reportid").val(),'final',$("#resultrocc").val());
     });
     
    $("#save-edit-otkl").click(function(){
     saveEditOtkl($("#editotklid").val(),$("#editmintext").val(),$("#editfulltext").val());
     });
    
    $("#save-edit-otkl-catlevel").click(function(){
     saveEditOtklLevel($("#editotklid-cat").val(),$("#editcategory").val(),$("#editsubcat").val());
     });
    
    $("#telegramrestbtn").click(function(){
     saveRestTelegram($("#reportid").val(),$("#restidd").val());
     });

    $("#editcategory").click(function(){
     getSubcat($("#editotklid-cat").val(),$("#editcategory").val());
     console.log('нажата');
     });
     
    $("#authbtn").click(function(){
     auth($("#userlist").val());
     });
    
    $("#startroccurl").click(function(){
     window.location.href = '/index.php?menu=start';
     });
     
     $("#plitvid").click(function(){
      $("#datab").hide();
      $("#pagem").show();
     getRestPlitka();
     });
     
     $("#tablevid").click(function(){
     $("#pagem").hide();
     $("#datab").show();
     getRest();
     });
     
     $("#addrestbtn").click(function(){
     addRest($("#restid").val(),$("#restemail").val(),$("#restname").val(),$("#dirname").val(),$("#adress").val());
     });
    
    $(".search").keyup(function () {
    var searchTerm = $(".search").val();
    var listItem = $('.results tbody').children('tr');
    var searchSplit = searchTerm.replace(/ /g, "'):containsi('")
    
  $.extend($.expr[':'], {'containsi': function(elem, i, match, array){
        return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
  });
    
  $(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','false');
  });

  $(".results tbody tr:containsi('" + searchSplit + "')").each(function(e){
    $(this).attr('visible','true');
  });

  var jobCount = $('.results tbody tr[visible="true"]').length;
    $('.counter').text(jobCount + ' item');

  if(jobCount == '0') {$('.no-result').show();}
    else {$('.no-result').hide();}
      });

    // Edit photo
    $('#editImgModal').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Кнопка, запускающая модальное окно
  	var imgrocc = button.data('img') // Извлечь информацию из атрибутов data- *
  	// При необходимости Вы можете инициировать здесь запрос AJAX (а затем выполнить обновление в обратном вызове).
  	// Обновите содержимое модального окна. Здесь мы будем использовать jQuery, но вместо этого Вы можете использовать библиотеку привязки данных или другие методы.
  	
  	var imgSa = "<img src='/uploads/"+imgrocc+"' class='img-fluid'>"
  	var modal = $(this)
  	modal.find('#roccimgedit').html(imgSa);
	})

    // Edit text
    $('#editTextModal').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Кнопка, запускающая модальное окно
  	var mintext = button.data('mintext') // Извлечь информацию из атрибутов data- *
  	var fulltext = button.data('fulltext')
  	var id = button.data('id')
  	// При необходимости Вы можете инициировать здесь запрос AJAX (а затем выполнить обновление в обратном вызове).
  	// Обновите содержимое модального окна. Здесь мы будем использовать jQuery, но вместо этого Вы можете использовать библиотеку привязки данных или другие методы.
  	
  	var modal = $(this)
  	modal.find('#editotklid').val(id);
  	modal.find('#editmintext').val(mintext);
  	modal.find('#editfulltext').val(fulltext);
	})
	
	// Edit level
    $('#editLevelModal').on('show.bs.modal', function (event) {
  	var button = $(event.relatedTarget) // Кнопка, запускающая модальное окно
  	var id = button.data('id')
  	getLevel(id);
  	// При необходимости Вы можете инициировать здесь запрос AJAX (а затем выполнить обновление в обратном вызове).
  	// Обновите содержимое модального окна. Здесь мы будем использовать jQuery, но вместо этого Вы можете использовать библиотеку привязки данных или другие методы.
  	var modal = $(this)
  	modal.find('#editotklid-cat').val(id);
  	
	})

    });

    
     // YYYY (M-1) D H m s ms (start time and date from DB)
// for storing the interval (to stop or pause later if needed)
	
	function generateToken(){

      $.get("api.php", {
        
       "type": "generatetoken"

      }, function(data){

        data = JSON.parse(data);
        console.log(data);
        tok = "<div class='alert alert-primary' role='alert' ><b><h1><center>"+data['token']+"</center></b></h1></div>"
        $("#tokentelegram").html(tok);
      })

    }

function uploadPhoto(){
    $("#uploadphoto").show();
}
    
  function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

	function getSubcat(otklid,catid){

      $.get("api.php", {
        
       "type": "getsubcat",
       "otklid": otklid,
       "catid": catid

      }, function(data){

        data = JSON.parse(data);

        tableHTMLdd = "";
        ////console.log(data);

          tableHTMLdd+="<option value='"+data['cat']['main'][0]['otkl_subcatid']+"'>"+data['cat']['main'][0]['otkl_subcatname']+" (Текущий)</option>";
        for(i=0;i<data['spisok'].length;i++){
          tableHTMLdd+="<option value='"+data['spisok'][i]['subcat_id']+"'>"+data['spisok'][i]['subcat_name']+"</option>";
        }
       // console.log(tableHTMLdd);
        $("#editsubcat").html(tableHTMLdd);
      })

    }

    function getActionPlan(reportid){

      $.get("api.php", {
        
       "type": "getrocc",
       "reportid": reportid

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);

        tableHTML = "";
        tableHTMLa = "";
       if(data['info']==null){
            $("#acplann_message").attr('class', 'alert alert-danger');
          $("#acplann_message").html('По данному ID action plan не найден или был удален  <a href="/index.php?menu=reportall">Все аудиты</a>');
       }else{
            if(data['info']['actionplan_id']==null){
            $("#acplann_message").attr('class', 'alert alert-danger');
          $("#acplann_message").html('По данному ID action plan не найден или был удален  <a href="/index.php?menu=reportall">Все аудиты</a>');
        }else{
          $("#acplanid").val(data['info']['actionplan_id']);
          tableHTMLa+="<tr>";
          tableHTMLa+="<td>"+data['info']['rocc_id']+" | "+data['info']['actionplan_id']+"</td>";
          tableHTMLa+="<td>"+data['info']['rest_name']+"</td>";
          tableHTMLa+="<td>"+data['info']['actionplan_date']+"</td>";
          tableHTMLa+="<td></td>";
          tableHTMLa+="<td>"+data['info']['actionplan_status']+"</td>";
          tableHTMLa+="</tr>";
        

        $("#acplaninfo").html(tableHTMLa);
        }
       }
        
      }
    )
    }

    function actionA(type){
    if(type=="all"){
    $("#uploadone").hide();
    $("#uploadall").show();   
    }
    
    if(type=="one"){
    $("#uploadall").hide();   
    $('#uploadonemodal').modal('show')
    }
    
    }

    function getRocc(reportid){

      $.get("api.php", {
        
       "type": "getrocc",
       "reportid": reportid

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        countReport(reportid);
        tableHTML = "";
        tableHTMLb = "";
        tableHTMLba = "";
        tableHTMLbaa = "";
        userid = getCookie('userid');
        if(data['info']==null){
            $("#roccedit_message").attr('class', 'alert alert-danger');
          $("#roccedit_message").html('По данному ID аудит не найден или был удален  <a href="/index.php?menu=reportall">Все аудиты</a>');
        }else{
            if(data['info']['auditor_id']==userid){
                
            }else{
                 $("#roccedit_message").attr('class', 'alert alert-danger');
              $("#roccedit_message").html('У вас нет Доступа к данному аудиту |  <a href="/index.php?menu=reportall">Все аудиты</a>');
            }
            if(data['info']['rocc_status']=='final'){
                $("#roccedit_message").attr('class', 'alert alert-danger');
              $("#roccedit_message").html('Данный аудит завершен  <a href="/index.php?menu=report&id">Посмотреть</a>');
            }else{
            	if(data['info']['rest_name']==null){
            		$('#telegramrest').modal('show');
            		console.log('Не выбран ресторан');
            	}
                tableHTMLb+="<tr>";
                tableHTMLb+="<td>"+data['info']['rocc_id']+"</td>";
                tableHTMLb+="<td>"+data['info']['rest_name']+"</td>";
                tableHTMLb+="<td>"+data['info']['rocc_opentime']+"</td>";
                tableHTMLb+="<td>"+data['info']['auditor_name']+"</td>";
                tableHTMLb+="<td>"+data['info']['rocc_statusname']+"</td>";
                tableHTMLb+="</tr>";
                
                if(data['otkl']==null){}else{
                for(i=0;i<data['otkl'].length;i++){
                tableHTMLba+="<tr>";
                tableHTMLba+="<td><img src='/uploads/"+data['otkl'][i]['otkl_img']+"' alt='...' width='150' height='150' class='img-thumbnail'></a></td>";
                tableHTMLba+="<td>"+data['otkl'][i]['otkl_mintext']+"</td>";
                tableHTMLba+="<td>"+data['otkl'][i]['otkl_catname']+" "+data['otkl'][i]['otkl_subcatname']+"</td>";
                tableHTMLba+="<td></td>";
                tableHTMLba+="</tr>";
                }
                $("#otklresult").html(tableHTMLba);   
                
                for(i=0;i<data['otkl'].length;i++){
                tableHTMLbaa+="<tr>";
                tableHTMLbaa+="<td><a href='#' data-toggle='modal' data-target='#editImgModal' data-id='"+data['otkl'][i]['otkl_id']+"' data-img='"+data['otkl'][i]['otkl_img']+"'><img src='/uploads/"+data['otkl'][i]['otkl_img']+"' alt='...' width='150' height='150' class='img-thumbnail'></a></td>";
                tableHTMLbaa+="<td>"+data['otkl'][i]['otkl_mintext']+"</td>";
                tableHTMLbaa+="<td><a href='#' data-toggle='modal'  data-target='#editLevelModal' data-id='"+data['otkl'][i]['otkl_id']+"'>"+data['otkl'][i]['otkl_catname']+"</a></td>";
                tableHTMLbaa+="<td><a href='#'>"+data['otkl'][i]['otkl_subcatname']+"</a></td>";
                tableHTMLbaa+="<td><a href='#' data-toggle='modal'  data-target='#editTextModal' data-id='"+data['otkl'][i]['otkl_id']+"' data-fulltext='"+data['otkl'][i]['otkl_fulltext']+"' data-mintext='"+data['otkl'][i]['otkl_mintext']+"'><i class='fas fa-edit'></i></a></td>";
                tableHTMLbaa+="</tr>";
                }
                
                }
                
                $("#otklresulta").html(tableHTMLbaa);
                $("#roccinfo").html(tableHTMLb);
                
            }
          
        }
        
        if(data['info']==null){
            $("#rocc_message").attr('class', 'alert alert-danger');
          $("#rocc_message").html('По данному ID аудит не найден или был удален  <a href="/index.php?menu=reportall">Все аудиты</a>');
        }else{
        
          tableHTML+="<tr>";
          tableHTML+="<td>"+data['info']['rocc_id']+"</td>";
          tableHTML+="<td>"+data['info']['rest_name']+"</td>";
          tableHTML+="<td>"+data['info']['rocc_opentime']+"</td>";
          tableHTML+="<td>"+data['info']['auditor_name']+"</td>";
          tableHTML+="<td>"+data['info']['rocc_statusname']+"</td>";
          tableHTML+="</tr>";
        
          if(data['otkl']==null){}else{
                for(i=0;i<data['otkl'].length;i++){
                tableHTMLba+="<tr>";
                 tableHTMLba+="<td><img src='/uploads/"+data['otkl'][i]['otkl_img']+"' alt='...' width='150' height='150' class='img-thumbnail'></a></td>";
                tableHTMLba+="<td>"+data['otkl'][i]['otkl_mintext']+"</td>";
                tableHTMLba+="<td>"+data['otkl'][i]['otkl_catname']+" "+data['otkl'][i]['otkl_subcatname']+"</td>";
                tableHTMLba+="<td></td>";
                tableHTMLba+="</tr>";
                }
                $("#otklresult").html(tableHTMLba);    
                }
                
        $("#roccinfo").html(tableHTML);
        }
      }
    )
    }
    
    function auth(userid){

      $.get("api.php", {
        
       "type": "auth",
       "userid": userid

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        if(data['status']=='OK'){
            location.reload()
        }
      })

    }
    
    
    function saveEditOtkl(id,mintext,fulltext){

      $.get("api.php", {
        
       "type": "saveotkl",
       "id": id,
       "mintext": mintext,
       "fulltext": fulltext

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        if(data['status']=='OK'){
            getRocc($("#reportid").val());
            $('#editTextModal').modal('hide')
        }
      })

    }
    

    function saveRestTelegram(reportid,restid){

      $.get("api.php", {
        
       "type": "saveresttelegram",
       "reportid": reportid,
       "restid": restid

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        if(data['status']=='OK'){
            getRocc($("#reportid").val());
            $('#telegramrest').modal('hide')
        }
      })

    }

    function saveEditOtklLevel(id,cat,subcat){

      $.get("api.php", {
        
       "type": "saveotkllevel",
       "id": id,
       "cat": cat,
       "subcat": subcat

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        if(data['status']=='OK'){
            getRocc($("#reportid").val());
            $('#editLevelModal').modal('hide')
        }
      })

    }

    function generatePdf(type,reportid){

      $('#loadmodal').modal('show');
      $.get("api.php", {
        
       "type": "generatepdf",
       "typepdf": type,
       "reportid": reportid

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        //window.open(data['url'])
        window.location.href = data['url'];
        $('#loadmodal').modal('hide');
      })

    }
    
    function deleteSession(id){

      $.get("api.php", {
        
       "type": "deletesession",
       "id": id

      }, function(data){

        data = JSON.parse(data);
        console.log(data);
        getSession();
      })

    }

    function getUserinfo(userid){

      $.get("api.php", {
        
       "type": "getuserinfo",
       "userid": userid

      }, function(data){

        data = JSON.parse(data);
        console.log(data);
        tableHTML = "";
        tableHTMLrest = "";
        tableHTMLauditsrest = "";
        tableHTMLauditsrestab = "";
        
        for(i=0;i<data['user_session'].length;i++){
          tableHTML+="<tr>";
          tableHTML+="<th scope='row'></th>";
          tableHTML+="<td>"+data['user_session'][i]['ss_date']+"</td>";
          tableHTML+="<td>"+data['user_session'][i]['ss_browser']+"</td>";
          tableHTML+="<td>"+data['user_session'][i]['ss_city']+" | "+data['user_session'][i]['ss_country']+" | "+data['user_session'][i]['ss_userip']+"</td>";
          tableHTML+="<td><a href='#' onclick='deleteSession("+data['user_session'][i]['ss_id']+");'>Удалить сессию</a></td>";
          tableHTML+="<tr>";
        }

        for(i=0;i<data['user_rest'].length;i++){
          tableHTMLrest+="<tr>";
          tableHTMLrest+="<td>"+data['user_rest'][i]['restid']+"</td>";
          tableHTMLrest+="<td>"+data['user_rest'][i]['restname']+"</td>";
          tableHTMLrest+="<td>"+data['user_rest'][i]['email']+"</td>";
          tableHTMLrest+="<td>"+data['user_rest'][i]['adress']+"</td>";
          tableHTMLrest+="<tr>";
        }

        for(i=0;i<data['audits_rest'].length;i++){
          tableHTMLauditsrest+="<tr>";
          tableHTMLauditsrest+="<th scope='row'></th>";
          tableHTMLauditsrest+="<td>"+data['audits_rest'][i]['rest_name']+"</td>";
          tableHTMLauditsrest+="<td>"+data['audits_rest'][i]['rocc_opentime']+"</td>";
          tableHTMLauditsrest+="<td>"+data['audits_rest'][i]['rocc_statusname']+"</td>";
          tableHTMLauditsrest+="<td>"+data['audits_rest'][i]['auditor_name']+"</td>";
          tableHTMLauditsrest+="<td>"+data['audits_rest'][i]['rocc_result']+"</td>";
          tableHTMLauditsrest+="<td><a href='/index.php?menu=report&id="+data['audits_rest'][i]['rocc_id']+"'>WEB</a></td>";
          tableHTMLauditsrest+="<tr>";
        }

        if(data['audits_auditor']==null){}else{
        for(i=0;i<data['audits_auditor'].length;i++){
          tableHTMLauditsrestab+="<tr>";
          tableHTMLauditsrestab+="<th scope='row'></th>";
          tableHTMLauditsrestab+="<td>"+data['audits_auditor'][i]['rest_name']+"</td>";
          tableHTMLauditsrestab+="<td>"+data['audits_auditor'][i]['rocc_opentime']+"</td>";
          tableHTMLauditsrestab+="<td>"+data['audits_auditor'][i]['rocc_statusname']+"</td>";
          tableHTMLauditsrestab+="<td>"+data['audits_auditor'][i]['rocc_result']+"</td>";
          tableHTMLauditsrestab+="<tr>";
        }	
        }
        

        $("#sessionresult").html(tableHTML);
        $("#restauditsall").html(tableHTMLauditsrest);
        $("#userrest").html(tableHTMLrest);
        $("#auditorroccall").html(tableHTMLauditsrestab);
      })

    }


    function getRest(datea,dateb,type){

      $.get("api.php", {
        
       "type": "getrestcompany",
       "typed": type,
       "datea": datea,
       "dateb": dateb

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);

        tableHTML = "";
        tableHTMLa = "";
        tableHTMLab = "";
        tableHTMLd = "";
        tableHTMLdd = "";
        acplan = "";
        tableHTMLrest = "";
        
        for(i=0;i<data['all_rest_company'].length;i++){
          tableHTML+="<option value='"+data['all_rest_company'][i]['restid']+"'>"+data['all_rest_company'][i]['restname']+"</option>";
        }
        
        tableHTMLrest+="<option value='all'>Все</option>";
        
        for(i=0;i<data['all_rest_company'].length;i++){
          tableHTMLrest+="<option value='"+data['all_rest_company'][i]['restid']+"'>"+data['all_rest_company'][i]['restname']+"</option>";
        }
        
        for(i=0;i<data['all_dir_company'].length;i++){
          tableHTMLdd+="<option value='"+data['all_dir_company'][i]['dir_id']+"'>"+data['all_dir_company'][i]['dir_name']+"</option>";
        }
        
        for(i=0;i<data['all_rest_company'].length;i++){
          tableHTMLd+="<tr>";
          tableHTMLd+="<td><a href='/index.php?menu=editrest&restid="+data['all_rest_company'][i]['restid']+"'>"+data['all_rest_company'][i]['restid']+"</a></td>";
          tableHTMLd+="<td>"+data['all_rest_company'][i]['restname']+"</td>";
          tableHTMLd+="<td>"+data['all_rest_company'][i]['email']+"</td>";
          tableHTMLd+="<td>"+data['all_rest_company'][i]['dir_name']+"</td>";
          tableHTMLd+="<td></td>";
          tableHTMLd+="</tr>";
        }
        
        if(data['all_audits_company']==null){}else{
        	for(i=0;i<data['all_audits_company'].length;i++){
         obwresult = countReportB('1');
         console.log(['obw_result']);
          if(data['all_audits_company'][i]['actionplan_status']==null){
          acplan = "";
          acplanb = "";
          }else{
          acplan = "<a href='/index.php?menu=acplan&id="+data['all_audits_company'][i]['rocc_id']+"'><span class='badge badge-"+data['all_audits_company'][i]['actionplan_css']+"'>AcPlan</span></a>";  
          acplanb = "<a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><button type='button' class='btn btn-sm btn-outline-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</button></a>";
          
          }
          
          if(data['all_audits_company'][i]['rocc_status']=='final'){
          url = "report";
          }else{
          url = "editrocc";
          }
          if(data['all_audits_company'][i]['rest_name']==null){
          	restname = "не выбран";
          }else{
          	restname = data['all_audits_company'][i]['rest_name'];
          	//restname = "НЕ ВЫБРАН";
          }
          var telegram = "";
          //var telegram = "<img src='https://img.icons8.com/plasticine/100/000000/chrome.png' height='24' width='24' >";
          if(data['all_audits_company'][i]['rocc_telegram']==1){
          	/*telegram = "<span class='badge badge-primary'>Telegram</span>";*/
          	telegram = "<img src='https://img.icons8.com/color/48/000000/telegram-app--v2.png' height='24' width='24' data-toggle='tooltip' data-placement='top' title='Данный аудит заполнен через телеграмм )) ' alt='Данный аудит заполнен через телеграмм))'>";
          }

          var timed = formatDate (data['all_audits_company'][i]['rocc_opentime']);
          tableHTMLa+="<tr>";
          tableHTMLa+="<td></td>";
          tableHTMLa+="<td>"+restname+" "+telegram+"</td>";
          tableHTMLa+="<td>"+timed+"</td>";
          tableHTMLa+="<td><a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><span class='badge badge-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</span></a>   "+acplan+"</td>";
          tableHTMLa+="<td>"+data['all_audits_company'][i]['auditor_name']+"</td>";
          tableHTMLa+="<td>"+data['all_audits_company'][i]['rocc_result']+"</td>";
          tableHTMLa+="</tr>";


          tableHTMLab+="<div class='col-md-4'>";
          tableHTMLab+="<div class='card mb-4 shadow-sm'>";
          tableHTMLab+="<img class='bd-placeholder-img card-img-top' width='100%' height='225' src='"+data['all_audits_company'][i]['rest_img']+"'>";
          tableHTMLab+="<div class='card-body'>";
          tableHTMLab+="<p class='card-text'>"+data['all_audits_company'][i]['rest_name']+"</p>";
          tableHTMLab+="<div class='d-flex justify-content-between align-items-center'>";
          tableHTMLab+="<div class='btn-group'>";
          tableHTMLab+="<a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><button type='button' class='btn btn-sm btn-outline-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</button></a>";
          tableHTMLab+=""+acplanb+"";
          tableHTMLab+="<div class='progress'><div class='progress-bar progress-bar-striped' role='progressbar' style='width: 10%' aria-valuenow='10' aria-valuemin='0' aria-valuemax='100'></div></div></div>";
          tableHTMLab+="<small class='text-muted'>"+timed+"</small>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";



        }
        }

        $("#resultreport").html(tableHTMLa);
        $("#resultrest").html(tableHTMLd);
        $("#restid").html(tableHTML);
        $("#restidd").html(tableHTML);
        $("#dirname").html(tableHTMLdd);
        $("#pageresult").html(tableHTMLab);
        $("#filterrest").html(tableHTMLrest);
      })

    }
    
        function getRestPlitka(){

      $.get("api.php", {
        
       "type": "getrestcompany",
       "typed": type

      }, function(data){

        data = JSON.parse(data);
        console.log(data);

        tableHTMLab = "";

    
          
        for(i=0;i<data['all_audits_company'].length;i++){
          if(data['all_audits_company'][i]['actionplan_status']==null){
          acplan = "";
          acplanb = "";
          }else{
          acplan = "<a href='/index.php?menu=acplan&id="+data['all_audits_company'][i]['rocc_id']+"'><span class='badge badge-"+data['all_audits_company'][i]['actionplan_css']+"'>AcPlan</span></a>";  
          acplanb = "<a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><button type='button' class='btn btn-sm btn-outline-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</button></a>";
          
          }
          
          if(data['all_audits_company'][i]['rocc_status']=='final'){
          var restname = "";
          url = "report";
          }else{
          url = "editrocc";
          }
          if(data['all_audits_company'][i]['rest_name']==null){
          	restname = "НЕ ВЫБРАН";
          }else{
          	restname = data['all_audits_company'][i]['rest_name'];
          	//restname = "НЕ ВЫБРАН";
          }
          var timed = formatDate (data['all_audits_company'][i]['rocc_opentime']);
          tableHTMLa+="<tr>";
          tableHTMLa+="<td></td>";
          tableHTMLa+="<td>"+restname+"</td>";
          tableHTMLa+="<td>"+timed+"</td>";
          tableHTMLa+="<td><a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><span class='badge badge-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</span></a>   "+acplan+"</td>";
          tableHTMLa+="<td>"+data['all_audits_company'][i]['auditor_name']+"</td>";
          tableHTMLa+="<td></td>";
          tableHTMLa+="</tr>";


          tableHTMLab+="<div class='col-md-4'>";
          tableHTMLab+="<div class='card mb-4 shadow-sm'>";
          tableHTMLab+="<img class='bd-placeholder-img card-img-top' width='100%' height='225' src='"+data['all_audits_company'][i]['rest_img']+"'>";
          tableHTMLab+="<div class='card-body'>";
          tableHTMLab+="<p class='card-text'>"+data['all_audits_company'][i]['rest_name']+"</p>";
          tableHTMLab+="<div class='d-flex justify-content-between align-items-center'>";
          tableHTMLab+="<div class='btn-group'>";
          tableHTMLab+="<a href='/index.php?menu="+url+"&id="+data['all_audits_company'][i]['rocc_id']+"'><button type='button' class='btn btn-sm btn-outline-"+data['all_audits_company'][i]['rocc_statuscss']+"'>"+data['all_audits_company'][i]['rocc_statusname']+"</button></a>";
          tableHTMLab+=""+acplanb+"";
          tableHTMLab+="</div>";
          tableHTMLab+="<small class='text-muted'>"+timed+"</small>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";
          tableHTMLab+="</div>";



        }

        
        $("#pagem").html(tableHTMLab);
      })

    }
    
    function getListDir(){

      $.get("api.php", {
        
       "type": "getlistdir"

      }, function(data){

        data = JSON.parse(data);

        tableHTMLdd = "";


        for(i=0;i<data.length;i++){
          tableHTMLdd+="<option value='"+data[i]['dir_id']+"'>"+data[i]['dir_name']+"</option>";
        }
        
        $("#dirname").html(tableHTMLdd);
      })

    }

    function formatDate (input) {
  var datePart = input.match(/\d+/g),
  year = datePart[0].substring(2), // get only two digits
  month = datePart[1], day = datePart[2];

  return day+'.'+month+'.'+year;
}

    function getSpisok(){

      $.get("api.php", {
        
       "type": "getspisok"

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);

        tableHTML = "";

        for(i=0;i<data.length;i++){
          tableHTML+="<option value='"+data[i]['statusid']+"'>"+data[i]['statusname']+"</option>";
        }

        $("#spisok").html(tableHTML);
      })

    }
    
    function getAllUser(){

      $.get("api.php", {
        
       "type": "getuserlist"

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);

        tableHTML = "";

        for(i=0;i<data.length;i++){
          tableHTML+="<option value='"+data[i]['id']+"'>"+data[i]['name']+"</option>";
        }

        $("#userlist").html(tableHTML);
      })

    }
    
    function countReport(reportid){

      $.get("api.php", {
        
       "type": "countreport",
       "reportid": reportid

      }, function(data){

        data = JSON.parse(data);
       	console.log(data);
       	var result = "5";
                if(data['obw_result']=="Proval"){
                    result = "Underperfoming";
                }else{
                    result = data['obw_result'];
                }
        		tableHTMLb = "";
        		tableHTMLb+= "<a href='#'><p>Скрыть этот раздел</a>";
        		
        		tableHTMLb+= "<table class='table table-bordered'>";
        		tableHTMLb+= "<thead>";
        		tableHTMLb+= "<tr>";
        		tableHTMLb+= "<th scope='col'>Результат: "+data['obw_result']+"</th>";
        		tableHTMLb+= "<th scope='col'>L1</th>";
        		tableHTMLb+= "<th scope='col'>L2</th>";
        		tableHTMLb+= "<th scope='col'>L3</th>";
                tableHTMLb+= "<th scope='col'></th>";
        		tableHTMLb+= "</tr>";
        		tableHTMLb+= "</thead>";
        		tableHTMLb+= "<tbody>";
        		

        		tableHTMLb+="<tr>";
                tableHTMLb+="<td>Пищевая Безопастность</td>";
                tableHTMLb+="<td>"+data['pb']['l1']+"</td>";
                tableHTMLb+="<td> - </td>";
                tableHTMLb+="<td>"+data['pb']['l3']+"</td>";
                tableHTMLb+="<td><span class='badge badge-"+data['pb']['result_css']+"'>"+data['pb']['result']+"</span></td>";
                tableHTMLb+="</tr>";

                tableHTMLb+="<tr>";
                tableHTMLb+="<td>Стандарты Бренда</td>";
                tableHTMLb+="<td>"+data['sb']['l1']+"</td>";
                tableHTMLb+="<td>"+data['sb']['l2']+"</td>";
                tableHTMLb+="<td>"+data['sb']['l3']+"</td>";
                tableHTMLb+="<td><span class='badge badge-"+data['sb']['result_css']+"'>"+data['sb']['result']+"</span></td>";
                tableHTMLb+="</tr>";

                tableHTMLb+="<tr>";
                tableHTMLb+="<td>Локальные Стандарты</td>";
                tableHTMLb+="<td>"+data['ls']['l1']+"</td>";
                tableHTMLb+="<td>"+data['ls']['l2']+"</td>";
                tableHTMLb+="<td>"+data['ls']['l3']+"</td>";
                tableHTMLb+="<td><span class='badge badge-"+data['ls']['result_css']+"'>"+data['ls']['result']+"</span></td>";
                tableHTMLb+="</tr>";
                
        		tableHTMLb+= "</tbody>";
        		tableHTMLb+= "</table>";
        console.log(tableHTMLb);
        
        $("#countreport").html(tableHTMLb);
        $("#resultrocc").val(data['id_result']);
      })
    }
    
    function countReportB(reportid){

      $.get("api.php", {
        
       "type": "countreportbb",
       "reportid": reportid

      }, function(data){

        data = JSON.parse(data);
        console.log('1: '+data);
        return data;
      })
    }
    
    function getLevel(id){

      $.get("api.php", {
        
       "type": "getlevel",
       "id": id

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        tableHTMLdd = "";


          tableHTMLdd+="<option value='"+data['main'][0]['otkl_catid']+"'>"+data['main'][0]['otkl_catname']+" (Текущий)</option>";
          for(i=0;i<data['spisok'].length;i++){
          tableHTMLdd+="<option value='"+data['spisok'][i]['cat_id']+"'>"+data['spisok'][i]['cat_name']+"</option>";
          }
        
        $("#editcategory").html(tableHTMLdd);
      })

    }
    
    function addRest(restid,email,restname,restmng,adress){
       
       $("#addrestbtn").attr('disabled', '');
       $("#addrestbtn").html('Пожалуйста подождите');

       $.get("api.php", {
        
       "type": "addrest",
       "restid": restid,
       "email": email,
       "restname": restname,
       "restmng": restmng,
       "adress": adress

      }, function(data){

        data = JSON.parse(data);
        ////console.log(data);
        if(data['status']=="NO"){
            $("#addrest_message").attr('class', 'alert alert-danger');
          $("#addrest_message").html(data['message']);
          $('#addrestbtn').removeAttr("disabled");
          $("#addrestbtn").html('Добавить');
        }else{
          getRest();
          $('#staticBackdrop').modal('hide')
          $('#addrestbtn').removeAttr("disabled");
          $("#addrestbtn").html('Добавить');
        }

      })

    }

    function startRocc(restid,type){
       
       $("#start").attr('disabled', '');
       $("#start").html('Пожалуйста подождите, идет загрузка');
       sdate = new Date();

       $.get("api.php", {
        
       "type": "startrocc",
       "restid": restid,
       "typerocc": type,
       "date": sdate

      }, function(data){

        data = JSON.parse(data);
        
        /*tableHTML = "";
        tableHTML+="Рокк аудит начат в "+sdate+"";
        tableHTML+="Ресторан: "+restid+"";
        tableHTML+="Тип аудита: "+type+"";*/
        if(data['status']=='OK'){
            window.location.href = '/index.php?menu=editrocc';
        }
        console.log(data);
        if(data['status']=="NO"){
            $("#message").attr('class', 'alert alert-danger');
          $("#message").html(data['message']);
        }
      })

    }

    function editRocc(reportid,value,result){

       $('#loadmodal').modal('show');
       $.get("api.php", {
        
       "type": "editrocc",
       "reportid": reportid,
       "value": value,
       "result": result
      }, function(data){

        data = JSON.parse(data);
        console.log(data);
        if(data['status']=='OK'){
          $('#loadmodal').modal('hide');
            window.location.href = '/index.php?menu=reportall';
        }
        if(data['status']=='NO'){
          $('#loadmodal').modal('hide')
           $("#closemod").show();
          $("#load_message").attr('class', 'alert alert-danger');
          $("#load_message").html(data['message']+' ID:'+data['otklid']+' Текст:'+data['mintext']);
        }
        
        ////console.log(data);
      })

    }


    function proverkaRocc(restid,type){
       
       $("#start").attr('disabled', '');
       $("#start").html('Пожалуйста подождите, идет загрузка');

       $.get("api.php", {
        
       "type": "proverka",
       "restid": restid

      }, function(data){

        data = JSON.parse(data);
        
        if(data['count']==0){
          startRocc(restid,type);
        }else{
          $("#message").attr('class', 'alert alert-danger');
          $("#message").html('По выбранному ресторану у вас есть незавершенные аудиты. <a href="/index.php?menu=reportall">Все аудиты</a>');
          /*$('.alert').alert('show')
          $("#mess").html('По выбранному ресторану у вас есть незавершенные аудиты. <a href="/index.php?menu=reportall">Все аудиты</a>');*/
          
          $('#start').removeAttr("disabled");
          $("#start").html('Попробовать еще раз');
        }
        /*tableHTML = "";
        tableHTML+="Рокк аудит начат в "+sdate+"";
        tableHTML+="Ресторан: "+restid+"";
        tableHTML+="Тип аудита: "+type+"";

        ////console.log(data);*/
      })

    }