<?php
include("auth.php");
require "../../login-signup/connect.php";
if (isset($_SESSION['kullanici_id'])) {
  $id = $_SESSION['kullanici_id'];
  $users = mysqli_query($conn,"SELECT * FROM kullanici WHERE kullanici_id = '$id' ");
  $row = mysqli_fetch_array($users);
} else {
  echo "Kullanıcı Bulunamadı";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Muhasebe Uygulaması
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .dropdown-toggle::after {
  display: none; /* Varsayılan okun görünmesini engeller */
}
  li {
    list-style-type: none;
  }

  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href="" target="_blank">
        <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="ms-1 text-sm text-dark"><?php echo $row['name'] ?></span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <!-- Şoförlerin sidebarı -->

        <?php 
        
        if ($_SESSION['role']== 0) { 
          
          $sofor = mysqli_query($conn,"SELECT * FROM sofor WHERE sofor.kullanici_id = '$id' ");
          $asd = mysqli_fetch_array($sofor);
          $sofor_id = $asd['sofor_id'];
          $_SESSION['sofor_id'] = $sofor_id;
          $result = mysqli_query($conn, "
              SELECT bayi.*
              FROM bayi
              INNER JOIN guzergah ON guzergah.guzergah_id = bayi.guzergah_id
              INNER JOIN sofor ON sofor.guzergah_id = guzergah.guzergah_id
              WHERE sofor.sofor_id = '$sofor_id'");
          while ($row = mysqli_fetch_array($result)) { 
          ?>
            <li class="nav-item">
            <a class="nav-link text-dark" href="../pages/fatura_ekleme.php?bayi_id=<?= $row['bayi_id']; ?>">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1"><?php echo $row['bayi_adi']; ?></span>
          </a>
        </li>
        <?php 
            }
          }
          else { ?>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/charts.php">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Grafikler</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/drivers.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Şoförler</span>
          </a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/virtual-reality.php">
            <i class="material-symbols-rounded opacity-5">view_in_ar</i>
            <span class="nav-link-text ms-1">Virtual Reality</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/rtl.php">
            <i class="material-symbols-rounded opacity-5">format_textdirection_r_to_l</i>
            <span class="nav-link-text ms-1">RTL</span>
          </a>
        </li> -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-dark d-flex align-items-center" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="material-symbols-rounded opacity-5">notifications</i>
              <span class="nav-link-text mx-1">Güzergahlar</span>
              <i class="material-symbols-rounded ms-2">expand_more</i> <!-- Elle Eklenmiş Aşağı Ok -->
          </a>

          <ul class="dropdown-menu" aria-labelledby="notificationsDropdown">
            <?php 
              $bayi = mysqli_query($conn, 'SELECT * FROM guzergah');
              while ($row = mysqli_fetch_array($bayi)) {  
                $guzergah_id = $row['guzergah_id'];
            ?>
              <li>
                <!-- PHP değişkenini HTML içinde doğru şekilde birleştirme -->
                <a class="dropdown-item d-flex align-items-center" href="marketler.php?guzergah_id=<?php echo $guzergah_id; ?>">
                  <span><?php echo $row['guzergah_adi']; ?></span>
                </a>
              </li>
            <?php } 
              }?>
          </ul>




        <li class="nav-item mt-4">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/profile.php">
            <i class="material-symbols-rounded opacity-5">person</i>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>

        <?php
            if (isset($_SESSION["login"])) {?>

            <li class="nav-item my-3">  
              <form action="../../login-signup/logout.php" method="POST">
              <button class="nav-link  btn  bg-danger mb-0 toast-btn" type="submit" name="logout" >
                Çıkış Yap</button>
              </form>
            </li>
            
            <?php
            }
        else {
        ?>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../../login-signup/login.php">
            <i class="material-symbols-rounded opacity-5">login</i>
            <span class="nav-link-text ms-1">Sign In</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../../login-signup/signup.php">
            <i class="material-symbols-rounded opacity-5">assignment</i>
            <span class="nav-link-text ms-1">Sign Up</span>
          </a>
        </li>
        <li class="nav-item">  
              <form action="../../login-signup/logout.php" method="POST">
                <button class="nav-link text-dark" name="logout"></button>
                <i class="material-symbols-rounded opacity-5">login</i>
                <span class="nav-link-text ms-1">Çıkış Yap</span>
              </form>
            </li>
      </ul>
      <?php } ?>
    </div>
    
  </aside>
