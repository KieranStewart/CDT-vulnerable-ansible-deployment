<?php
// Database credentials matching the Ansible deployment
$db_host = 'localhost';
$db_user = 'amazin_user';
$db_pass = 'insecure_db_password';
$db_name = 'amazin_db';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$term = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];
$debug_query = "";

if ($term !== '') {
    // VULNERABILITY 1: SQL Injection
    // The user input ($term) is concatenated directly into the SQL string without parameterization.
    $debug_query = "SELECT * FROM products WHERE name LIKE '%$term%'";
    
    $result = $conn->query($debug_query);

    if ($result) {
        while($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        // VULNERABILITY 2: Information Disclosure
        // Echoing raw database errors to the user enables Error-Based SQL Injection.
        $db_error = $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Amazin</title>
</head>
<body>
    <h1>Amazin</h1>
    <form method="GET">
        <label>Search:</label>
        <input type="text" name="q" value="<?php echo $term; ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($term !== ''): ?>
        <h2>Results for: <?php echo $term; ?></h2>
        
        <p>Debug query:</p>
        <pre><?php echo $debug_query; ?></pre>

        <?php if (isset($db_error)): ?>
            <p style="color: red;">Database Error: <?php echo $db_error; ?></p>
        <?php endif; ?>

        <?php if (count($results) > 0): ?>
            <ul>
            <?php foreach ($results as $item): ?>
                <li>
                    <strong><?php echo $item['name']; ?></strong> - 
                    $<?php echo $item['price']; ?><br>
                    <i><?php echo $item['description']; ?></i>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php if (!isset($db_error)): ?>
                <p>No products found.</p>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>