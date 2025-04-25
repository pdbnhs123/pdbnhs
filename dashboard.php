<?php
include('db.php');
include 'information.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paso De Blas NHS Dashboard</title>
    <!-- Include external CSS and font resources -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js library -->
</head>
<body>
    <!-- Sidebar navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i><img src="./img/pdb.png" alt=""></i>
            </div>
            <h3>Paso De Blas NHS</h3> <!-- School name -->
        </div>
        
        <div class="sidebar-menu">
            <!-- Navigation menu items -->
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span> <!-- Dashboard link -->
            </a>
            <a href="students_info.php" class="menu-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span> <!-- Students link -->
            </a>
            <a href="input.php" class="menu-item">
                <i class="fas fa-user-plus"></i>
                <span>Input Students</span> <!-- Input Students link -->
            </a>
        </div>
    </div>
    
    <!-- Main content area -->
    <div class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1> <!-- Page title -->
        </div>
        
        <div class="container my-5">
            <!-- Grade Summary Cards section -->
              <div class="row justify-content-center g-4">
                <!-- Grade 11 Card -->
                <div class="col-md-4">
                    <div class="card shadow border-primary">
                        <div class="card-body text-center">
                        <i class="fa-solid fa-user"></i>
                            <h5 class="card-title text-primary">Grade 11</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $grade_data['11'] ?? 0; ?></p> <!-- Display Grade 11 count -->
                        </div>
                    </div>
                </div>

                <!-- Grade 12 Card -->
                <div class="col-md-4">
                    <div class="card shadow border-success">
                        <div class="card-body text-center">
                        <i class="fa-solid fa-user"></i>
                            <h5 class="card-title text-success">Grade 12</h5>
                            <p class="card-text fs-1 fw-bold"><?php echo $grade_data['12'] ?? 0; ?></p> <!-- Display Grade 12 count -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Strand Distribution Chart section -->
            <div class="chart-container card shadow-sm p-4 my-4"><br>
              <center>  <h3 class="text-center mb-4">Strand Distribution Chart</h3> </center> <!-- Chart title -->
                <div class="chart-wrapper">
                    <canvas id="strandChart"></canvas> <!-- Chart canvas element -->
                </div>
            </div><br>

            <!-- Export Buttons section -->
            <div class="text-center mt-4">
                <button class="btn btn-outline-primary me-2" id="downloadCsv">Download CSV</button> <!-- CSV download button -->
                <button class="btn btn-outline-success me-2" id="downloadWord">Download Word</button> <!-- Word download button -->
                <button class="btn btn-outline-dark me-2" id="printPage">Print</button> <!-- Print button -->
            </div>
        </div>
    </div>

    <script>
        // Initialize the page when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar(); // Set up sidebar functionality
            initializeStrandChart(); // Initialize the strand chart
            setupCardHover(); // Set up card hover effects
            setupExportButtons(); // Set up export button handlers
        });

        let strandChart = null; // Global variable for chart instance

        // Function to initialize sidebar interactions
        function initializeSidebar() {
            const menuItems = document.querySelectorAll('.menu-item'); // Get all menu items
            
            menuItems.forEach(item => {
                // Add hover effects to menu items
                item.addEventListener('mouseenter', function() {
                    this.querySelector('i').style.transform = 'translateX(5px)'; // Move icon on hover
                });
                
                item.addEventListener('mouseleave', function() {
                    this.querySelector('i').style.transform = 'translateX(0)'; // Reset icon position
                });
                
                // Add click effects to menu items
                item.addEventListener('click', function(e) {
                    menuItems.forEach(i => i.classList.remove('active')); // Remove active class from all
                    this.classList.add('active'); // Add active class to clicked item
                    
                    // Create ripple effect on click
                    const rect = this.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    ripple.style.left = `${e.clientX - rect.left}px`;
                    ripple.style.top = `${e.clientY - rect.top}px`;
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 800); // Remove ripple after animation
                });
            });
            
            // Handle window resize events
            let resizeTimer;
            window.addEventListener('resize', function() {
                document.body.classList.add('resize-animation-stopper'); // Pause animations during resize
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    document.body.classList.remove('resize-animation-stopper');
                    if (strandChart) {
                        strandChart.resize(); // Resize chart if window resizes
                    }
                }, 400);
            });
        }

        // Function to initialize the strand distribution chart
        function initializeStrandChart() {
            const chartEl = document.getElementById('strandChart');
            if (!chartEl) return; // Exit if chart element not found
            
            if (strandChart) {
                strandChart.destroy(); // Destroy existing chart instance
            }
            
            // Get data from PHP
            const strandLabels = Object.keys(<?php echo json_encode($strand_data); ?>);
            const strandValues = Object.values(<?php echo json_encode($strand_data); ?>);
            
            // Create new chart instance
            const ctx = chartEl.getContext('2d');
            strandChart = new Chart(ctx, {
                type: 'bar', // Set chart type to bar
                data: {
                    labels: strandLabels, // Set strand names as labels
                    datasets: [{
                        label: 'Number of Students',
                        data: strandValues, // Set student counts as data
                        backgroundColor: ' #0d265c', // Bar color
                        borderColor: 'rgb(15, 13, 117)', // Border color
                        borderWidth: 2, // Border width
                        borderRadius: 6, // Rounded bar corners
                        barThickness: 100, // Fixed bar width
                        maxBarThickness: 250, // Maximum bar width
                        minBarLength: 2 // Minimum bar length
                    }]
                },
                options: {
                    responsive: true, // Make chart responsive
                    maintainAspectRatio: false, // Don't maintain aspect ratio
                    scales: {
                        x: {
                            grid: { display: false }, // Hide x-axis grid lines
                            barPercentage: 0.4, // Control bar width
                            categoryPercentage: 0.6 // Control space between categories
                        },
                        y: {
                            beginAtZero: true, // Start y-axis at 0
                            ticks: { precision: 0 }, // No decimal places
                            grid: { color: 'rgba(0, 0, 0, 0.05)' } // Light grid lines
                        }
                    },
                    plugins: {
                        legend: { display: false }, // Hide legend
                        tooltip: { // Customize tooltips
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 6
                        }
                    },
                    layout: { // Add padding around chart
                        padding: { left: 10, right: 10, top: 10, bottom: 10 }
                    }
                }
            });
        }

        // Function to set up card hover effects
        function setupCardHover() {
            const cards = document.querySelectorAll('.card'); // Get all card elements
            cards.forEach(card => {
                // Add hover effects to cards
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)'; // Lift card on hover
                    this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.12)'; // Enhance shadow
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)'; // Reset position
                    this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.08)'; // Reset shadow
                });
            });
        }

        // Function to set up export button handlers
        function setupExportButtons() {
            document.getElementById('downloadCsv').addEventListener('click', downloadCSV); // CSV export
            document.getElementById('downloadWord').addEventListener('click', downloadWord); // Word export
            document.getElementById('printPage').addEventListener('click', printPage); // Print page
        }

        // Function to download data as CSV
        function downloadCSV() {
            let csvContent = "Strand,Student Count\n"; // CSV header
            const strandData = <?php echo json_encode($strand_data); ?>;
            
            // Add each strand's data to CSV
            for (const [strand, count] of Object.entries(strandData)) {
                csvContent += `${strand},${count}\n`;
            }
            
            // Create and trigger download
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

        // Function to handle Word export (placeholder)
        function downloadWord() {
            alert('Word export functionality would be implemented here.\nIn a real implementation, you would use a library like docx.js');
        }

        // Function to handle page printing
        function printPage() {
            const elementsToHide = document.querySelectorAll('.sidebar, .user-profile, .btn');
            elementsToHide.forEach(el => el.style.display = 'none'); // Hide elements for printing
            
            document.querySelector('.main-content').style.marginLeft = '0'; // Adjust layout
            window.print(); // Trigger print dialog
            
            setTimeout(() => {
                elementsToHide.forEach(el => el.style.display = ''); // Restore hidden elements
                document.querySelector('.main-content').style.marginLeft = '260px'; // Reset layout
            }, 500);
        }
    </script>
</body>
</html>
