<?php 
  include("sidebar.php");

  if (isset($_GET["bayi_id"])) {
    $bayi_id = $_GET["bayi_id"];
  
  $query = "SELECT * FROM urun LEFT JOIN dagitim_kayitlari on urun.urun_id = dagitim_kayitlari.urun_id WHERE dagitim_kayitlari.bayi_id = '$bayi_id'";
  $result = mysqli_query($conn, $query);
 }
?>

<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veri Görüntüleme</title>
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
    <h2>Dağıtım Bilgileri</h2>
    <table class="product-table">
      <thead>
        <tr>
          <th>Ürün</th>
          <th>Satış Miktar</th>
          <th>İade Miktar</th>
          <th>Adet Fiyat</th>
          <th>Fatura</th>

        </tr>
      </thead>
      <tbody>
        <?php
          // Veritabanındaki her kayıttan verileri çekip tabloya yerleştiriyoruz
          while ($row = mysqli_fetch_array($result)) {
        ?>
          <tr>
            <td><?php echo $row['urun_adi']; ?></td>
            <td><?php echo $row['satilan_miktar']; ?></td>
            <td><?php echo $row['iade_miktar']; ?></td>
            <td><?php echo $row['fiyat']; ?></td>
            <td><img src="../../img/<?= $row['image_url']; ?>" class="card-img-top" alt="..."></td>

          </tr>
          <!-- Diğer ürünler için aynı yapıyı kullanabilirsiniz -->
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>

</html>
