<?php
$token = "...";
$chat_id = "...";

// order.php
header('Content-Type: application/json; charset=utf-8');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']); 
    exit;
}

$name = htmlspecialchars($data['name'] ?? '');
$phone = htmlspecialchars($data['phone'] ?? '');
$comment = htmlspecialchars($data['comment'] ?? '');
$pickup = $data['pickup'] ?? false;
$address = $pickup ? "Самовывоз" : htmlspecialchars($data['address'] ?? '');
$cart = $data['cart'] ?? [];

$cartText = "";
$totalSum = 0;
foreach ($cart as $item) {
    $qty = intval($item['qty'] ?? 1);
    $price = floatval($item['price'] ?? 0);
    $sum = $qty * $price;
    $totalSum += $sum;
    $title = htmlspecialchars($item['title'] ?? '');
    $cartText .= "• $title — $qty шт — " . number_format($sum, 2, ',', ' ') . " ₽\n";
}

$message = "🛒 Новый заказ!\n\n"
    . "👤 Имя: $name\n"
    . "📞 Телефон: $phone\n"
    . "📍 Адрес: $address\n"
    . "💬 Комментарий: $comment\n\n"
    . "🛍 Товар(ы):\n$cartText"
    . "💰 Итого: " . number_format($totalSum, 2, ',', ' ') . " ₽";
    
$url = "https://api.telegram.org/bot$token/sendMessage";
$params = [
    "chat_id" => $chat_id,
    "text" => $message,
    "parse_mode" => "HTML"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Здесь вставь логику сохранения заказа в БД (RedBean)
// Пример (упрощённый):
/*
require 'rb.php';
R::setup(...);

$order = R::dispense('orders');
$order->name = $name;
$order->phone = $phone;
$order->comment = $data['comment'] ?? '';
$order->created_at = date('Y-m-d H:i:s');
$order->items = json_encode($cart);
$id = R::store($order);
*/

echo json_encode(['success' => true, 'message' => 'Заказ принят. Спасибо!']);
