<?php
require_once 'db.php';

function getAllStudents(PDO $pdo): array {
    $sql = "SELECT 
        id,
        student_type AS Student_Type,
        full_name AS Full_Name,
        gender AS Gender,
        age AS Age, grade_level,
        strand AS Strand_Name,
        city AS City,  
        psa AS PSA,
        form137 AS Form137,
        good_moral AS Good_moral,
        card AS Card
    FROM student_info";
    
    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching students: " . $e->getMessage());
        return [];
    }
}

function getStrandAndDocumentData(PDO $pdo): array {
    $sql = "
        SELECT 
            strand,
            COUNT(*) AS total_students,
            SUM(PSA) AS psa_count,
            SUM(Form137) AS form137_count,
            SUM(Good_moral) AS good_moral_count,
            SUM(Card) AS card_count
        FROM 
            student_info
        GROUP BY 
            strand
    ";

    $strand_data = [];
    $document_totals = [
        'PSA' => 0,
        'Form137' => 0,
        'Good Moral' => 0,
        'Card' => 0
    ];

    try {
        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $strand = $row['strand'];
            $strand_data[$strand] = (int)$row['total_students'];

            // Sum up each document across strands
            $document_totals['PSA'] += (int)$row['psa_count'];
            $document_totals['Form137'] += (int)$row['form137_count'];
            $document_totals['Good Moral'] += (int)$row['good_moral_count'];
            $document_totals['Card'] += (int)$row['card_count'];
        }
    } catch (PDOException $e) {
        error_log("Database error in getStrandAndDocumentData: " . $e->getMessage());
        throw new RuntimeException("Failed to retrieve strand and document data.");
    }

    return [
        'strand_data' => $strand_data,
        'document_data' => $document_totals
    ];
}

$data = getStrandAndDocumentData($pdo);
$strand_data = $data['strand_data'];
$document_data = $data['document_data'];
?>