    $(document).ready(function(){
    getMon();
    getLastOtkl();

    });

    function getMon(){

      $.get("api.php", {
        
       "type": "getmon"

      }, function(data){

        data = JSON.parse(data);
        console.log(data);

        tableHTML = "";
        status = "";
        for(i=0;i<data.length;i++){
            if(data[i]['server_statusid']=="start"){
        	status = "В обработке";
        }
        if(data[i]['server_statusid']=="final"){
        	status = "Отчет доставлен";
        }
          tableHTML+="<tr>";
          tableHTML+="<td>"+data[i]['rest_name']+"</td>";
          tableHTML+="<td>"+data[i]['auditor_name']+"</td>";
          tableHTML+="<td>"+data[i]['rocc_result']+"</td>";
          tableHTML+="<td>"+data[i]['server_datea']+"</td>";
          tableHTML+="<td>"+status+"</td>";
          tableHTML+="<td>"+data[i]['server_finaltime']+"</td>";
          tableHTML+="</tr>";
        }

        $("#monresult").html(tableHTML);
      })

    }
    
 function getLastOtkl(){

      $.get("api.php", {
        
       "type": "getlastotkl"

      }, function(data){

        data = JSON.parse(data);
        console.log(data);

        tableHTML = "";
        for(i=0;i<data.length;i++){
            
          tableHTML+="<li class='nav-item'>";
          tableHTML+="<a class=nav-'link' href='/admin.php?menu=viewotkl&id="+data[i]['otkl_id']+"'>";
          tableHTML+="<span data-feather='file-text'></span>"+data[i]['otkl_mintext'];
          tableHTML+="</a>";
          tableHTML+="</li>";
        }

        $("#lastotkl").html(tableHTML);
      })

    }