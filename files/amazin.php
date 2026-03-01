<?php
// database creds matching ansible deploy // TODO look for a way to import automatically to reduce number of changes that need to be made
$db_host = 'localhost';
$db_user = 'amazin_user';
$db_pass = 'insecure_db_password';
$db_name = 'amazin_db';

// mysql conn
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// verify
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$term = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];
$debug_query = "";

if ($term !== '') {
    $debug_query = "SELECT * FROM products WHERE name LIKE '%$term%'";
    
    $result = $conn->query($debug_query);

    if ($result) {
        while($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        $db_error = $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazin - Spend Less, Expect Less.</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #eaeded; color: #0f1111; }
        
        header { background-color: #131921; color: white; display: flex; align-items: center; padding: 10px 20px; gap: 20px; }
        .logo-text {font-size: 26px; font-weight: bold; font-style: italic; letter-spacing: -1px;}

        .search-form { display: flex; flex-grow: 1; max-width: 800px; border-radius: 4px; overflow: hidden; }
        .search-input { flex-grow: 1; padding: 12px 15px; border: none; font-size: 16px; outline: none; }
        .search-btn { background-color: #febd69; border: none; padding: 10px 20px; cursor: pointer; font-size: 18px; color: #111; transition: background-color 0.2s; }
        .search-btn:hover { background-color: #f3a847; }

        .item {
            background-color: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <header>
        <a href="?">
            <div class="logo-text">📦 Amazin</div>
        </a>
        
        <form method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Search Amazin..." value="<?php echo $term; ?>">
            <button type="submit" class="search-btn">🔍</button>
        </form>
    </header>

    <main>
        <?php if ($term !== ''): ?>
        <h2>Results for: <?php echo $term; ?></h2>

        <?php if (isset($db_error)): ?>
            <p style="color: red;">Database Error: <?php echo $db_error; ?></p>
        <?php endif; ?>

        <?php if (count($results) > 0): ?>
            <ul>
            <?php foreach ($results as $item): ?>
                <div class="item">
                    <strong><?php echo $item['name']; ?></strong> - 
                    $<?php echo $item['price']; ?><br>
                    <i><?php echo $item['description']; ?></i>
                </div>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php if (!isset($db_error)): ?>
                <p>No products found.</p>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    </main>
</body>
</html>