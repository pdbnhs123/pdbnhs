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
