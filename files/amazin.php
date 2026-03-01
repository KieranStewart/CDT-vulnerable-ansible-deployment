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
    $debug_query = "SELECT * FROM products WHERE name LIKE '%$term%'";
    
    $result = $conn->query($debug_query);

    if ($result) {
        while($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        // VULNERABILITY 2: Information Disclosure
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
        /* Reset and Base Styles */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #eaeded; color: #0f1111; }
        
        /* Header / Navbar */
        header { background-color: #131921; color: white; display: flex; align-items: center; padding: 10px 20px; gap: 20px; }
        .logo-container { display: flex; align-items: center; gap: 5px; text-decoration: none; color: white; margin-right: 15px; }
        .logo-icon { font-size: 32px; }
        .logo-text { font-size: 26px; font-weight: bold; font-style: italic; letter-spacing: -1px; }
        .logo-smile { color: #ff9900; font-style: normal; }
        
        /* Modern Search Bar */
        .search-form { display: flex; flex-grow: 1; max-width: 800px; border-radius: 4px; overflow: hidden; }
        /* VULNERABILITY 3: The input value directly echoes $term without sanitization */
        .search-input { flex-grow: 1; padding: 12px 15px; border: none; font-size: 16px; outline: none; }
        .search-btn { background-color: #febd69; border: none; padding: 10px 20px; cursor: pointer; font-size: 18px; color: #111; transition: background-color 0.2s; }
        .search-btn:hover { background-color: #f3a847; }
        
        /* Navigation Links (Mockup) */
        .nav-links { display: flex; gap: 20px; font-size: 14px; font-weight: bold; margin-left: auto; }
        .nav-links div { cursor: pointer; line-height: 1.2; }
        .nav-links .sub-text { font-size: 12px; font-weight: normal; display: block; }
        
        /* Main Content */
        main { max-width: 1200px; margin: 20px auto; padding: 20px; background-color: transparent; }
        .search-header { margin-bottom: 20px; font-weight: 400; font-size: 20px; }
        
        /* Product Grid */
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; }
        .product-card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; text-align: center; transition: box-shadow 0.2s; background: #fff; display: flex; flex-direction: column; }
        .product-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .product-icon { font-size: 72px; margin-bottom: 15px; }
        .product-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #007185; }
        .product-price { font-size: 28px; color: #B12704; margin-bottom: 10px; }
        .product-price small { font-size: 14px; position: relative; top: -8px; }
        .product-desc { font-size: 14px; color: #565959; margin-bottom: 20px; line-height: 1.4; flex-grow: 1; }
        .add-to-cart { background-color: #ffd814; border: 1px solid #fcd200; border-radius: 20px; padding: 10px 15px; cursor: pointer; width: 100%; font-weight: bold; transition: background-color 0.2s; }
        .add-to-cart:hover { background-color: #f7ca00; }
        
        /* Debug / Hacker Area */
        .debug-area { margin-top: 40px; padding: 15px; background-color: #fff; border: 1px solid #ddd; border-left: 5px solid #dc3545; font-family: monospace; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .error-msg { color: #dc3545; font-weight: bold; margin-top: 10px; font-size: 16px; }
    </style>
</head>
<body>
    <header>
        <a href="?" class="logo-container">
            <div class="logo-icon">📦</div>
            <div class="logo-text">Amazin<span class="logo-smile">➔</span></div>
        </a>
        
        <form method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Search Amazin..." value="<?php echo $term; ?>">
            <button type="submit" class="search-btn">🔍</button>
        </form>

        <div class="nav-links">
            <div><span class="sub-text">Hello, Sign in</span>Account & Lists</div>
            <div><span class="sub-text">Returns</span>& Orders</div>
            <div style="font-size: 16px; display: flex; align-items: flex-end;">🛒 Cart</div>
        </div>
    </header>

    <main>
        <?php if ($term === ''): ?>
            <div style="background: #fff; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #ddd;">
                <h2>Welcome to Amazin!</h2>
                <p style="margin-top: 10px; color: #565959;">Try searching for "Mouse", "Keyboard", or "Mug" to see our deals.</p>
                <p style="margin-top: 30px; font-size: 12px; color: #888;">* Amazin Sub-Prime™ members get free 5-week shipping.</p>
            </div>
        <?php else: ?>
            <h2 class="search-header">Results for: "<strong><?php echo $term; ?></strong>"</h2>

            <?php if (count($results) > 0): ?>
                <div class="product-grid">
                    <?php foreach ($results as $item): ?>
                        <div class="product-card">
                            <div class="product-icon">
                                <?php 
                                    if (stripos($item['name'], 'mouse') !== false) echo '🖱️';
                                    elseif (stripos($item['name'], 'keyboard') !== false) echo '⌨️';
                                    elseif (stripos($item['name'], 'mug') !== false) echo '☕';
                                    elseif (stripos($item['name'], 'flag') !== false) echo '🚩';
                                    else echo '🛍️';
                                ?>
                            </div>
                            <div class="product-title"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="product-price">
                                <small>$</small><?php echo htmlspecialchars(floor($item['price'])); ?><small><?php echo substr(number_format($item['price'], 2), -2); ?></small>
                            </div>
                            <div class="product-desc"><?php echo htmlspecialchars($item['description']); ?></div>
                            <button class="add-to-cart">Add to Cart</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php if (!isset($db_error)): ?>
                    <div style="background: #fff; padding: 40px; text-align: center; border-radius: 8px; border: 1px solid #ddd;">
                        <div style="font-size: 48px; margin-bottom: 20px;">🤷</div>
                        <h3>No results for <?php echo $term; ?>.</h3>
                        <p style="color: #565959; margin-top: 10px;">Check your spelling or try more general terms.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="debug-area">
                <strong>[Dev Debug] Current SQL Query:</strong><br>
                <pre style="margin-top: 5px; white-space: pre-wrap; word-wrap: break-word;"><?php echo htmlspecialchars($debug_query); ?></pre>
                
                <?php if (isset($db_error)): ?>
                    <div class="error-msg">FATAL DATABASE ERROR: <?php echo htmlspecialchars($db_error); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>