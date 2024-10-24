<?php include 'header.php'?>
<?php include 'nav.php'?>
<?php include 'db.php'?>

<?php
session_start(); 

$errorMsg = $successMsg = "";

// Handle Create operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']); // Trim whitespace from the name
    $email = $_POST['email'];

    // Check if the name is empty or only whitespace
    if (empty($name)) {
        $_SESSION['error_message'] = "Invalid name!";
        header('Location: add-users');
        exit; // Ensure no further code is executed after the redirect
    }

    // Prepare a SQL query to check if the email already exists
    $checkSql = "SELECT email FROM users WHERE email = '$email'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // User with this email already exists
        $_SESSION['error_message'] = "Email is already in use.";
        header('Location: add-users');
        exit; // Ensure no further code is executed after the redirect
    } else {
        // Prepare the SQL query to insert the new user
        $sql = "INSERT INTO users (name, email, created_at) VALUES ('$name', '$email', NOW())";

        if ($conn->query($sql) === TRUE) {
            // Clear error message upon successful submission
            unset($_SESSION['error_message']);
            $successMsg = "New user added successfully.";
        } else {
            $errorMsg = "Error: " . $conn->error;
        }
    }

    // Redirect after processing the form to prevent form re-submission
    header('Location: add-users');
    exit; // Ensure no further code is executed after the redirect
}

// Check if there is an error message in the session and store it
if (isset($_SESSION['error_message'])) {
    $errorMsg = $_SESSION['error_message']; // Retrieve the error message
    unset($_SESSION['error_message']); // Clear it after retrieval
}

// SQL query to fetch users from the database
$sql = "SELECT user_id, name, email, created_at FROM users";
$result = $conn->query($sql);
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <style>
        table {
            width: 50% !important;
            border-collapse: collapse;
            margin: auto;
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }
        .form-container {
            margin-bottom: 20px;
            padding: 20px;
            text-align: center;
        }
        .alert-danger {
            color: red; /* Error message color */
        }
        .alert-success {
            color: green; /* Success message color */
        }
    </style>
</head>

<!-- Form for Adding Users -->
<div class="form-container">

    <!-- Display Error Message -->
    <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger">
            <?php echo $errorMsg; ?>
        </div>
    <?php endif; ?>
    
    <!-- Display Success Message -->
    <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success">
            <?php echo $successMsg; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add-users">
        <label>Name:</label><input type="text" name="name" required><br><br>
        <label>Email:</label><input type="email" name="email" required><br><br>
        <button type="submit" class="btn btn-outline-primary">Add user</button>
    </form>
</div>

<!-- Table containing users in the database -->
<h1>Registered Users</h1>

<table>
    <thead>
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
