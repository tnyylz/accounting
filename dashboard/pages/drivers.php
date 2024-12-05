<?php 
  include("sidebar.php");


  if (isset($_POST["upload-info"]) && isset($_FILES['image'])) {
    $satis_beyaz = $_POST["satis_beyaz"];
    $iade_beyaz = $_POST["iade_beyaz"];

    $satis_kepek = $_POST["satis_kepek"];
    $iade_kepek = $_POST["iade_kepek"];

    $satis_karafirin = $_POST["satis_karafirin"];
    $iade_karafirin = $_POST["iade_karafirin"];

    $satis_cavdar = $_POST["satis_cavdar"];
    $iade_cavdar = $_POST["iade_cavdar"];    

    
    $satis_trabzon = $_POST["satis_trabzon"];
    $iade_trabzon = $_POST["iade_trabzon"];

    

    $img_name = $_FILES["image"]["name"];
    $img_size = $_FILES["image"]["size"];
    $tmp_name = $_FILES["image"]["tmp_name"];
    $error = $_FILES["image"]["error"];


    if ($error === 0) {
        if ($img_size > 10000000) {
            $_SESSION['message'] = 'Fotoğrafın boyutu çok yüksek!.';
            header("Location: add-card.php");
            exit(0);
        }
        else {
            $img_ex = pathinfo($img_name,PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed_exs = array("jpg","jpeg","png");

            if (in_array($img_ex_lc,$allowed_exs)){
                $new_img_name = uniqid("IMG-",true).'.'.$img_ex_lc;
                $img_upload_path = '../img/'.$new_img_name;
                move_uploaded_file($tmp_name, $img_upload_path);
            }else{
                $_SESSION['message'] = 'Yanlış Dosya Türü!';
                header('Location: add-card.php');
                exit(0);
            }
        }
        
    }
    else{
        $_SESSION['message'] = 'Bilinmeyen Bir hata oluştu.';
        header('Location: add-card.php');
        exit(0);
    }



     $query = "INSERT INTO dagitim_kayitlari (satilan_miktar,iade_miktar,image_url) VALUES ('$title','$description','$new_img_name','$url')";
     $query_run = mysqli_query($conn, $query);
     if ($query_run){
         $_SESSION['message'] = "Kart Başarılı bir şekilde eklenmiştir.";
         header("Location:view_cards.php");
         exit(0);
     }
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Market Form</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 20px;
    }

    .content {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
      background-color: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      font-size: 14px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .photo-upload {
      border: 2px dashed #ddd;
      height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      font-size: 18px;
      color: #666;
      cursor: pointer;
      border-radius: 4px;
    }

    .btn {
      background-color: #007bff;
      color: white;
      padding: 10px 15px;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .product-table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
      margin-top: 20px;
    }

    .product-table th,
    .product-table td {
      border: 1px solid #ddd;
      padding: 10px;
    }

    .product-table th {
      background-color: #f4f4f4;
    }
  </style>
</head>

<body>
  
  <div class="content">
    <form action="drivers.php" method="POST" enctype="multipart/form-data">
     
      <div class="photo-upload" onclick="document.getElementById('file').click()">
        Fotoğraf Yükle (Tıklayın)
        <input type="file" class="form-control" name="image" id="file" style="display: none;" required>
      </div>

      <!-- Ürün Giriş Formu -->
      <table class="product-table">
        <thead>
          <tr>
            <th>Ürün</th>
            <th>Satış Miktar</th>
            <th>İade Miktar</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>A Ürün</td>
            <td><input type="number" name="satis_beyaz" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_beyaz" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <tr>
            <td>B Ürün</td>
            <td><input type="number" name="satis_kepek" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_kepek" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <tr>
            <td>C Ürün</td>
            <td><input type="number" name="satis_karafirin" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_karafirin" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <tr>
            <td>D Ürünü</td>
            <td><input type="number" name="satis_cavdar" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_cavdar" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <tr>
            <td>E Ürünü</td>
            <td><input type="number" name="satis_trabzon" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_trabzon" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <tr>
            <td>Toplam Fiyat</td>
            <td>Fiyat gelecek</td>
          </tr>
        </tbody>
      </table>

      <div class="form-group" style="margin-top: 20px;">
        <button type="submit" name="upload_info" class="btn">Kaydet</button>
      </div>
    </form>
  </div>
</body>

</html>
