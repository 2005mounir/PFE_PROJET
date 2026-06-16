<?php
require_once 'config.php';

//get shearch value
$type = $_GET['type'] ?? '';
$price = $_GET['price'] ?? '';
$rent_type = $_GET['rent_type'] ?? '';

// get property from database
$sql = "SELECT p.*, i.image_path 
        FROM properties p 
        LEFT JOIN images i ON p.id_property = i.id_property 
        WHERE 1=1 AND p.status = 'approved'"; 



if (!empty($type)) $sql .= " AND p.type = " . $pdo->quote($type);
if (!empty($price)) $sql .= " AND p.price <= " . (float)$price;
if (!empty($rent_type)) $sql .= " AND p.rent_type = " . $pdo->quote($rent_type) ;

$sql .= " GROUP BY p.id_property ORDER BY p.id_property DESC";

$stmt = $pdo->query($sql);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rentora - Find Your Perfect Home</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <section class="hero">
        <div class="hero-content">
                 <h1>Find your perfect apartment or house in Rentora</h1>            
            <form action="index.php" method="GET" class="search-bar">
                <select name="type">
                    <option value="">Property Type</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Villa">Villa</option>
                    <option value="Studio">Studio</option>
                    <option value="Commercial">Commercial</option>
                    <option value="House">House</option>
                </select>

                <input type="number" name="price" placeholder="Max Price (DH)">

                <select name="rent_type">
                    <option value="">Rent Period</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>

                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <main>
        <h2>Featured Listings</h2>
        <div class="listings-grid">
            <?php foreach ($properties as $prop): ?>
                <a href="detailsProperty.php?id=<?php echo $prop['id_property']; ?>" class="card">
                    <img src="<?php echo $prop['image_path'] ?? 'default.jpg'; ?>" alt="Property">
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($prop['title']); ?></h3>
                        <p class="price"><?php echo number_format($prop['price'], 2); ?> DH / <?php echo $prop['rent_type']; ?></p>
                        <p><?php echo $prop['rooms']; ?> Rooms · <?php echo $prop['type']; ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

