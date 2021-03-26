 <?php
require_once 'oop.php';
require_once 'libs/img.php';
$imgObject = new IMG();
session_start();
define ("MAX_SIZE","9000"); 
function getExtension($str)
{
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
}

$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
setcookie('photo', 'yes');
if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") 
{
  
    $uploaddir = "uploads/"; //a directory inside
    foreach ($_FILES['photos']['name'] as $name => $value)
    {
  
        $filename = stripslashes($_FILES['photos']['name'][$name]);
        $size=filesize($_FILES['photos']['tmp_name'][$name]);
        //get the extension of the file in a lower case format
          $ext = getExtension($filename);
          $ext = strtolower($ext);
      
         if(in_array($ext,$valid_formats))
         {
         if ($size < (MAX_SIZE*1024))
         {
       $image_name=time().$filename;
       echo "<img src='".$uploaddir."small_".$image_name."' class='imgList'>";
       $newname=$uploaddir.$image_name;
       $newnameSmall=$uploaddir."small_".$image_name;
           $bb = "small_".$image_name;
           if (move_uploaded_file($_FILES['photos']['tmp_name'][$name], $newname)) 
           {
         $time=time();
         global $mysqli;
         global $datea;
         $add = addOtkl("","","9","9",$_POST['restid'],$_POST['reportid'],$bb,$image_name);
         //$reportid = $_POST['reportid'];
         //$otklid = $add['id'];
         //$res=$mysqli->query("INSERT INTO `otklimg` (`id`, `img`, `otklid`, `reportid`, `createtime`) VALUES (NULL, '$bb', '$otklid', '$reportid', '$datea')");
         //mysql_query("INSERT INTO otklimg(img,otklid,reportid,createtime VALUES('$bb','5','24','2021-03-04 14:47:32')");
        
        $imgObject->resizeProportional($newname, $newnameSmall, 600, 600, 100);
         }
         else
         {
          echo '<span class="imgList">Вы превысили лимит размера!  </span>';
            }

         }
       else
       {
      echo '<span class="imgList">Вы превысили лимит размера!</span>';
          
         }
       
          }
          else
         { 
        echo '<span class="imgList">Неподдерживаемый формат рисунка</span>';
           
       }
           
     }
}

?>