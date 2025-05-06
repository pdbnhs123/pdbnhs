<?php
require_once 'security.php';
require_once 'db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $studentType = $_POST['studentType'] ?? '';
    $fullName = trim($_POST['fullname'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $gradeLevel = $_POST['gradeLevel'] ?? '';
    $strand = ($gradeLevel === '11' || $gradeLevel === '12') ? trim($_POST['strand'] ?? '') : '';

    // Handle individual documents (checkboxes)
    $PSA = isset($_POST['PSA']) ? 1 : 0;
    $Form137 = isset($_POST['Form137']) ? 1 : 0;
    $GoodMoral = isset($_POST['Good_moral']) ? 1 : 0;
    $Card = isset($_POST['Card']) ? 1 : 0;

    // Handle city selection
    $city = $_POST['city'] ?? '';
    if ($city === 'Others') {
        $city = trim($_POST['otherCity'] ?? '');
    }

    // Validate required fields
    $errors = [];
    if (empty($studentType)) $errors[] = "Student type is required";
    if (empty($fullName)) $errors[] = "Full name is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if ($age < 12 || $age > 99) $errors[] = "Age must be between 12 and 99";
    if (empty($gradeLevel)) $errors[] = "Grade level is required";
    if (($gradeLevel === '11' || $gradeLevel === '12') && empty($strand)) $errors[] = "Strand is required for Senior High School";
    if (empty($city)) $errors[] = "City is required";

    if (empty($errors)) {
        try {
            // Insert data into database
            $stmt = $pdo->prepare("INSERT INTO student_info 
                (student_type, full_name, gender, age, grade_level, strand, city, psa, form137, good_moral, card) 
                VALUES (:student_type, :full_name, :gender, :age, :grade_level, :strand, :city, :psa, :form137, :good_moral, :card)");

            $stmt->execute([
                ':student_type' => $studentType,
                ':full_name' => $fullName,
                ':gender' => $gender,
                ':age' => $age,
                ':grade_level' => $gradeLevel,
                ':strand' => $strand,
                ':city' => $city,
                ':psa' => $PSA,
                ':form137' => $Form137,
                ':good_moral' => $GoodMoral,
                ':card' => $Card
            ]);

            $success = "Student information submitted successfully!";
            $_POST = []; // Clear form
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon Configuration -->
    <!-- Standard ICO format (fallback) -->
    <link rel="icon" href="img/pdb.png" type="image/x-icon">
    
    <!-- Modern browsers (PNG format) -->
    <link rel="icon" type="image/png" sizes="32x32" href="img/pdb.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/pdb.png">
    
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    
    <!-- Windows Metro -->
    <meta name="msapplication-TileColor" content="#0d265c">
    <meta name="msapplication-TileImage" content="img/pdb.png">
    <title>Student Information Form</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --primary-blue: #4361ee;
            --secondary-blue: rgb(0, 0, 0);
            --light-blue: #4cc9f0;
            --background: #f8f9fa;
            --card-bg: #ffffff;
            --text: #2b2d42;
            --border: #e9ecef;
            --highlight: #f72585;
            --success: #4caf50;
            --error: #f44336;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background);
            margin: 0;
            padding: 0;
            color: var(--text);
            line-height: 1.6;
            background-image: linear-gradient(135deg,rgb(27, 64, 119) 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .form-container {
            max-width: 700px;
            margin: 30px;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary-blue), var(--light-blue));
        }
        
        h1 {
            text-align: center;
            color: var(--secondary-blue);
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 32px;
            position: relative;
            padding-bottom: 15px;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--light-blue));
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--secondary-blue);
            font-size: 15px;
        }
        
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: var(--light-blue);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .radio-option, .checkbox-option {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
        }
        
        .radio-option input[type="radio"],
        .checkbox-option input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .documents-group {
            border-top: 1px solid var(--border);
            padding-top: 25px;
            margin-top: 25px;
        }
        
        .documents-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .checkbox-option {
            margin-bottom: 8px;
        }
        
        button {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: white;
            border: none;
            padding: 16px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            text-transform: uppercase;
        }
        
        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(58, 12, 163, 0.2);
            background: linear-gradient(135deg, var(--secondary-blue), var(--primary-blue));
        }
        
        button:active {
            transform: translateY(-1px);
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234361ee' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .error-list {
            margin: 0;
            padding-left: 20px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 30px;
                margin: 20px;
            }
            
            .documents-options {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .radio-group {
                flex-direction: column;
                gap: 12px;
            }
            
            h1 {
                font-size: 26px;
            }
        }
        
        /* Floating label effect */
        .floating-label {
            position: relative;
        }
        
        .floating-label label {
            position: absolute;
            top: 14px;
            left: 18px;
            color: #999;
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 5px;
        }
        
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: var(--primary-blue);
        }
        
        /* Strand container initially hidden */
        #strandContainer {
            display: none;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Student Information Form</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Please fix these errors:</strong>
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <!-- Student Type -->
            <div class="form-group">
                <label for="studentType">Student Type</label>
                <select id="studentType" name="studentType" required>
                    <option value="" disabled selected>Select student type</option>
                    <option value="new" <?= isset($_POST['studentType']) && $_POST['studentType'] === 'new' ? 'selected' : '' ?>>New Enrollee</option>
                    <option value="transferee" <?= isset($_POST['studentType']) && $_POST['studentType'] === 'transferee' ? 'selected' : '' ?>>Transferee</option>
                    <option value="returnee" <?= isset($_POST['studentType']) && $_POST['studentType'] === 'returnee' ? 'selected' : '' ?>>Returnee</option>
                </select>
            </div>
            
            <!-- Full Name -->
            <div class="form-group floating-label">
                <input type="text" id="fullname" name="fullname" required placeholder=" " value="<?= isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : '' ?>">
                <label for="fullname">Full Name</label>
            </div>
            
            <!-- Gender -->
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="male" name="gender" value="male" required <?= isset($_POST['gender']) && $_POST['gender'] === 'male' ? 'checked' : (empty($_POST) ? 'checked' : '') ?>>
                        <label for="male">Male</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="female" name="gender" value="female" <?= isset($_POST['gender']) && $_POST['gender'] === 'female' ? 'checked' : '' ?>>
                        <label for="female">Female</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="other" name="gender" value="other" <?= isset($_POST['gender']) && $_POST['gender'] === 'other' ? 'checked' : '' ?>>
                        <label for="other">Other</label>
                    </div>
                </div>
            </div>
            
            <!-- Age -->
            <div class="form-group">
            <label>Age</label>
                <input type="number" id="age" name="age" required min="12" max="99" placeholder=" " value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>">
           
            </div>
            
            <!-- Grade Level -->
            <div class="form-group">
                <label for="gradeLevel">Grade Level</label>
                <select id="gradeLevel" name="gradeLevel" required onchange="toggleStrandField(this.value)">
                    <option value="" disabled selected>Select grade level</option>
                    <option value="7" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '7' ? 'selected' : '' ?>>Grade 7</option>
                    <option value="8" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '8' ? 'selected' : '' ?>>Grade 8</option>
                    <option value="9" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '9' ? 'selected' : '' ?>>Grade 9</option>
                    <option value="10" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '10' ? 'selected' : '' ?>>Grade 10</option>
                    <option value="11" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '11' ? 'selected' : '' ?>>Grade 11</option>
                    <option value="12" <?= isset($_POST['gradeLevel']) && $_POST['gradeLevel'] === '12' ? 'selected' : '' ?>>Grade 12</option>
                </select>
            </div>
            
            <!-- Strand (shown only for grades 11-12) -->
            <div class="form-group" id="strandContainer">
                <label for="strand">Strand</label>
                <select id="strand" name="strand">
                    <option value="" disabled selected>Select your strand</option>
                    <option value="STEM" <?= isset($_POST['strand']) && $_POST['strand'] === 'STEM' ? 'selected' : '' ?>>STEM</option>
                    <option value="ABM" <?= isset($_POST['strand']) && $_POST['strand'] === 'ABM' ? 'selected' : '' ?>>ABM</option>
                </select>
            </div>
            
            <!-- City Dropdown with "Others" option -->
            <div class="form-group">
                <label for="city">City</label>
                <select id="city" name="city" required onchange="showOtherCityInput(this)">
                    <option value="" disabled selected>Select your city</option>
                    <option value="Quezon City" <?= isset($_POST['city']) && $_POST['city'] === 'Quezon City' ? 'selected' : '' ?>>Quezon City</option>
                    <option value="Valenzuela City" <?= isset($_POST['city']) && $_POST['city'] === 'Valenzuela City' ? 'selected' : '' ?>>Valenzuela City</option>
                    <option value="Caloocan City" <?= isset($_POST['city']) && $_POST['city'] === 'Caloocan City' ? 'selected' : '' ?>>Caloocan City</option>
                    <option value="Bulacan" <?= isset($_POST['city']) && $_POST['city'] === 'Bulacan' ? 'selected' : '' ?>>Bulacan</option>
                    <option value="Others" <?= isset($_POST['city']) && $_POST['city'] === 'Others' ? 'selected' : '' ?>>Others (please specify)</option>
                </select>
            </div>
            
            <!-- Other City Input (hidden by default) -->
            <div class="form-group floating-label" id="otherCityContainer" style="<?= (isset($_POST['city']) && $_POST['city'] === 'Others') ? '' : 'display: none;' ?>">
                <input type="text" id="otherCity" name="otherCity" placeholder=" " 
                       value="<?= (isset($_POST['city']) && $_POST['city'] === 'Others' && isset($_POST['otherCity'])) ? htmlspecialchars($_POST['otherCity']) : '' ?>"
                       <?= (isset($_POST['city']) && $_POST['city'] === 'Others') ? 'required' : '' ?>>
                <label for="otherCity">Specify City</label>
            </div>
            
            <!-- Documents - Changed to Checkboxes -->
            <div class="form-group documents-group">
                <label>Documents Submitted (Check all that apply)</label>
                <div class="documents-options">
                    <div class="checkbox-option">
                        <input type="checkbox" id="psa" name="PSA" value="1" <?= isset($_POST['PSA']) ? 'checked' : '' ?>>
                        <label for="psa">PSA</label>
                    </div>
                    <div class="checkbox-option">
                        <input type="checkbox" id="form137" name="Form137" value="1" <?= isset($_POST['Form137']) ? 'checked' : '' ?>>
                        <label for="form137">Form 137</label>
                    </div>
                    <div class="checkbox-option">
                        <input type="checkbox" id="goodmoral" name="Good_moral" value="1" <?= isset($_POST['Good_moral']) ? 'checked' : '' ?>>
                        <label for="goodmoral">Good Moral</label>
                    </div>
                    <div class="checkbox-option">
                        <input type="checkbox" id="card" name="Card" value="1" <?= isset($_POST['Card']) ? 'checked' : '' ?>>
                        <label for="card">Card</label>
                    </div>
                </div>
            </div>
            
            <button type="submit">Submit Application</button>
        </form>
    </div>

<?php if (isset($success)): ?>
    <script>
        window.onload = function () {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $success ?>',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                timer: 3000,  // Auto close after 3 seconds
                timerProgressBar: true,
                willClose: () => {
                    window.location.href = 'https://jrsprog.github.io/PDBNHS/enrollment.html'; // Redirect to dashboard
                }
            });
        };
    </script>
<?php endif; ?>     

    <script>
        function showOtherCityInput(select) {
            const otherCityContainer = document.getElementById('otherCityContainer');
            const otherCityInput = document.getElementById('otherCity');
            
            if (select.value === 'Others') {
                otherCityContainer.style.display = 'block';
                otherCityInput.required = true;
            } else {
                otherCityContainer.style.display = 'none';
                otherCityInput.required = false;
                otherCityInput.value = ''; // Clear the value when not "Others"
            }
        }
        
        function toggleStrandField(gradeLevel) {
            const strandContainer = document.getElementById('strandContainer');
            const strandSelect = document.getElementById('strand');
            
            if (gradeLevel === '11' || gradeLevel === '12') {
                strandContainer.style.display = 'block';
                strandSelect.required = true;
            } else {
                strandContainer.style.display = 'none';
                strandSelect.required = false;
                strandSelect.value = ''; // Clear the value when not needed
            }
        }
        
        // Initialize on page load in case of form errors
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('city');
            if (citySelect) {
                showOtherCityInput(citySelect);
            }
            
            const gradeLevelSelect = document.getElementById('gradeLevel');
            if (gradeLevelSelect && gradeLevelSelect.value) {
                toggleStrandField(gradeLevelSelect.value);
            }
            
            // Also check if we need to show strand from previous submission
            <?php if (isset($_POST['gradeLevel']) && ($_POST['gradeLevel'] === '11' || $_POST['gradeLevel'] === '12')): ?>
                document.getElementById('strandContainer').style.display = 'block';
            <?php endif; ?>
        });
    </script>
</body>
</html>
