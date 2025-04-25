    <?php
    include('db.php');

    // Check if student ID is provided
    if (isset($_GET['id'])) {
        $student_id = $_GET['id'];

        // Fetch student info with JOIN to include strand details
        $query = "
            SELECT 
                s.*, 
                st.Name AS Strand_Name, 
                st.Grade AS Strand_Grade 
            FROM 
                Student_Info s
            JOIN 
                Strands st ON s.Strand_ID = st.Strand_ID
            WHERE 
                s.Student_ID = ?
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
        $strands_query = "SELECT * FROM Strands";
        $strands_result = $conn->query($strands_query);
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $student_id   = $_POST['student_id'];
        $first_name   = $_POST['first_name'];
        $middle_name  = $_POST['middle_name'];
        $last_name    = $_POST['last_name'];
        $birthdate    = $_POST['birthdate'];
        $gender       = $_POST['gender'];
        $address      = $_POST['address'];
        $contact      = $_POST['contact'];
        $strand_id    = $_POST['strand']; // NOTE: should match the name in your form select input
        $grade        = $_POST['grade'];

        // Update student info
        $update_query = "
            UPDATE Student_Info SET 
                First_Name = ?,
                Middle_Name = ?,
                Last_Name = ?,
                Birthdate = ?,
                Gender = ?,
                Address = ?,
                Contact_Number = ?,
                Strand_ID = ?,
                Grade = ?
            WHERE 
                Student_ID = ?
        ";

        $stmt = $conn->prepare($update_query);
        $stmt->bind_param(
            "sssssssisi",
            $first_name,
            $middle_name,
            $last_name,
            $birthdate,
            $gender,
            $address,
            $contact,
            $strand_id,
            $grade,
            $student_id
        );

        if ($stmt->execute()) {
            $success = "Student information updated successfully!";

            // Refresh student info with JOIN again
            $query = "
                SELECT 
                    s.*, 
                    st.Name AS Strand_Name, 
                    st.Grade AS Strand_Grade 
                FROM 
                    Student_Info s
                JOIN 
                    Strands st ON s.Strand_ID = st.Strand_ID
                WHERE 
                    s.Student_ID = ?
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();
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
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    </head>
    <body>
        <!-- Sidebar navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i><img src="./img/pdb.png" alt=""></i>
                </div>
                <h3>Paso De Blas NHS</h3>
            </div>
            
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="students_info.php" class="menu-item">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
                <a href="input" class="menu-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Input Students</span>
                </a>
            </div>
        </div>
        
        <!-- Main content area -->
        <div class="main-content">
            <div class="header">
                <h1>Edit Student Information</h1>
                <div class="user-profile">
                <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

                </div>
            </div>

            <div class="container">
                <?php if(isset($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="edit-form">
        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
    <!-- First Name -->
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" 
            value="<?php echo htmlspecialchars($student['First_Name'] ?? ''); ?>" required>
    </div>

    <!-- Middle Name -->
    <div class="form-group">
        <label for="middle_name">Middle Name</label>
        <input type="text" id="middle_name" name="middle_name" 
            value="<?php echo htmlspecialchars($student['Middle_Name'] ?? ''); ?>">
    </div>

    <!-- Last Name -->
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" 
            value="<?php echo htmlspecialchars($student['Last_Name'] ?? ''); ?>" required>
    </div>

    <!-- Birthdate & Gender -->
    <div class="form-row">
        <div class="form-group">
            <label for="birthdate">Birthdate</label>
            <input type="date" id="birthdate" name="birthdate" 
                value="<?php echo htmlspecialchars($student['Birthdate'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo (isset($student['Gender']) && $student['Gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo (isset($student['Gender']) && $student['Gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
    </div>
        <!-- Address -->
    <div class="form-group">
        <label for="address">Address</label>
        <textarea id="address" name="address" required><?php echo htmlspecialchars($student['Address'] ?? ''); ?></textarea>
    </div>

    <!-- Contact -->
    <div class="form-group">
        <label for="contact">Contact Number</label>
        <input type="tel" id="contact" name="contact" 
            value="<?php echo htmlspecialchars($student['Contact_Number'] ?? ''); ?>" required>
    </div>

    <!-- Grade -->
    <div class="form-group">
        <label for="grade">Grade</label>
        <select id="grade" name="grade" class="form-control" required>
            <option value="11" <?php echo (isset($student['Grade']) && $student['Grade'] == '11') ? 'selected' : ''; ?>>Grade 11</option>
            <option value="12" <?php echo (isset($student['Grade']) && $student['Grade'] == '12') ? 'selected' : ''; ?>>Grade 12</option>
        </select>
    </div>
    <!-- Strand -->
    <div class="form-group">
        <label for="strand">Strand</label>
        <select id="strand" name="strand" class="form-control" required>
            <?php
            if (isset($strands_result) && $strands_result->num_rows > 0):
                $strands_result->data_seek(0); // Reset result pointer
                while ($strand = $strands_result->fetch_assoc()): ?>
                    <option value="<?php echo $strand['name']; ?>"
                        <?php echo (isset($student['name']) && $student['name'] == $strand['name']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($strand['name']); ?>
                    </option>
            <?php
                endwhile;
            else: ?>
                <option disabled>No strands available</option>
            <?php endif; ?>
        </select>
    </div>


        <!-- Submit -->
        <div class="form-group text-center">
            <button type="submit" class="btn btn-success">Update Student</button>
        </div>
    </form>


                        </div>
                    </div>
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
