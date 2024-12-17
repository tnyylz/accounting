<?php
    include("sidebar.php");
    $_SESSION['baslik'] = "Grafikler";
?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
   
     
    <?php 
      include("navbar.php");
    ?>
    <div class="container-fluid py-2">
    <?php include("../../login-signup/message.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Grafik</title>
</head>
<body>

    <?php
    
    // Aylık Satış ve İade Oranı Verileri
    $monthlySalesReturnQuery = "SELECT MONTH(tarih) as ay, SUM(satilan_miktar) as toplam_satis, SUM(iade_miktar) as toplam_iade FROM dagitim_kayitlari GROUP BY ay ORDER BY ay";
    $monthlySalesReturnResult = $conn->query($monthlySalesReturnQuery);
    $monthlyLabels = [];
    $monthlySales = [];
    $monthlyReturns = [];
    while ($row = $monthlySalesReturnResult->fetch_assoc()) {
        $monthlyLabels[] = $row['ay'] . ".Ay";
        $monthlySales[] = $row['toplam_satis'];
        $monthlyReturns[] = $row['toplam_iade'];
    }

    // Güzergah Listesi
    $routeListQuery = "SELECT DISTINCT guzergah.guzergah_id, guzergah.guzergah_adi FROM guzergah INNER JOIN bayi ON guzergah.guzergah_id = bayi.guzergah_id";
    $routeListResult = $conn->query($routeListQuery);
    $routeList = [];
    while ($row = $routeListResult->fetch_assoc()) {
        $routeList[$row['guzergah_id']] = $row['guzergah_adi'];
    }

    // Güzergahlara Göre Aylık Ürün Satış ve İade Miktarları
    $routeSalesReturnQuery = "SELECT guzergah.guzergah_id as guzergah_id, MONTH(dagitim_kayitlari.tarih) as ay, SUM(dagitim_kayitlari.satilan_miktar) as toplam_satis, SUM(dagitim_kayitlari.iade_miktar) as toplam_iade FROM dagitim_kayitlari INNER JOIN bayi ON dagitim_kayitlari.bayi_id = bayi.bayi_id INNER JOIN guzergah ON bayi.guzergah_id = guzergah.guzergah_id GROUP BY guzergah.guzergah_id, ay ORDER BY guzergah.guzergah_id, ay";
    $routeSalesReturnResult = $conn->query($routeSalesReturnQuery);
    $routeData = [];
    while ($row = $routeSalesReturnResult->fetch_assoc()) {
        $routeData[$row['guzergah_id']]['labels'][] = $row['ay'] . ".Ay";
        $routeData[$row['guzergah_id']]['sales'][] = $row['toplam_satis'];
        $routeData[$row['guzergah_id']]['returns'][] = $row['toplam_iade'];
    }
    
    // Fetch data for each chart
    $bayiPerformanceQuery = "SELECT bayi_id, SUM(satilan_miktar) as toplam_satis, SUM(iade_miktar) as toplam_iade, SUM(tutar) as toplam_tutar FROM dagitim_kayitlari GROUP BY bayi_id";
    $bayiPerformanceResult = $conn->query($bayiPerformanceQuery);
    $bayiLabels = [];
    $bayiSatis = [];
    $bayiIade = [];
    $bayiTutar = [];
    while ($row = $bayiPerformanceResult->fetch_assoc()) {
        $bayiLabels[] = "Bayi " . $row['bayi_id'];
        $bayiSatis[] = $row['toplam_satis'];
        $bayiIade[] = $row['toplam_iade'];
        $bayiTutar[] = $row['toplam_tutar'];
    }

    $driverPerformanceQuery = "SELECT sofor_id, SUM(satilan_miktar) as toplam_satis, SUM(iade_miktar) as toplam_iade FROM dagitim_kayitlari GROUP BY sofor_id";
    $driverPerformanceResult = $conn->query($driverPerformanceQuery);
    $driverLabels = [];
    $driverSatis = [];
    $driverIade = [];
    while ($row = $driverPerformanceResult->fetch_assoc()) {
        $driverLabels[] = "Şoför " . $row['sofor_id'];
        $driverSatis[] = $row['toplam_satis'];
        $driverIade[] = $row['toplam_iade'];
    }

    $productProfitabilityQuery = "SELECT urun_id, SUM(tutar) as toplam_tutar FROM dagitim_kayitlari GROUP BY urun_id";
    $productProfitabilityResult = $conn->query($productProfitabilityQuery);
    $productLabels = [];
    $productTutar = [];
    while ($row = $productProfitabilityResult->fetch_assoc()) {
        $productLabels[] = "Ürün" . $row['urun_id'];
        $productTutar[] = $row['toplam_tutar'];
    }

    // Satış ve İade Oranı Verilerini Ürün Adıyla Çekme
            $salesReturnRatioQuery = "
            SELECT urun.urun_adi, SUM(dagitim_kayitlari.satilan_miktar) as toplam_satis, SUM(dagitim_kayitlari.iade_miktar) as toplam_iade 
            FROM dagitim_kayitlari 
            JOIN urun ON dagitim_kayitlari.urun_id = urun.urun_id
            GROUP BY urun.urun_adi
            ";
            $salesReturnRatioResult = $conn->query($salesReturnRatioQuery);
            $salesReturnLabels = [];
            $salesReturnSatis = [];
            $salesReturnIade = [];
            while ($row = $salesReturnRatioResult->fetch_assoc()) {
            $salesReturnLabels[] = $row['urun_adi']; // Ürün adı
            $salesReturnSatis[] = $row['toplam_satis'];
            $salesReturnIade[] = $row['toplam_iade'];
            }

    $revenueTrendsQuery = "SELECT DATE(tarih) as gun, SUM(tutar) as toplam_tutar FROM dagitim_kayitlari GROUP BY gun ORDER BY gun";
    $revenueTrendsResult = $conn->query($revenueTrendsQuery);
    $revenueTrendsLabels = [];
    $revenueTrendsData = [];
    while ($row = $revenueTrendsResult->fetch_assoc()) {
        $revenueTrendsLabels[] = $row['gun'];
        $revenueTrendsData[] = $row['toplam_tutar'];
    }
    ?>


<!-- Aylık Satış ve İade Oranı -->
<h2>Aylık Satış ve İade Miktarı</h2>
    <canvas id="monthlySalesReturnChart" width="400" height="200"></canvas>
    <p>
        <ul>
            <li>- Bu grafik, aylık bazda satış ve iade miktarlarını karşılaştırır.</li>
            <li>- Aylık performans değişimlerini analiz etmek için kullanılır.</li>
        </ul>
    </p>
    <script>
        const monthlyCtx = document.getElementById('monthlySalesReturnChart').getContext('2d');
        const monthlySalesReturnChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthlyLabels); ?>,
                datasets: [
                    {
                        label: 'Satış Miktarı',
                        data: <?php echo json_encode($monthlySales); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'İade Miktarı',
                        data: <?php echo json_encode($monthlyReturns); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>



<!-- Güzergahlara Göre Aylık Satış ve İade -->
<h2>Güzergahlara Göre Aylık Satış ve İade Miktarları</h2>
<div id="routeCharts">
    <?php foreach ($routeData as $routeId => $data): ?>
        <div class="routeChart" id="routeChart<?php echo $routeId; ?>">
            <h3>Güzergah <?php echo $routeList[$routeId]; ?></h3>
            <canvas id="routeSalesReturnChart<?php echo $routeId; ?>" width="400" height="200"></canvas>
            <script>
                const routeCtx<?php echo $routeId; ?> = document.getElementById('routeSalesReturnChart<?php echo $routeId; ?>').getContext('2d');
                const routeSalesReturnChart<?php echo $routeId; ?> = new Chart(routeCtx<?php echo $routeId; ?>, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($data['labels']); ?>,
                        datasets: [
                            {
                                label: 'Satış Miktarı',
                                data: <?php echo json_encode($data['sales']); ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'İade Miktarı',
                                data: <?php echo json_encode($data['returns']); ?>,
                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // Dropdown change event to show/hide charts
    document.getElementById('routeSelector').addEventListener('change', function() {
        const selectedRoute = this.value;
        const routeCharts = document.querySelectorAll('.routeChart');

        routeCharts.forEach(chart => {
            if (selectedRoute === "" || chart.id === routeChart${selectedRoute}) {
                chart.style.display = 'block';
            } else {
                chart.style.display = 'none';
            }
        });
    });
</script>


    <!-- Bayi Performansı -->
    <h2>Bayi Performansı</h2>
    <canvas id="bayiPerformanceChart" width="400" height="200"></canvas>
    <p>
        <ul>
            <li>- Bu tablo, farklı bayilerin satış, iade ve toplam tutar performansını karşılaştırır.</li>
            <li>- En iyi performans gösteren bayileri belirlemek için kullanılabilir.</li>
        </ul>
    </p>
    <script>
        const bayiCtx = document.getElementById('bayiPerformanceChart').getContext('2d');
        const bayiPerformanceChart = new Chart(bayiCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($bayiLabels); ?>,
                datasets: [
                    {
                        label: 'Satılan Miktar',
                        data: <?php echo json_encode($bayiSatis); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Tutar',
                        data: <?php echo json_encode($bayiTutar); ?>,
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'İade Miktar',
                        data: <?php echo json_encode($bayiIade); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

   
    <!-- Tarih ve Bayi Bazlı Yoğunluk Haritası -->
    <h2>Tarih ve Bayi Bazlı Ciro</h2>
    <canvas id="heatmapChart" width="400" height="200"></canvas>
    <p>
        <ul>
            <li>- Belirli tarihlerdeki satış trendlerini anlamaya yardımcı olur.</li>
            <li>- Mevsimsel veya dönemsel satış dalgalanmalarını analiz etmek için kullanılır.</li>
        </ul>
    </p>
    <script>
        const heatmapCtx = document.getElementById('heatmapChart').getContext('2d');
        const heatmapChart = new Chart(heatmapCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($revenueTrendsLabels); ?>,
                datasets: [
                    {
                        label: 'Toplam Tutar',
                        data: <?php echo json_encode($revenueTrendsData); ?>,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <!-- Satış ve İade Oranı -->
    <h2>Ürünlere Göre Satış ve İade Oranı</h2>
    <canvas id="salesReturnRatioChart" width="400" height="200"></canvas>
    <p>
        <ul>
            <li>- Ürün bazında satış ve iade oranlarını gösterir.</li>
            <li>- İade oranlarını azaltmaya yönelik stratejiler geliştirmede kullanılır.</li>
            <li>- Müşteri tercihlerine ilişkin bilgi sağlar.</li>
        </ul>
    </p>
    <script>
        const salesReturnCtx = document.getElementById('salesReturnRatioChart').getContext('2d');
        const salesReturnRatioChart = new Chart(salesReturnCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($salesReturnLabels); ?>,
                datasets: [
                    {
                        label: 'Satış',
                        data: <?php echo json_encode($salesReturnSatis); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'İade',
                        data: <?php echo json_encode($salesReturnIade); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true
            }
        });
    </script>

    

</body>
</html>