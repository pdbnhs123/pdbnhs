
<?php
include('db.php');

// 1. Aggregated data: Strand and Grade distribution
$distribution_sql = "SELECT 
                        s.name AS Strand_Name, 
                        s.grade AS Grade, 
                        COUNT(st.student_id) AS Student_Count
                    FROM 
                        strands s
                    LEFT JOIN 
                        student_info st ON s.strand_id = st.strand_id
                    GROUP BY 
                        s.name, s.grade
                    ORDER BY 
                        s.grade, s.name";

$distribution_result = $conn->query($distribution_sql);

$strand_data = [];
$grade_data = [];
$students_distribution = [];

if ($distribution_result && $distribution_result->num_rows > 0) {
    while ($row = $distribution_result->fetch_assoc()) {
        $students_distribution[] = $row;

        if (!isset($strand_data[$row['Strand_Name']])) {
            $strand_data[$row['Strand_Name']] = 0;
        }
        $strand_data[$row['Strand_Name']] += $row['Student_Count'];

        if (!isset($grade_data[$row['Grade']])) {
            $grade_data[$row['Grade']] = 0;
        }
        $grade_data[$row['Grade']] += $row['Student_Count'];
    }
}

// 2. Full student information
$student_info_sql = "SELECT 
                        st.student_id AS Student_ID,
                        st.first_name AS First_Name,
                        st.last_name AS Last_Name,
                        st.birthdate AS Birthdate,
                        st.gender AS Gender,
                        st.address AS Address,
                        st.contact_number AS Contact_Number,
                        s.name AS Strand_Name,
                        s.grade AS Grade
                    FROM 
                        student_info st
                    JOIN 
                        strands s ON st.strand_id = s.strand_id
                    ORDER BY 
                        s.grade, s.name, st.last_name";

$student_info_result = $conn->query($student_info_sql);

$all_students = [];

if ($student_info_result && $student_info_result->num_rows > 0) {
    while ($row = $student_info_result->fetch_assoc()) {
        $all_students[] = $row;
    }
}
?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Info Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="style.css" rel="stylesheet">
    <style>
      body { padding: 40px; }
      h2 { margin-bottom: 30px; }
      table th, table td { vertical-align: middle !important; }
    </style>
  </head>
  <body>
  <div class="container">
    <h2 class="text-center">Student Information Report</h2>

    <div class="row mb-3">
      <div class="col-md-4 text-end">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="gray" class="bi bi-search" viewBox="0 0 16 16">
              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.397h-.001l3.85 3.85a1 1 0 0 0 
                1.415-1.415l-3.85-3.85zm-5.242.656a5.5 5.5 0 
                1 1 0-11 5.5 5.5 0 0 1 0 11z" />
            </svg>
          </span>
          <input type="text" id="searchInput" onkeyup="searchTable()" class="form-control" placeholder="Search...">
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button class="btn btn-outline-primary me-2" onclick="downloadCSV()">CSV</button>
        <button class="btn btn-outline-success me-2" onclick="downloadWord()">Word</button>
        <button class="btn btn-outline-danger me-2" onclick="downloadPDF()">PDF</button>
        <button class="btn btn-outline-dark" onclick="window.print()">Print</button>
      </div>
    </div>

    <table id="studentTable" class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Birthdate</th>
          <th>Gender</th>
          <th>Address</th>
          <th>Contact</th>
          <th>Strand</th>
          <th>Grade</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($all_students as $student): ?>
          <tr>
            <td><?= htmlspecialchars($student['Student_ID']) ?></td>
            <td><?= htmlspecialchars($student['First_Name']) ?></td>
            <td><?= htmlspecialchars($student['Last_Name']) ?></td>
            <td><?= htmlspecialchars($student['Birthdate']) ?></td>
            <td><?= htmlspecialchars($student['Gender']) ?></td>
            <td><?= htmlspecialchars($student['Address']) ?></td>
            <td><?= htmlspecialchars($student['Contact_Number']) ?></td>
            <td><?= htmlspecialchars($student['Strand_Name']) ?></td>
            <td><?= htmlspecialchars($student['Grade']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <script>
  function downloadCSV() {
    const rows = [
      ["ID", "First Name", "Last Name", "Birthdate", "Gender", "Address", "Contact Number", "Strand", "Grade"],
      <?php foreach ($all_students as $s): ?>
        ["<?= $s['Student_ID'] ?>", "<?= $s['First_Name'] ?>", "<?= $s['Last_Name'] ?>", "<?= $s['Birthdate'] ?>", "<?= $s['Gender'] ?>", "<?= $s['Address'] ?>", "<?= $s['Contact_Number'] ?>", "<?= $s['Strand_Name'] ?>", "<?= $s['Grade'] ?>"],
      <?php endforeach; ?>
    ];
    let csv = rows.map(r => r.join(",")).join("\n");
    let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "student_report.csv";
    link.click();
  }

  function downloadWord() {
    let content = `<h2>Student Information Report</h2>
    <table border="1" style="width: 100%; border-collapse: collapse;">
      <tr>
        <th>ID</th><th>First Name</th><th>Last Name</th><th>Birthdate</th><th>Gender</th><th>Address</th><th>Contact</th><th>Strand</th><th>Grade</th>
      </tr>
      <?php foreach ($all_students as $s): ?>
      <tr>
        <td><?= $s['Student_ID'] ?></td>
        <td><?= $s['First_Name'] ?></td>
        <td><?= $s['Last_Name'] ?></td>
        <td><?= $s['Birthdate'] ?></td>
        <td><?= $s['Gender'] ?></td>
        <td><?= $s['Address'] ?></td>
        <td><?= $s['Contact_Number'] ?></td>
        <td><?= $s['Strand_Name'] ?></td>
        <td><?= $s['Grade'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table>`;
    let blob = new Blob(['<html><body>' + content + '</body></html>'], { type: 'application/msword' });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "student_report.doc";
    link.click();
  }

  async function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l');
    doc.setFontSize(14);
    doc.text("Student Information Report", 10, 10);
    let y = 20;
    doc.setFontSize(10);
    <?php foreach ($all_students as $s): ?>
      doc.text(`<?= $s['Student_ID'] ?> | <?= $s['First_Name'] ?> | <?= $s['Last_Name'] ?> | <?= $s['Birthdate'] ?> | <?= $s['Gender'] ?> | <?= $s['Address'] ?> | <?= $s['Contact_Number'] ?> | <?= $s['Strand_Name'] ?> | <?= $s['Grade'] ?>`, 10, y);
      y += 8;
      if (y > 190) {
        doc.addPage();
        y = 10;
      }
    <?php endforeach; ?>
    doc.save("student_report.pdf");
  }

  function searchTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("studentTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
      let tds = tr[i].getElementsByTagName("td");
      let match = false;
      for (let j = 0; j < tds.length; j++) {
        if (tds[j] && tds[j].innerText.toLowerCase().includes(filter)) {
          match = true;
          break;
        }
      }
      tr[i].style.display = match ? "" : "none";
    }
  }
  </script>
  </body>
  </html>
