<?php
include '../inc/db.php';

// SQL: Get menu items with sold count from restaurant_orders
$sql = "
SELECT mi.id, mi.name, mi.photo, COALESCE(SUM(ro.quantity),0) as sold
FROM menu_items mi
LEFT JOIN restaurant_orders ro ON mi.id = ro.food_id
GROUP BY mi.id
ORDER BY sold DESC
LIMIT 20
";

$result = $conn->query($sql);
$foods = [];

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $foods[] = $row;
  }
}

// Shuffle array to randomize popular foods
shuffle($foods);

// Return top 8 items as JSON
echo json_encode(array_slice($foods, 0, 8));
