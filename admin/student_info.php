<?php
require_once 'security.php';
require_once 'db.php';
require_once 'student_data.php';

// Get data
$data = getStrandAndDocumentData($pdo);
$document_data = $data['document_data'];
$all_students = getAllStudents($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <link rel="stylesheet" href="./css/student_info.css?=v1.2">
    <link rel="stylesheet" href="css/core.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- For PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</head>
<style>
</style>
<body>
<?php
    // Include the loading screen HTML structure first
    include 'load.html';
    ?>

    <div class="app-layout">
        <?php
        // Include the sidebar HTML
        include 'sidebar.html';
        ?>

<div class="content-area">
    <div class="header">
            <h1>Student Information</h1>
            <div class="main-content">
            <?php include 'header.html'; ?>
        <!-- Search Bar with Bulk Delete Button -->
        <div class="search-container">
            <div class="input-group search-input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search students...">
            </div>
            <button id="bulkDeleteBtn" class="btn-bulk-delete" onclick="deleteSelectedStudents()">
                <i class="fas fa-trash-alt"></i> Delete Selected
            </button>
        </div>
</div>
        
        <!-- Export Buttons with Distinct Hover Colors -->
        <div class="export-buttons">
            <button class="btn btn-export btn-csv" onclick="downloadCSV()">
                <i class="fas fa-file-csv"></i> CSV
            </button>
            <button class="btn btn-export btn-word" onclick="downloadWord()">
                <i class="fas fa-file-word"></i> Word
            </button>
            <button class="btn btn-export btn-pdf" onclick="downloadPDF()">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="btn btn-export btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
        
        <!-- Student Table -->
        <section class="student-table-section">
            <div class="table-responsive">
                <table id="studentTable" class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th class="checkbox-header">
                                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)">
                            </th>
                            <th>Student Type</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Grade Level</th>
                            <th>Strand</th>
                            <th>City</th>
                            <th>PSA</th>
                            <th>Form 137</th>
                            <th>Good Moral</th>
                            <th>Card</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_students as $student): ?>
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="student-checkbox" value="<?= $student['id'] ?>" onchange="updateBulkDeleteButton()">
                            </td>
                            <td><?= htmlspecialchars($student['Student_Type'] ?? 'Regular') ?></td>
                            <td><?= htmlspecialchars($student['Full_Name']) ?></td>
                            <td><?= htmlspecialchars($student['Gender']) ?></td>
                            <td><?= htmlspecialchars($student['Age']) ?></td>
                            <td>Grade <?= isset($student['grade_level']) ? htmlspecialchars($student['grade_level']) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($student['Strand_Name']) ?></td>
                            <td><?= htmlspecialchars($student['City']) ?></td>
                            <td class="document-status <?= $student['PSA'] ? 'present' : 'missing' ?>">
                                <?= $student['PSA'] ? '✓ Present' : '✗ Missing' ?>
                            </td>
                            <td class="document-status <?= $student['Form137'] ? 'present' : 'missing' ?>">
                                <?= $student['Form137'] ? '✓ Present' : '✗ Missing' ?>
                            </td>
                            <td class="document-status <?= $student['Good_moral'] ? 'present' : 'missing' ?>">
                                <?= $student['Good_moral'] ? '✓ Present' : '✗ Missing' ?>
                            </td>
                            <td class="document-status <?= $student['Card'] ? 'present' : 'missing' ?>">
                                <?= $student['Card'] ? '✓ Present' : '✗ Missing' ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-action btn-edit" onclick="editStudent(<?= $student['id'] ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-action btn-delete" onclick="deleteStudent(<?= $student['id'] ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    

    <script>
    // Function to edit student
    function editStudent(id) {
        // You can implement your edit functionality here
        console.log("Editing student with ID:", id);
      window.location.href = 'edit_student.php?id=' + id;
    }

    // Function to delete student
    function deleteStudent(id) {
        if (confirm("Are you sure you want to delete this student?")) {
            // You can implement your delete functionality here
            console.log("Deleting student with ID:", id);
         window.location.href = 'delete_student.php?id=' + id;
        }
    }

// Function to delete selected students
function deleteSelectedStudents() {
    const selectedIds = [];
    document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });

    if (selectedIds.length === 0) {
        alert("Please select at least one student to delete");
        return;
    }

    if (confirm(`Are you sure you want to delete ${selectedIds.length} selected student(s)?`)) {
        // Sending the selected IDs to bulk_delete.php via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "./function/bulk_delete.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Prepare the data to send
        const data = "ids=" + encodeURIComponent(JSON.stringify(selectedIds));

        xhr.onload = function() {
            if (xhr.status === 200) {
                // Handle the response from the server
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert("Students deleted successfully");
                    // Optionally, you can remove the deleted students from the table
                    selectedIds.forEach(id => {
                        const row = document.querySelector(`tr[data-id='${id}']`);
                        if (row) row.remove();
                    });
                } else {
                    alert("Error deleting students");
                }
            } else {
                alert("An error occurred while deleting students");
            }
        };

        xhr.send(data);
    }
}

    // Function to toggle select all checkboxes
    function toggleSelectAll(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateBulkDeleteButton();
    }

    // Function to update bulk delete button visibility
    function updateBulkDeleteButton() {
        const anyChecked = document.querySelectorAll('.student-checkbox:checked').length > 0;
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        
        if (anyChecked) {
            bulkDeleteBtn.classList.add('show');
            
            // Update count in button text
            const count = document.querySelectorAll('.student-checkbox:checked').length;
            bulkDeleteBtn.innerHTML = `<i class="fas fa-trash-alt"></i> Delete Selected (${count})`;
        } else {
            bulkDeleteBtn.classList.remove('show');
            document.getElementById('selectAllCheckbox').checked = false;
        }
    }

    // Function to download CSV
    function downloadCSV() {
        try {
            const rows = [
                ["Student Type", "Full Name", "Gender", "Age", "Grade Level", "Strand", "City", "PSA", "Form 137", "Good Moral", "Card"],
                <?php foreach ($all_students as $s): ?>
                [
                    "<?= addslashes($s['Student_Type'] ?? 'Regular') ?>",
                    "<?= addslashes($s['Full_Name']) ?>",
                    "<?= addslashes($s['Gender']) ?>",
                    "<?= $s['Age'] ?>",
                    "Grade <?= addslashes($s['grade_level']) ?>",
                    "<?= addslashes($s['Strand_Name']) ?>",
                    "<?= addslashes($s['City']) ?>",
                    "<?= $s['PSA'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Form137'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Good_moral'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Card'] ? 'Present' : 'Missing' ?>"
                ],
                <?php endforeach; ?>
            ];
            
            let csvContent = rows.map(row => 
                row.map(field => "${field}").join(",")
            ).join("\n");
            
            let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "student_report_" + new Date().toISOString().slice(0,10) + ".csv";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error("Error generating CSV:", error);
            alert("Error generating CSV file. Please check console for details.");
        }
    }

    // Function to download Word document
    function downloadWord() {
        try {
            let htmlContent = `
            <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
            <head>
                <title>Student Information Report</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #000; padding: 5px; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2>Student Information Report</h2>
                <table>
                    <tr>
                        <th>Student Type</th><th>Full Name</th><th>Gender</th><th>Age</th>
                        <th>Grade Level</th><th>Strand</th><th>City</th><th>PSA</th>
                        <th>Form 137</th><th>Good Moral</th><th>Card</th>
                    </tr>
                    <?php foreach ($all_students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['Student_Type'] ?? 'Regular') ?></td>
                        <td><?= htmlspecialchars($s['Full_Name']) ?></td>
                        <td><?= htmlspecialchars($s['Gender']) ?></td>
                        <td><?= $s['Age'] ?></td>
                        <td>Grade <?= htmlspecialchars($s['grade_level']) ?></td>
                        <td><?= htmlspecialchars($s['Strand_Name']) ?></td>
                        <td><?= htmlspecialchars($s['City']) ?></td>
                        <td><?= $s['PSA'] ? '✓ Present' : '✗ Missing' ?></td>
                        <td><?= $s['Form137'] ? '✓ Present' : '✗ Missing' ?></td>
                        <td><?= $s['Good_moral'] ? '✓ Present' : '✗ Missing' ?></td>
                        <td><?= $s['Card'] ? '✓ Present' : '✗ Missing' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </body>
            </html>
            `;

            let blob = new Blob(['\ufeff', htmlContent], { 
                type: 'application/msword;charset=utf-8' 
            });
            let link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "student_report_" + new Date().toISOString().slice(0,10) + ".doc";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            console.error("Error generating Word document:", error);
            alert("Error generating Word document. Please check console for details.");
        }
    }

    // Function to download PDF (requires jsPDF library)
    async function downloadPDF() {
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape');
            
            // Add title
            doc.setFontSize(16);
            doc.text("Student Information Report", 14, 15);
            doc.setFontSize(10);
            doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 22);
            
            // Prepare table data
            const headers = [
                "Student Type",
                "Full Name",
                "Gender",
                "Age",
                "Grade Level",
                "Strand",
                "City",
                "PSA",
                "Form 137",
                "Good Moral",
                "Card"
            ];
            
            const data = [
                <?php foreach ($all_students as $s): ?>
                [
                    "<?= addslashes($s['Student_Type'] ?? 'Regular') ?>",
                    "<?= addslashes($s['Full_Name']) ?>",
                    "<?= addslashes($s['Gender']) ?>",
                    "<?= $s['Age'] ?>",
                    "Grade <?= addslashes($s['grade_level']) ?>",
                    "<?= addslashes($s['Strand_Name']) ?>",
                    "<?= addslashes($s['City']) ?>",
                    "<?= $s['PSA'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Form137'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Good_moral'] ? 'Present' : 'Missing' ?>",
                    "<?= $s['Card'] ? 'Present' : 'Missing' ?>"
                ],
                <?php endforeach; ?>
            ];
            
            // Add table using autoTable plugin
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 25,
                styles: {
                    fontSize: 8,
                    cellPadding: 2,
                    overflow: 'linebreak'
                },
                columnStyles: {
                    0: { cellWidth: 'auto' },
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 'auto' },
                    3: { cellWidth: 'auto' },
                    4: { cellWidth: 'auto' },
                    5: { cellWidth: 'auto' },
                    6: { cellWidth: 'auto' },
                    7: { cellWidth: 'auto' },
                    8: { cellWidth: 'auto' },
                    9: { cellWidth: 'auto' },
                    10: { cellWidth: 'auto' }
                }
            });
            
            doc.save("student_report_" + new Date().toISOString().slice(0,10) + ".pdf");
        } catch (error) {
            console.error("Error generating PDF:", error);
            alert("Error generating PDF. Make sure jsPDF is loaded and check console for details.");
        }
    }

    // Function to search the table
    function searchTable() {
        try {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("studentTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tds = tr[i].getElementsByTagName("td");
                let match = false;
                
                for (let j = 1; j < tds.length; j++) { // Start from 1 to skip checkbox column
                    if (tds[j]) {
                        let text = tds[j].textContent || tds[j].innerText;
                        if (text.toLowerCase().includes(filter)) {
                            match = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = match ? "" : "none";
            }
        } catch (error) {
            console.error("Error in search:", error);
        }
    }

    // Add event listener for Enter key in search
    document.getElementById("searchInput").addEventListener("keyup", function(event) {
        if (event.key === "Enter") {
            searchTable();
        }
    });
    </script>
    <script src="js/dashboard.js"></script>
</body>
</html>