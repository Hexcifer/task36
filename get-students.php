<?php
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$offset = ($page - 1) * $limit;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tugas36', 'root', '');
    $totalQuery = $pdo->query("SELECT COUNT(*) FROM student");
    $totalRows = $totalQuery->fetchColumn();

    $query = $pdo->prepare("SELECT * FROM student LIMIT :limit OFFSET :offset");
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();

    $students = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "students" => $students,
        "totalPages" => ceil($totalRows / $limit),
    ]);
} catch (Exception $e) {
    echo json_encode(["status" => false, "error" => $e->getMessage()]);
}
?>
