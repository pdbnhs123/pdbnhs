<?php
require_once 'security.php'; // security headers & nonce
require_once 'config.php';   // session & common setup
require_once 'db.php';       // $pdo setup
require_once 'information.php';
$username = '';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'sidebar.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon Configuration -->
    <link rel="icon" href="img/pdb.png" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="img/pdb.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/pdb.png">
    <link rel="apple-touch-icon" sizes="180x180" href="img/pdb.pn">
    <meta name="msapplication-TileColor" content="#0d265c">
    <meta name="msapplication-TileImage" content="img/pdb.png">
    
    <!-- CSS and Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
        .chart-wrapper {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
        </div>
        
        <div class="container my-5">
            <!-- Strand Summary Cards -->
            <div class="row justify-content-center g-4">
                <?php foreach ($strand_data as $strand => $count): ?>
                    <div class="col-md-4">
                        <div class="card shadow border-info">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-user"></i>
                                <h5 class="card-title text-info"><?= htmlspecialchars($strand) ?></h5>
                                <p class="card-text fs-1 fw-bold"><?= $count ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Document Submission Chart -->
            <div class="chart-container card shadow-sm p-4 my-4">
                <br>
                <center><h3 class="text-center mb-4">Document Submission Summary</h3></center>
                <div class="chart-wrapper">
                    <canvas id="documentChart"></canvas>
                </div>
            </div>
            <br>
        </div>
    </div>

    <script>
        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            initializeDocumentChart(); // This will now show the chart
            setupCardHover();
            setupExportButtons();
        });

        function initializeSidebar() {
            const menuItems = document.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.querySelector('i').style.transform = 'translateX(5px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.querySelector('i').style.transform = 'translateX(0)';
                });
                
                item.addEventListener('click', function(e) {
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    const rect = this.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    ripple.style.left = `${e.clientX - rect.left}px`;
                    ripple.style.top = `${e.clientY - rect.top}px`;
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 800);
                });
            });
            
            let resizeTimer;
            window.addEventListener('resize', function() {
                document.body.classList.add('resize-animation-stopper');
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    document.body.classList.remove('resize-animation-stopper');
                }, 400);
            });
        }

        function initializeDocumentChart() {
            const chartEl = document.getElementById('documentChart');
            if (!chartEl) {
                console.error("Chart element not found");
                return;
            }

            // Destroy previous chart if exists
            if (chartEl.chart) {
                chartEl.chart.destroy();
            }

            const documentData = <?php echo json_encode($document_data, JSON_HEX_TAG); ?>;
            
            // Debug: Check what data we're getting
            console.log("Document Data:", documentData);
            
            if (!documentData || Object.keys(documentData).length === 0) {
                chartEl.innerHTML = '<p class="text-muted">No document data available</p>';
                console.warn("No document data available");
                return;
            }

            const documentLabels = Object.keys(documentData);
            const documentValues = Object.values(documentData);

            const backgroundColors = [
                '#007bff', '#28a745', '#ffc107', '#dc3545', 
                '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'
            ].slice(0, documentLabels.length);
            
            const borderColors = [
                '#0056b3', '#1c7430', '#d39e00', '#bd2130',
                '#5a3d8a', '#d5670d', '#17a673', '#c5306d'
            ].slice(0, documentLabels.length);

            const ctx = chartEl.getContext('2d');
            chartEl.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: documentLabels,
                    datasets: [{
                        label: 'Number of Submissions',
                        data: documentValues,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 80
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 6,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: { left: 10, right: 10, top: 10, bottom: 10 }
                    }
                }
            });
        }

        function setupCardHover() {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.12)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.08)';
                });
            });
        }

        function setupExportButtons() {
            const downloadCsv = document.getElementById('downloadCsv');
            const downloadWord = document.getElementById('downloadWord');
            const printPage = document.getElementById('printPage');
            
            if (downloadCsv) downloadCsv.addEventListener('click', downloadCSV);
            if (downloadWord) downloadWord.addEventListener('click', downloadWord);
            if (printPage) printPage.addEventListener('click', printPage);
        }

        function downloadCSV() {
            let csvContent = "Strand,Student Count\n";
            const strandData = <?php echo json_encode($strand_data); ?>;
            
            for (const [strand, count] of Object.entries(strandData)) {
                csvContent += `${strand},${count}\n`;
            }
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'strand_distribution.csv');
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function downloadWord() {
            alert('Word export functionality would be implemented here.\nIn a real implementation, you would use a library like docx.js');
        }

        function printPage() {
            const elementsToHide = document.querySelectorAll('.sidebar, .user-profile, .btn');
            elementsToHide.forEach(el => el.style.display = 'none');
            
            document.querySelector('.main-content').style.marginLeft = '0';
            window.print();
            
            setTimeout(() => {
                elementsToHide.forEach(el => el.style.display = '');
                document.querySelector('.main-content').style.marginLeft = '260px';
            }, 500);
        }
    </script>
</body>
</html>
