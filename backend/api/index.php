<?php
// Enable CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit(0); // Respond with 200 OK for preflight requests
}

// Include database connection
require_once "db.php";

// Handle POST request (place order)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $name = $data["name"];
    $phone = $data["phone"];
    $meatType = $data["meatType"];
    $quantity = $data["quantity"];

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (name, phone, meat_type, quantity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $meatType, $quantity]);

        echo json_encode(["message" => "Order placed successfully"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to place order: " . $e->getMessage()]);
    }
}
// Handle GET request (fetch all orders or a specific order by ID)
elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Check if an ID is provided in the URL (e.g., /api/orders/1)
    $requestUri = $_SERVER["REQUEST_URI"];
    $uriParts = explode("/", trim($requestUri, "/"));
    $id = end($uriParts); // Get the last part of the URL (e.g., "1")

    try {
        if (is_numeric($id)) {
            // Fetch a specific order by ID
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                echo json_encode($order);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Order not found"]);
            }
        } else {
            // Fetch all orders
            $stmt = $pdo->query("SELECT * FROM orders");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($orders);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch orders: " . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method"]);
}
?>
