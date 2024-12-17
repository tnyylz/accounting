<?php 
include("sidebar.php");
include("navbar.php");


if (isset($_GET["bayi_id"])) {
    $bayi_id = $_GET["bayi_id"];

    // Seçilen tarihi almak için GET parametresi kullan
    $selected_date = isset($_GET['tarih']) ? $_GET['tarih'] : date('Y-m-d');

    // Sayfalama ayarları
    $records_per_page = 7; // Sayfa başına kayıt sayısı
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Mevcut sayfa
    $offset = ($page - 1) * $records_per_page; // Başlangıç kaydı

    // Seçilen gün için toplam kayıt sayısını öğren
    $total_query = "SELECT COUNT(*) AS total_records 
                    FROM urun 
                    LEFT JOIN dagitim_kayitlari 
                    ON urun.urun_id = dagitim_kayitlari.urun_id 
                    WHERE dagitim_kayitlari.bayi_id = '$bayi_id' AND DATE(dagitim_kayitlari.tarih) = '$selected_date'";
    $total_result = mysqli_query($conn, $total_query);
    $total_records = mysqli_fetch_assoc($total_result)['total_records'];
    $total_pages = ceil($total_records / $records_per_page);

    // Seçilen gün için verileri tarihe göre sıralayıp sayfalama uygula
    $query = "SELECT *, 
                     (dagitim_kayitlari.satilan_miktar * urun.fiyat - dagitim_kayitlari.iade_miktar * urun.fiyat) AS genel_kar
              FROM urun 
              LEFT JOIN dagitim_kayitlari 
              ON urun.urun_id = dagitim_kayitlari.urun_id 
              WHERE dagitim_kayitlari.bayi_id = '$bayi_id' AND DATE(dagitim_kayitlari.tarih) = '$selected_date'
              ORDER BY dagitim_kayitlari.tarih DESC 
              LIMIT $records_per_page OFFSET $offset";
    $result = mysqli_query($conn, $query);

    // Seçilen gün için genel toplamı hesapla
    $total_kar_query = "SELECT SUM(dagitim_kayitlari.satilan_miktar * urun.fiyat - dagitim_kayitlari.iade_miktar * urun.fiyat) AS total_kar 
                        FROM urun 
                        LEFT JOIN dagitim_kayitlari 
                        ON urun.urun_id = dagitim_kayitlari.urun_id 
                        WHERE dagitim_kayitlari.bayi_id = '$bayi_id' AND DATE(dagitim_kayitlari.tarih) = '$selected_date'";
    $total_kar_result = mysqli_query($conn, $total_kar_query);
    $total_kar = mysqli_fetch_assoc($total_kar_result)['total_kar'];
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dağıtım Bilgileri</title>
  <style>
    body {
      margin: 0;
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

    .pagination {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      list-style: none;
    }

    .pagination li {
      margin: 0 5px;
    }

    .pagination a {
      color: #007bff;
      padding: 5px 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .pagination a:hover {
      background-color: #007bff;
      color: white;
    }

    .pagination .active {
      background-color: #007bff;
      color: white;
      pointer-events: none;
    }

    .photo-link {
      color: #007bff;
      text-decoration: underline;
      cursor: pointer;
    }

   
    /* Modal kaplama alanı */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: auto; /* İçeriğe göre genişlik */
        max-width: 100%; /* Ekranın tamamını aşmasın */
        padding: 0;
    }

    /* Modal içeriği */
    .modal-content {
        display: inline-block;
        text-align: center;
        padding: 0;
    }

    .modal-content img {
        max-width: 100%; /* Resim genişliği sınırlı */
        height: auto; /* Resmin oranı korunur */
        display: block;
    }

    /* Kapatma butonu */
    .close {
        position: absolute;
        top: -20px;
        right: -20px;
        font-size: 24px;
        font-weight: bold;
        color: #000;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 50%;
        padding: 5px 10px;
        cursor: pointer;
    }

    .close:hover {
        color: red;
    }

    .filter-form {
      margin-bottom: 20px;
      text-align: center;
    }

    .filter-form input {
      padding: 5px;
      font-size: 16px;
    }

    .filter-form button {
      padding: 5px 10px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .filter-form button:hover {
      background-color: #0056b3;
    }
</style>

  
</head>
<body>
  <div class="content">
    <h2>Dağıtım Bilgileri</h2>

    <!-- Tarih Filtreleme Formu -->
    <form class="filter-form" method="GET">
      <input type="hidden" name="bayi_id" value="<?php echo $bayi_id; ?>">
      <label for="tarih">Tarih Seç:</label>
      <input type="date" name="tarih" id="tarih" value="<?php echo $selected_date; ?>" required>
      <button type="submit">Filtrele</button>
    </form>

    <table class="product-table">
      <thead>
        <tr>
          <th>Ürün</th>
          <th>Satış Miktar</th>
          <th>İade Miktar</th>
          <th>Adet Fiyat</th>
          <th>Fatura Tarihi</th>
          <th>Fatura</th>
          <th>Genel Tutar</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_array($result)) { ?>
          <tr>
            <td><?php echo $row['urun_adi']; ?></td>
            <td><?php echo $row['satilan_miktar']; ?></td>
            <td><?php echo $row['iade_miktar']; ?></td>
            <td><?php echo $row['fiyat']; ?></td>
            <td><?php echo $row['tarih']; ?></td>
            <td>
              <?php if (!empty($row['image_url'])) { ?>
                <span class="photo-link" onclick="showModal('../../img/<?php echo $row['image_url']; ?>')">Faturayı Gör</span>
              <?php } else { ?>
                Resim Yok
              <?php } ?>
            </td>
            <td><?php echo number_format($row['genel_kar'], 2); ?> TL</td>
          </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6" style="text-align: right;"><strong>Genel Toplam:</strong></td>
          <td><?php echo number_format($total_kar, 2); ?> TL</td>
        </tr>
      </tfoot>
    </table>

    <!-- Sayfa Numaraları -->
    <ul class="pagination">
      <?php if ($page > 1) { ?>
        <li><a href="?bayi_id=<?php echo $bayi_id; ?>&tarih=<?php echo $selected_date; ?>&page=<?php echo $page - 1; ?>">Önceki</a></li>
      <?php } ?>
      <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
        <li><a class="<?php echo ($i == $page) ? 'active' : ''; ?>" href="?bayi_id=<?php echo $bayi_id; ?>&tarih=<?php echo $selected_date; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
      <?php } ?>
      <?php if ($page < $total_pages) { ?>
        <li><a href="?bayi_id=<?php echo $bayi_id; ?>&tarih=<?php echo $selected_date; ?>&page=<?php echo $page + 1; ?>">Sonraki</a></li>
      <?php } ?>
    </ul>
  </div>
<?php include("footer.php"); ?>
  <!-- Modal -->
  <div id="photoModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <img id="modalImage" src="" alt="Fatura Görüntüsü">
    </div>
  </div>

  <script>
    function showModal(imageUrl) {
      const modal = document.getElementById('photoModal');
      const modalImage = document.getElementById('modalImage');
      modalImage.src = imageUrl;
      modal.style.display = 'block';
    }

    function closeModal() {
      const modal = document.getElementById('photoModal');
      modal.style.display = 'none';
    }
  </script>
  <script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>