<?php
ob_start();
include("sidebar.php");

if (isset($_POST['upload-info'])) {
    // Fotoğraf yükleme işlemi
    if (isset($_FILES['image'])) {
        $img_name = $_FILES['image']['name'];
        $img_size = $_FILES['image']['size'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $error = $_FILES['image']['error'];

        if ($error === 0) {
            if ($img_size > 10000000) {
                $_SESSION['message'] = 'Fotoğrafın boyutu çok yüksek!';
                header("Location: fatura_ekleme.php");
                exit();
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
                    header("Location: fatura_ekleme.php");
                    exit();
                }
            }
        } else {
            $_SESSION['message'] = 'Bilinmeyen Bir hata oluştu.';
            header("Location: fatura_ekleme.php");
            exit();
        }
    }

        $bayi_id = $_GET['bayi_id'];

        if (isset($_POST['upload-info'])) {
            $tarih = date('Y-m-d'); // Güncel tarih
        
            // Tüm ürünleri listele
            $query = "SELECT * FROM urun";
            $result_urun = mysqli_query($conn, $query);
        
            if (!$result_urun) {
                $_SESSION['message'] = 'Ürünler alınırken hata oluştu: ' . mysqli_error($conn);
                header("Location: fatura_ekleme.php");
                exit();
            }
        
            while ($row = mysqli_fetch_assoc($result_urun)) {
                $urun_id = $row['urun_id'];
                $satilan_miktar = isset($_POST["satis_$urun_id"]) ? intval($_POST["satis_$urun_id"]) : 0;
                $iade_miktar = isset($_POST["iade_$urun_id"]) ? intval($_POST["iade_$urun_id"]) : 0;
        
                // Eğer satış ve iade miktarları sıfır ise atla
                if ($satilan_miktar == 0 && $iade_miktar == 0) {
                    continue;
                }
        
                // İlk kayıt kontrolü: Dağıtım kayıtlarında mevcut mu?
                $kontrol_query = "SELECT * FROM dagitim_kayitlari WHERE bayi_id = ? AND urun_id = ?";
                $stmt_kontrol = $conn->prepare($kontrol_query);
                $stmt_kontrol->bind_param("ii", $bayi_id, $urun_id);
                $stmt_kontrol->execute();
                $result_kontrol = $stmt_kontrol->get_result();
        
                // Kayıt varsa güncelleme yap
                if ($result_kontrol->num_rows > 0) {
                    $stmt = $conn->prepare("CALL ekle_dagitim_kayitlari(?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiiisis", $bayi_id, $urun_id, $satilan_miktar, $iade_miktar, $new_img_name, $_SESSION['sofor_id'], $tarih);
                } else {
                    // Kayıt yoksa ekleme yap
                    $stmt = $conn->prepare("CALL ekle_dagitim_kayitlari(?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iiiisis", $bayi_id, $urun_id, $satilan_miktar, $iade_miktar, $new_img_name, $_SESSION['sofor_id'], $tarih);
                }
        
                if (!$stmt->execute()) {
                    $_SESSION['message'] = 'Veri eklerken hata oluştu: ' . $stmt->error;
                    header("Location: fatura_ekleme.php");
                    exit();
                }
        
                $stmt->close();
                $stmt_kontrol->close();
            }
        
            $_SESSION['message'] = "Tüm veriler başarıyla kaydedildi.";
            header("Location: fatura_ekleme.php");
            exit();
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
        /* CSS stil kısmı */
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
        <form action="" method="POST" enctype="multipart/form-data">
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
                    if (isset($_GET["bayi_id"])) {
                        $bayi_id = $_GET['bayi_id'];
                        $query = "SELECT urun_id, urun_adi 
                            FROM urun 
                            ORDER BY urun_adi;
                            ;
                            ";
                        $result_urun = mysqli_query($conn, $query);

                        if (!$result_urun) {
                            $_SESSION['message'] = 'Veri alınırken bir hata oluştu: ' . mysqli_error($conn);
                            header("Location: fatura_ekleme.php");
                            exit();
                        }

                        // Verileri diziye alıyoruz
                        $urunler = [];
                        while ($row = mysqli_fetch_array($result_urun)) {
                            $urunler[] = $row; // Her bir ürün satırını diziye ekliyoruz
                        }

                        // Dizi üzerinden döngü ile her ürünü işliyoruz
                        foreach ($urunler as $row) {
                    ?>
                    <tr>
                        <td><?php echo $row['urun_adi']; ?></td>
                        <td><input type="number" name="satis_<?php echo $row['urun_id']; ?>" class="form-control" min="0" placeholder="Miktar" value="<?php echo $row['satilan_miktar'] ?? 0; ?>" required></td>
                        <td><input type="number" name="iade_<?php echo $row['urun_id']; ?>" class="form-control" min="0" placeholder="Miktar" value="<?php echo $row['iade_miktar'] ?? 0; ?>" required></td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="form-group" style="margin-top: 20px;">
                <button type="submit" name="upload-info" class="btn">Kaydet</button>
            </div>
        </form>
    </div>
</body>
</html>
