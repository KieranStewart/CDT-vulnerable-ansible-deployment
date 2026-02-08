<?php
// Vulnerability: unsanitized input directly in out
// TODO Vulnerability: database query unsanitized

function search($term) {
    // Pretend this is a database query
    return "SELECT * FROM products WHERE name LIKE '%$term%';";
}

$term = isset($_GET['q']) ? $_GET['q'] : '';

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
        <pre><?php echo search($term); ?></pre>
        <p>No products found (todo).</p>
    <?php endif; ?>
</body>
</html>
