<?php
$body = file_get_contents('php://input');
$request = json_decode($body, true);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=tugas35', 'root', ''); // Adjust username and password as needed
    $query = $pdo->prepare("INSERT INTO students(nik, nama) VALUES(:nik, :name)");
    $query->bindValue(':name', $request['name'], PDO::PARAM_STR);
    $query->bindValue(':nik', $request['nik'], PDO::PARAM_STR);

    $result = $query->execute();
    $id = $pdo->lastInsertId();

    if ($result) {
        $query = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $student = $query->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => true,
            "student" => $student
        ]);
    } else {
        echo json_encode(["status" => false, "error" => "Failed to save student data."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => false, "error" => $e->getMessage()]);
}
?>
