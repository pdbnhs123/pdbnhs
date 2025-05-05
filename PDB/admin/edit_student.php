<?php
include('../db.php');

include '../sidebar.php';

// Initialize variables
$student = [];
$strands = [];
$success = '';
$error = '';

// Check if student ID is provided
if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch student info with JOIN to include strand details
    $query = "
        SELECT 
            s.*, 
            st.name AS strand_name
        FROM 
            student_info s
        LEFT JOIN 
            strands st ON s.strand_id = st.strand_id
        WHERE 
            s.student_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    if (!$student) {
        die("Student not found");
    }

    // Fetch all strands for dropdown
    $strands_query = "SELECT * FROM strands";
    $strands_result = $conn->query($strands_query);
    if ($strands_result) {
        $strands = $strands_result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    die("Student ID not provided");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $strand_id = $_POST['strand_id'];

    // Update student info
    $update_query = "
        UPDATE student_info SET 
            first_name = ?,
            middle_name = ?,
            last_name = ?,
            birthdate = ?,
            gender = ?,
            address = ?,
            contact_number = ?,
            strand_id = ?
        WHERE 
            student_id = ?
    ";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param(
        "sssssssii",
        $first_name,
        $middle_name,
        $last_name,
        $birthdate,
        $gender,
        $address,
        $contact_number,
        $strand_id,
        $student_id
    );

    if ($stmt->execute()) {
        // Redirect to students_info.php after successful update
        header("Location: students_info.php");
        exit();
    } else {
        $error = "Error updating student: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Paso De Blas NHS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>    
    <!-- Main content area -->
    <div class="main-content">
        <div class="header">
            <h1>Edit Student Information</h1>
            <div class="user-profile">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="container">
            <form method="POST" class="edit-form">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id'] ?? ''); ?>">
                
                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                        value="<?php echo htmlspecialchars($student['first_name'] ?? ''); ?>" required>
                </div>

                <!-- Middle Name -->
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" 
                        value="<?php echo htmlspecialchars($student['middle_name'] ?? ''); ?>">
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                        value="<?php echo htmlspecialchars($student['last_name'] ?? ''); ?>" required>
                </div>

                <!-- Birthdate & Gender -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" 
                            value="<?php echo htmlspecialchars($student['birthdate'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" required>
                            <option value="Male" <?php echo (isset($student['gender']) && $student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($student['gender']) && $student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (isset($student['gender']) && $student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label for="contact_number">Contact Number</label>
                    <input type="tel" id="contact_number" name="contact_number" 
                        value="<?php echo htmlspecialchars($student['contact_number'] ?? ''); ?>" required>
                </div>
                
                <!-- Strand -->
                <div class="form-group">
                    <label for="strand_id">Strand</label>
                    <select id="strand_id" name="strand_id" required>
                        <?php if (!empty($strands)): ?>
                            <?php foreach ($strands as $strand): ?>
                                <option value="<?php echo $strand['strand_id']; ?>"
                                    <?php echo (isset($student['strand_id']) && $student['strand_id'] == $strand['strand_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($strand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option disabled>No strands available</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="students_info.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>