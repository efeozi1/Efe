<?php
//session başlatılır
session_start();

//sunucu bağlantısı ve veritabanı seçimi
require_once '../../includes/connection.php';

//form fonksiyonları 
require_once '../../includes/functions.php';

     //giriş yapılmışmı kontrol ediliyor
     if(!GirisVarmi()){
            header("Location:../index.php?Hata=GirisYap");
        }
        
        //giriş yapılmış ise
      $uyeID = $_SESSION['Uye']['UyeID'];
        $modulID = 11;
        $alan = 'Duzenle';
             if(!yetkiVarmi($_SESSION['Uye']['SeviyeID'],4,$uyeID,$modulID,$alan)){
            header("Location:../index.php?Hata=YetkisizGiris");      
            }
            
            //$modulID değeri alınacak
            $modulID = getValues($_GET['ModulID']);
            //echo "Düzenleme yapılacak Modül ID değeri : " . $modulID . "<br>";
            
            //$modulkayıt seti
            $query_rsModul = "SELECT * FROM modul WHERE ModulID = '$modulID'";
            $rsModul = mysql_query($query_rsModul);
            $row_rsModul = mysql_fetch_object($rsModul);
           
            
            if(isset($_POST['modulDuzenleSubmit']))
{
              /* echo "Form Gönderildi";
                
                echo "<pre>";
                print_r($_POST);
                print_r($_FILES);
                
                echo "</pre>";
                exit();
                
      
     [ModulAdi] => ÃœrÃ¼n ModÃ¼lÃ¼
    [ModulDizin] => urun
    [ModulSeviye] => 3
    [ModulSira] => 1
    [ModulAktif] => on
    [ModulResim] => Array
            [name] => _kullanici.png
            [tmp_name] => /tmp/phpquu1sP */
                
                      //form verilerinin alınması
                $modulAdi= postValues($_POST['ModulAdi']);
                $modulDizin  = postValues($_POST['ModulDizin']);
                $modulSeviye = postValues($_POST['ModulSeviye']);
                $modulSira = postValues($_POST['ModulSira']);
                $modulAktif = isset($_POST['ModulAktif'])?1:0;
                $modulResim = $_FILES['ModulResim']['name'];
                $modulResimName = empty($modulResim)?$row_rsModul->ModulResim:$modulResim;
                
                
                //echo $modulResimName;
               // echo "Modül Resim Değeri : " .$modulResimName;
          
            //query oluşturma
            $query_ModulDuzenle = "UPDATE modul SET
            ModulAdi = '$modulAdi',
            ModulDizin = '$modulDizin',
            ModulSeviye = '$modulSeviye',
            ModulSira = '$modulSira',
            ModulAktif = '$modulAktif',
            ModulResim =    '$modulResimName'
            WHERE ModulID = '$modulID' ";
            
         //  echo $query_ModulDuzenle;
            
            //query işleme
            $query_Sonuc=mysql_query($query_ModulDuzenle);
            
            
            //resim yükleme, resim silme 
            if($query_Sonuc){
                
                if(!empty($_FILES['ModulResim']['name'])){
                    
                    //echo "resim yüklenecek";
                    
                    $filename = $_FILES['ModulResim']['tmp_name'];
                    $destination = "../../uploads/modul/".$modulResimName;
                    
                    $silinecekResim  = "../../uploads/modul/" .$row_rsModul->ModulResim;
                    unlink($silinecekResim);

                    move_uploaded_file($filename, $destination);
                                    
                header("Location:index.php?Islem=ResimDuzenle");

                    
                    
                }else{
                    
                  header("Location:index.php?Islem=ResimDuzenle");
                    
                }                    
                
                
            }//query başarılı
            
            
            
       
            
                    }//form gönderildiğinde işlemleri sonu

                         $uyeSeviyeID = $_SESSION['Uye']['SeviyeID'];
                    //modül kayıt seti
        $query_rsModulBar = "SELECT ModulAdi,ModulDizin,ModulResim FROM modul WHERE ModulAktif=1 AND ModulSeviye >= $uyeSeviyeID  AND ModulID!=1 AND ModulID!=17  AND ModulID IN (SELECT ModulID FROM modul_uye WHERE UyeID='$uyeID') ORDER BY ModulSira ASC";
     
       // echo $query_rsModul;
        $rsModulBar = mysql_query($query_rsModulBar);
        $row_rsModulBar = mysql_fetch_object($rsModulBar);
        $num_row_rsModulBar = mysql_num_rows($rsModulBar);
                    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Modül Düzenle</title>
        <link href="../../includes/css/form.css" type="text/css" rel="stylesheet" />
                <link href="../../includes/css/yonetim.css"  type="text/css" rel="stylesheet" />

    </head>
    <body>        <div id="yonetimToolbar">
        <ul>
            <li><a hreF="../index.php"><img src="../../includes/img/anasayfa.png" />Anasayfa</a></li>
            <?php do { ?>
            <li><a href="../<?= $row_rsModulBar->ModulDizin;?>"><img src="../../uploads/modul/<?=$row_rsModulBar->ModulResim;?>"  width="24"/><?= $row_rsModulBar->ModulAdi;?></a></li>
            <?php } while($row_rsModulBar= mysql_fetch_object($rsModulBar)); ?>
                <li><a hreF="../../index.php"><img src="../../includes/img/logout.png" />Çıkış</a></li>

        </ul>
    </div>
        <?php
        // put your code here
        ?>
        <h1>Modül Düzenle</h1>
        <form action="<?= phpSelf(); ?>?ModulID=<?= $modulID;?>" method="post" enctype="multipart/form-data">
        
            <fieldset>
                <legend>Modül Bilgileri</legend>
                <label for="ModulAdi">Modül Adı </label>
                <input type="text" name="ModulAdi" id="ModulAdi" value="<?=$row_rsModul->ModulAdi;?>" required/>
                
                <label for="ModulDizin">Modül Dizin</label>
                <input type="text" name="ModulDizin" id="ModulDizin" value="<?=$row_rsModul->ModulDizin;?>" required />
                <p><img src="../../uploads/modul/<?=$row_rsModul->ModulResim;?>" height="75"/></p>
                <label for="ModulResim" >Yeni Modül Resmi</label> 
                <input type="file" name="ModulResim" id="ModulResim" />
            </fieldset>
            
            <fieldset>
                <legend>Sıra Aktiflik ve Seviye</legend>
                <label for="ModulSeviye">Modül Seviye</label>
                <input type="text" name="ModulSeviye" id="ModulSeviye" value="<?= $row_rsModul->ModulSeviye;?>" />
                
                <label for="ModulSira" >Modül Sıra</label>
                <input type="text" name="ModulSira" id="ModulSira" value="<?= $row_rsModul->ModulSira;?>" />
                
                <label for="ModulAktif" >Modül Aktif mi ?</label>
                <input type="checkbox" name="ModulAktif" id="ModulAktif" <?= $modulAktifmi = $row_rsModul->ModulAktif==1?' checked ':'';?> />
                
            </fieldset>
            
        
            <p><input type="submit" name="modulDuzenleSubmit" value="Değişiklikleri Kaydet " /></p>
        
        </form>
        
    </body>
</html>
