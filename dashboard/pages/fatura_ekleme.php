<?php
include("sidebar.php");

if (isset($_GET["bayi_id"])) {
  $bayi_id = $_GET['bayi_id'];
  $query = "SELECT * FROM urun INNER JOIN dagitim_kayitlari on dagitim_kayitlari.urun_id = urun.urun_id INNER JOIN bayi on dagitim_kayitlari.bayi_id = '$bayi_id' GROUP BY urun_adi";
  $result = mysqli_query($conn, $query);
}


if (isset($_POST["upload-info"]) && isset($_FILES['image'])) {
  $img_name = $_FILES["image"]["name"];
  $img_size = $_FILES["image"]["size"];
  $tmp_name = $_FILES["image"]["tmp_name"];
  $error = $_FILES["image"]["error"];

  if ($error === 0) {
    if ($img_size > 10000000) {
      $_SESSION['message'] = 'Fotoğrafın boyutu çok yüksek!';
      header("Refresh:0");
      exit(0);
    } else {
      $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
      $img_ex_lc = strtolower($img_ex);
      $allowed_exs = array("jpg", "jpeg", "png");

      if (in_array($img_ex_lc, $allowed_exs)) {
        $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
        $img_upload_path = '../../img/' . $new_img_name;
        move_uploaded_file($tmp_name, $img_upload_path);
      } else {
        $_SESSION['message'] = 'Yanlış Dosya Türü!';
        header("Refresh:0");
        exit(0);
      }
    }
  } else {
    $_SESSION['message'] = 'Bilinmeyen Bir hata oluştu.';
    header("Refresh:0");
    exit(0);
  }

  // Resim yüklendikten sonra ürünleri ekleyelim
  while ($row = mysqli_fetch_array($result)) {
    // Satılan ve iade miktarlarını almak
    $satilan_miktar = $_POST["satis_" . $row['urun_id']];
    $iade_miktar = $_POST["iade_" . $row['urun_id']];

    // `dagitim_kayitlari` tablosuna veri ekleme
    $query1 = "INSERT INTO dagitim_kayitlari (bayi_id, urun_id, satilan_miktar, iade_miktar, image_url) 
              VALUES ('$bayi_id', '{$row['urun_id']}', '$satilan_miktar', '$iade_miktar', '$new_img_name')";
    
    // SQL sorgusunu çalıştır ve hata kontrolü yap
    $query_run = mysqli_query($conn, $query1);

    if (!$query_run) {
      // Hata mesajını ekrana yazdır
      $_SESSION['message'] = "Veri ekleme sırasında bir hata oluştu: " . mysqli_error($conn);
      header("Refresh:0");
      exit(0);
    }
  }

  $_SESSION['message'] = "Kart Başarılı bir şekilde eklenmiştir.";
  header("Refresh:0");
  exit(0);
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
    .product-table th, .product-table td {
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
    <form action="fatura_ekleme.php" method="POST" enctype="multipart/form-data">
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
          <?php
            while ($row = mysqli_fetch_array($result)) {
          ?>
          <tr>
            <td><?php echo $row['urun_adi']; ?></td>
            <td><input type="number" name="satis_<?php echo $row['urun_id']; ?>" class="form-control" min="0" placeholder="Miktar" required></td>
            <td><input type="number" name="iade_<?php echo $row['urun_id']; ?>" class="form-control" min="0" placeholder="Miktar" required></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

      <div class="form-group" style="margin-top: 20px;">
        <button type="submit" name="upload-info" class="btn">Kaydet</button>
      </div>
    </form>
  </div>
</body>
</html>
