<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['modelFile'])) {
    $uploadDir = 'models/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // إنشاء المجلد إذا لم يكن موجودًا
    }

    $fileName = basename($_FILES['modelFile']['name']);
    $uploadFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['modelFile']['tmp_name'], $uploadFilePath)) {
        echo json_encode(["message" => "تم الرفع بنجاح!", "filePath" => $uploadFilePath]);
    } else {
        echo json_encode(["message" => "فشل في الرفع!"]);
    }
} else {
    echo json_encode(["message" => "طلب غير صالح."]);
}
?>
