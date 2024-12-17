<?php
session_start();
require "connect.php";
if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $result = mysqli_query($conn, "SELECT * FROM kullanici WHERE username = '$username'");

  $row = mysqli_fetch_assoc($result);
  if (mysqli_num_rows($result) > 0) {
    if ($password == $row['password']) {
      $_SESSION['role'] = $row['role'];
      $_SESSION["login"] = true;
      $_SESSION["username"] = $username;
      $_SESSION['kullanici_id'] = $row['kullanici_id'];
      $_SESSION['name'] = $row['name'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['password'] = $row['password'];


      $_SESSION['lastname'] = $row['lastname'];


      // $_SESSION['register_time'] = $row['register_time'];

      if ($row['role'] == '0') { // şoför
        $_SESSION['message'] = "Hoşgeldin $username";
        header('Location: ../dashboard/pages/profile.php');
        exit(0);
      } elseif ($row['role'] == '1') {  //muhasebeci
        $_SESSION['message'] = "Hoşgeldin $username";
        header('Location: ../dashboard/pages/profile.php?id=' . $row["kullanici_id"]);
        exit(0);
      }
    } else {
      $_SESSION['message'] = "Yanlış Şifre";
      header("Refresh:0");
      exit(0);
    }
  } else {
    $_SESSION['message'] = "Kaydınız Bulunmamaktadır";
    header("Refresh:0");

    exit(0);
  }
}
?>

<!doctype html>
<html lang="tr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

  <link rel="stylesheet" href="../css/log.css?v=<?php echo time(); ?>">

  <title>Giriş Yap</title>
  
</head>

<body class="">
  
  <section class="">
    <div style="width: 400px;
    height: 450px;" class="form-box">
      <div class="form-value">
        <?php include("message.php"); ?>
        <form action="" method="post">
          <h2>Giriş Yap</h2>
          <div class="inputbox">
            <ion-icon name="person-outline"></ion-icon>
            <input type="text" name="username" id="username" required>
            <label for="username">Kullanıcı Adı</label>
          </div>
          <div class="inputbox">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="password" id="password" required>
            <label for="password">Şifre</label>
          </div>
          <button name="login" type="submit">Giriş Yapın</button>
          <div class="register">
            <p>Hesabınız Mı Yok <a href="signup.php">Kayıt Ol</a></p>
          </div>
        </form>
      </div>
    </div>
  </section>


  <?php
  if (isset($_SESSION["login"])) {
    $_SESSION['message'] = "Zaten giriş yapılmış durumda!";
    header("Location: ../dashboard/pages/profile.php");
    exit(0);
  }
  ?>


  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>