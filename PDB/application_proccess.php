<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Pre-Registration | Paso De Blas NHS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #ef476f;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --danger: #ef4444;
            --success: #10b981;
            --rounded: 1rem;
            --shadow: 0 10px 20px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            margin: 0;
            padding: 2rem;
            line-height: 1.7;
            color: var(--dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: var(--rounded);
            box-shadow: var(--shadow);
            animation: fadeIn 1s ease-in;
        }

        h1, h2, h3 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        h2 {
            margin-top: 2rem;
        }

        p, li {
            font-size: 1.05rem;
            color: var(--gray);
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li::before {
            content: "🔥";
            margin-right: 8px;
            color: var(--accent);
        }

        .important {
            background: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--rounded);
            margin: 2rem 0;
            font-weight: 600;
            box-shadow: var(--shadow);
        }

        .steps {
            background: var(--light);
            border-left: 5px solid var(--accent);
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: var(--rounded);
        }

        .date-box {
            background: var(--secondary);
            color: white;
            padding: 1rem;
            margin-top: 2rem;
            border-radius: var(--rounded);
            text-align: center;
            box-shadow: var(--shadow);
        }

        .footer-note {
            font-size: 0.9rem;
            color: var(--gray);
            margin-top: 3rem;
            text-align: center;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>📋 Student Pre-Registration Process</h1>
    <p><strong>For Incoming Grade 11 and 12 Students (STEM/ABM)</strong></p>

    <h2>🏫 Who Can Pre-Register?</h2>
    <ul>
        <li>Students entering Grade 11 or 12.</li>
        <li>Available Strands: <strong>STEM</strong> or <strong>ABM</strong>.</li>
    </ul>

    <h2>📚 Requirements:</h2>
    <ul>
        <li>2x2 ID Photo (recent, white background)</li>
        <li>Photocopy of PSA Birth Certificate</li>
        <li>Report Card (SF9 Form)</li>
        <li>Certificate of Good Moral Character</li>
        <li>Proof of Address (Barangay Clearance / Utility Bill)</li>
        <li>Parent/Guardian Valid ID</li>
        <li>Vaccination Card (optional)</li>
    </ul>

    <div class="important">
        🔥 Remember: Incomplete requirements may delay your enrollment.
    </div>

    <h2>🖋️ Pre-Registration Steps:</h2>

    <div class="steps">
        <h3>1. Online Pre-Registration</h3>
        <p>Fill out the online form and upload required documents.</p>
    </div>

    <div class="steps">
        <h3>2. Verification</h3>
        <p>Registrar reviews your submission (3–5 days).</p>
    </div>

    <div class="steps">
        <h3>3. Status Notification</h3>
        <p>You'll be notified if you're approved, incomplete, or need to re-submit.</p>
    </div>

    <div class="steps">
        <h3>4. Physical Enrollment</h3>
        <p>Bring original documents during physical enrollment day.</p>
    </div>

    <a href="pre-registration.php" style="text-decoration: none;">
    <div class="date-box">
        📅 Pre-Registration Now!: <br>
    </div>
</a>


    <div class="footer-note">
        🚀 <em>Pre-registration is the first step to your Senior High Journey at Paso De Blas NHS!</em>
    </div>

</div>

</body>
</html>
