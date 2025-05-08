<?php

function getAllStudents(PDO $pdo): array {
    $sql = "SELECT 
        id,
        student_type AS Student_Type,
        full_name AS Full_Name,
        gender AS Gender,
        age AS Age, 
        grade_level,
        strand AS Strand_Name,
        city AS City,
        email AS Email,  
        psa AS PSA,
        form137 AS Form137,
        good_moral AS Good_moral,
        card AS Card
    FROM student_info";

    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching students: " . $e->getMessage());
        header("Location: ../template/error_400.html");
        exit();
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
            $strand_data[$row['strand']] = (int)$row['total_students'];
            $document_totals['PSA'] += (int)$row['psa_count'];
            $document_totals['Form137'] += (int)$row['form137_count'];
            $document_totals['Good Moral'] += (int)$row['good_moral_count'];
            $document_totals['Card'] += (int)$row['card_count'];
        }

        return [
            'strand_data' => $strand_data,
            'document_data' => $document_totals
        ];
    } catch (PDOException $e) {
        error_log("Error fetching strand data: " . $e->getMessage());
        header("Location: ../template/error_400.html");
        exit();
    }
}
function getCityDistribution(PDO $pdo): array {
    $sql = "SELECT city, COUNT(*) AS total FROM student_info GROUP BY city";
    $city_data = [];

    try {
        $stmt = $pdo->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $city_data[$row['city']] = (int)$row['total'];
        }
        return $city_data;
    } catch (PDOException $e) {
        error_log("Error fetching city data: " . $e->getMessage());
        return [];
    }
}
