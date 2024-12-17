<?php
include("sidebar.php");
$_SESSION['baslik'] = "Profil";

if (isset($_POST["update_profile"])) {
    // Kullanıcıdan gelen verileri al
    $kullanici_id = $_SESSION["kullanici_id"];
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];


    $duplicate = mysqli_query($conn, "SELECT * FROM kullanici WHERE username = '$username' ");
  
    $query = "UPDATE kullanici SET name = ?, lastname = ?, email = ?, password = ?, username = ? WHERE kullanici_id = ?";
    
    // Sorguyu hazırlayın
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Parametreleri bağlayın
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $lastname, $email, $password,$username, $kullanici_id);

        // Sorguyu çalıştırın
        if (mysqli_stmt_execute($stmt)) {
          $_SESSION['message'] = "Profil başarıyla güncellendi!";
        } else {
          $_SESSION['message'] = "Bir hata oluştu. Lütfen tekrar deneyin.";
        }

        // Sorguyu kapatın
        mysqli_stmt_close($stmt);
    } else {
        echo "Sorgu hazırlama hatası.";
    }
}

?>


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <?php include("navbar.php"); ?>
    
    <div class="container-fluid px-2 px-md-4">
        <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('../../img/bg2.jpeg');">
            <span class="mask bg-gradient-dark opacity-6"></span>
        </div>
        <?php 
        $kullanici_id = $_SESSION["kullanici_id"];
        $users = mysqli_query($conn,"SELECT * FROM kullanici WHERE kullanici_id = '$kullanici_id' ");
        $row = mysqli_fetch_array($users);
        
        ?>
        <div class="card card-body mx-2 mx-md-2 mt-n6">
            <div class="row gx-4 mb-2">                   
                <div class="col-auto my-auto">
                    <div class="h-100">
                    <h5 class="mb-1">
                        <?php echo ucfirst($row['name'])." ".ucfirst($row['lastname']); ?>
                    </h5>
                        <p class="mb-0 font-weight-normal text-sm">
                            <?php 
                                if ($row['role'] == 1) {
                                    echo 'Muhasebeci';
                                } else if ($row['role'] == 2) {
                                    echo 'Muhasebeci';
                                } else {
                                    echo 'Şoför';
                                }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-md-8 d-flex align-items-center">
                                    <h6 class="mb-0">Profili Düzenle</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">İsim</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Soyisim</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Kullanıcı Adı</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" class="form-control" id="password" name="password" value="<?php echo $row['password']; ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-info">Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="row">
                                <div class="col-md-8 d-flex align-items-center">
                                    <h6 class="mb-0">Hesap Ayarları</h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Yetki: <?php 
                                        if ($row['role'] == 1) { echo 'Muhasebeci'; }
                                        else if ($row['role'] == 2) { echo 'Muhasebeci'; }
                                        else { echo 'Şoför'; }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "footer.php"; ?>
</main>

<!-- Core JS Files -->
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>
