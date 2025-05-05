<?php
session_start();

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'student_registrations';

// Initialize variables
$success = '';
$error = '';
$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Initialize entered values
$entered_values = [
    // Personal Information
    'first_name' => '',
    'middle_name' => '',
    'last_name' => '',
    'extension_name' => '',
    'birthdate' => '',
    'sex' => '',
    'age' => '',
    'place_of_birth' => '',
    'mother_tongue' => '',
    'ip_community' => 'No',
    'ip_specify' => '',
    '4ps_beneficiary' => 'No',
    '4ps_id' => '',
    
    // Address Information
    'house_no' => '',
    'street' => '',
    'barangay' => '',
    'municipality_city' => '',
    'province' => '',
    'country' => 'Philippines',
    'zip_code' => ''
];

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($db_name);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS student_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    extension_name VARCHAR(10),
    birthdate VARCHAR(10) NOT NULL,
    sex VARCHAR(10) NOT NULL,
    age INT NOT NULL,
    place_of_birth VARCHAR(100) NOT NULL,
    mother_tongue VARCHAR(50) NOT NULL,
    ip_community VARCHAR(3) NOT NULL,
    ip_specify VARCHAR(100),
    `4ps_beneficiary` VARCHAR(3) NOT NULL,
    `4ps_id` VARCHAR(50),
    house_no VARCHAR(20) NOT NULL,
    street VARCHAR(100) NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    municipality_city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    zip_code VARCHAR(10) NOT NULL,
    registration_date DATETIME NOT NULL
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid request. Please try again.";
    } else {
        // Sanitize and validate input
        if ($current_step === 1) {
            // Personal Information Step
            $entered_values['first_name'] = trim($_POST['first_name']);
            $entered_values['middle_name'] = trim($_POST['middle_name']);
            $entered_values['last_name'] = trim($_POST['last_name']);
            $entered_values['extension_name'] = trim($_POST['extension_name']);
            $entered_values['birthdate'] = trim($_POST['birthdate']);
            $entered_values['sex'] = trim($_POST['sex']);
            $entered_values['age'] = trim($_POST['age']);
            $entered_values['place_of_birth'] = trim($_POST['place_of_birth']);
            $entered_values['mother_tongue'] = trim($_POST['mother_tongue']);
            $entered_values['ip_community'] = trim($_POST['ip_community']);
            $entered_values['ip_specify'] = trim($_POST['ip_specify']);
            $entered_values['4ps_beneficiary'] = trim($_POST['4ps_beneficiary']);
            $entered_values['4ps_id'] = trim($_POST['4ps_id']);

            // Validation
            $errors = [];
            if (empty($entered_values['first_name'])) $errors[] = "First name is required";
            if (empty($entered_values['last_name'])) $errors[] = "Last name is required";
            if (empty($entered_values['birthdate'])) {
                $errors[] = "Birthdate is required";
            } elseif (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $entered_values['birthdate']) || !strtotime(str_replace('/', '-', $entered_values['birthdate']))) {
                $errors[] = "Birthdate must be in valid MM/DD/YYYY format";
            }
            if (empty($entered_values['sex'])) $errors[] = "Sex is required";
            if (empty($entered_values['age'])) $errors[] = "Age is required";
            if (empty($entered_values['place_of_birth'])) $errors[] = "Place of birth is required";
            if (empty($entered_values['mother_tongue'])) $errors[] = "Mother tongue is required";
            if ($entered_values['ip_community'] === 'Yes' && empty($entered_values['ip_specify'])) $errors[] = "Please specify your IP Community";
            if ($entered_values['4ps_beneficiary'] === 'Yes' && empty($entered_values['4ps_id'])) $errors[] = "Please provide 4Ps Household ID Number";

            if (empty($errors)) {
                $_SESSION['form_data'] = $entered_values;
                header("Location: ?step=2");
                exit();
            } else {
                $error = implode("<br>", $errors);
            }
        } elseif ($current_step === 2) {
            // Address Information Step
            $entered_values['house_no'] = trim($_POST['house_no']);
            $entered_values['street'] = trim($_POST['street']);
            $entered_values['barangay'] = trim($_POST['barangay']);
            $entered_values['municipality_city'] = trim($_POST['municipality_city']);
            $entered_values['province'] = trim($_POST['province']);
            $entered_values['country'] = trim($_POST['country']);
            $entered_values['zip_code'] = trim($_POST['zip_code']);

            // Combine with personal info from session
            if (isset($_SESSION['form_data'])) {
                $entered_values = array_merge($_SESSION['form_data'], $entered_values);
            }

            // Validation
            $errors = [];
            if (empty($entered_values['house_no'])) $errors[] = "House number is required";
            if (empty($entered_values['street'])) $errors[] = "Street is required";
            if (empty($entered_values['barangay'])) $errors[] = "Barangay is required";
            if (empty($entered_values['municipality_city'])) $errors[] = "Municipality/City is required";
            if (empty($entered_values['province'])) $errors[] = "Province is required";
            if (empty($entered_values['zip_code'])) {
                $errors[] = "Zip code is required";
            } elseif (!preg_match('/^\d{4}$/', $entered_values['zip_code'])) {
                $errors[] = "Zip code must be 4 digits";
            }

            if (empty($errors)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO student_registrations 
                    (first_name, middle_name, last_name, extension_name, birthdate, sex, age, 
                    place_of_birth, mother_tongue, ip_community, ip_specify, 4ps_beneficiary, 4ps_id,
                    house_no, street, barangay, municipality_city, province, country, zip_code, registration_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

                if ($stmt) {
                    $stmt->bind_param(
                        "ssssssisssssssssssss",
                        $entered_values['first_name'],
                        $entered_values['middle_name'],
                        $entered_values['last_name'],
                        $entered_values['extension_name'],
                        $entered_values['birthdate'],
                        $entered_values['sex'],
                        $entered_values['age'],
                        $entered_values['place_of_birth'],
                        $entered_values['mother_tongue'],
                        $entered_values['ip_community'],
                        $entered_values['ip_specify'],
                        $entered_values['4ps_beneficiary'],
                        $entered_values['4ps_id'],
                        $entered_values['house_no'],
                        $entered_values['street'],
                        $entered_values['barangay'],
                        $entered_values['municipality_city'],
                        $entered_values['province'],
                        $entered_values['country'],
                        $entered_values['zip_code']
                    );

                    if ($stmt->execute()) {
                        $success = "Registration submitted successfully!";
                        $current_step = 3;
                        unset($_SESSION['form_data']);
                    } else {
                        $error = "Database error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
            } else {
                $error = implode("<br>", $errors);
            }
        }
    }
}

// Load data from session if available
if ($current_step === 2 && isset($_SESSION['form_data'])) {
    $entered_values = array_merge($entered_values, $_SESSION['form_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* [Previous CSS styles remain exactly the same] */
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --danger: #ef4444;
            --success: #10b981;
            --rounded: 0.5rem;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: var(--light);
            padding: 2rem;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: var(--rounded);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 1rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: <?= $current_step >= 1 ? 'var(--primary)' : '#e5e7eb' ?>;
            color: <?= $current_step >= 1 ? 'white' : 'var(--gray)' ?>;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }
        .step-label {
            margin-top: 0.5rem;
            color: <?= $current_step >= 1 ? 'var(--primary)' : 'var(--gray)' ?>;
            font-weight: <?= $current_step >= 1 ? '600' : 'normal' ?>;
        }
        .step-connector {
            height: 2px;
            width: 50px;
            background-color: <?= $current_step >= 2 ? 'var(--primary)' : '#e5e7eb' ?>;
            margin-top: 20px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--rounded);
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        .required::after {
            content: " *";
            color: var(--danger);
        }
        .btn {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: var(--rounded);
            cursor: pointer;
        }
        .btn:hover {
            background-color: var(--secondary);
        }
        .btn-next {
            float: right;
        }
        .btn-prev {
            float: left;
            background-color: var(--gray);
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--rounded);
        }
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .hidden {
            display: none;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Senior High School Registration</h1>
            <p>Please fill out the form completely</p>
        </div>
        
        <div class="step-indicator">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-label">Personal Info</div>
            </div>
            <div class="step-connector"></div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">Address Info</div>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <!-- Step 1: Personal Information -->
            <div id="step1" <?= $current_step !== 1 ? 'class="hidden"' : '' ?>>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name" class="required">First Name</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($entered_values['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" name="middle_name" value="<?= htmlspecialchars($entered_values['middle_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="required">Last Name</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($entered_values['last_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="extension_name">Extension Name (Jr., III)</label>
                        <input type="text" name="extension_name" value="<?= htmlspecialchars($entered_values['extension_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="birthdate" class="required">Birthdate (MM/DD/YYYY)</label>
                        <input type="text" name="birthdate" value="<?= htmlspecialchars($entered_values['birthdate']) ?>" placeholder="MM/DD/YYYY" required>
                    </div>
                    <div class="form-group">
                        <label for="sex" class="required">Sex</label>
                        <select name="sex" required>
                            <option value="">Select</option>
                            <option value="Male" <?= $entered_values['sex'] === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $entered_values['sex'] === 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="age" class="required">Age</label>
                        <input type="number" name="age" min="10" max="30" value="<?= htmlspecialchars($entered_values['age']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="place_of_birth" class="required">Place of Birth</label>
                        <input type="text" name="place_of_birth" value="<?= htmlspecialchars($entered_values['place_of_birth']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mother_tongue" class="required">Mother Tongue</label>
                        <input type="text" name="mother_tongue" value="<?= htmlspecialchars($entered_values['mother_tongue']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ip_community" class="required">Belong to IP Community?</label>
                        <select name="ip_community" id="ip_community" required>
                            <option value="No" <?= $entered_values['ip_community'] === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Yes" <?= $entered_values['ip_community'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                    <div class="form-group" id="ip_specify_group" style="<?= $entered_values['ip_community'] !== 'Yes' ? 'display: none;' : '' ?>">
                        <label for="ip_specify" class="<?= $entered_values['ip_community'] === 'Yes' ? 'required' : '' ?>">Specify IP Community</label>
                        <input type="text" name="ip_specify" id="ip_specify" value="<?= htmlspecialchars($entered_values['ip_specify']) ?>" <?= $entered_values['ip_community'] === 'Yes' ? 'required' : '' ?>>
                    </div>
                    <div class="form-group">
                        <label for="4ps_beneficiary" class="required">4Ps Beneficiary?</label>
                        <select name="4ps_beneficiary" id="4ps_beneficiary" required>
                            <option value="No" <?= $entered_values['4ps_beneficiary'] === 'No' ? 'selected' : '' ?>>No</option>
                            <option value="Yes" <?= $entered_values['4ps_beneficiary'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>
                    <div class="form-group" id="4ps_id_group" style="<?= $entered_values['4ps_beneficiary'] !== 'Yes' ? 'display: none;' : '' ?>">
                        <label for="4ps_id" class="<?= $entered_values['4ps_beneficiary'] === 'Yes' ? 'required' : '' ?>">4Ps ID Number</label>
                        <input type="text" name="4ps_id" id="4ps_id" value="<?= htmlspecialchars($entered_values['4ps_id']) ?>" <?= $entered_values['4ps_beneficiary'] === 'Yes' ? 'required' : '' ?>>
                    </div>
                </div>
                <div class="form-group full-width">
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <button type="submit" class="btn btn-next">Next</button>
                </div>
            </div>

    <script>
        // Toggle conditional fields
        document.getElementById('ip_community').addEventListener('change', function() {
            const ipSpecifyGroup = document.getElementById('ip_specify_group');
            const ipSpecifyInput = document.getElementById('ip_specify');
            
            if (this.value === 'Yes') {
                ipSpecifyGroup.style.display = 'block';
                ipSpecifyInput.required = true;
            } else {
                ipSpecifyGroup.style.display = 'none';
                ipSpecifyInput.required = false;
            }
        });
        
        document.getElementById('4ps_beneficiary').addEventListener('change', function() {
            const fourPsIdGroup = document.getElementById('4ps_id_group');
            const fourPsIdInput = document.getElementById('4ps_id');
            
            if (this.value === 'Yes') {
                fourPsIdGroup.style.display = 'block';
                fourPsIdInput.required = true;
            } else {
                fourPsIdGroup.style.display = 'none';
                fourPsIdInput.required = false;
            }
        });
        
        // Format birthdate input
        document.querySelector('input[name="birthdate"]').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 2) value = value.substring(0, 2) + '/' + value.substring(2);
            if (value.length > 5) value = value.substring(0, 5) + '/' + value.substring(5, 9);
            this.value = value;
        });
        
        // Client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            if (document.getElementById('step1') && !document.getElementById('step1').classList.contains('hidden')) {
                const requiredFields = document.querySelectorAll('#step1 [required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = 'red';
                        isValid = false;
                    }
                });
                
                const birthdate = document.querySelector('input[name="birthdate"]');
                if (birthdate && !/^\d{2}\/\d{2}\/\d{4}$/.test(birthdate.value)) {
                    birthdate.style.borderColor = 'red';
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill all required fields correctly.');
                }
            }
        });
    </script>
</body>
</html>