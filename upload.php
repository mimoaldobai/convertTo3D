<?php
header('Content-Type: application/json');

$target_dir = "models/";

// التأكد من وجود مجلد models أو إنشاؤه إذا لم يكن موجوداً
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if(isset($_FILES['modelFile'])){
    $target_file = $target_dir . basename($_FILES["modelFile"]["name"]);
    if (move_uploaded_file($_FILES["modelFile"]["tmp_name"], $target_file)) {
        echo json_encode(array("message" => "تم حفظ الملف في مجلد models."));
    } else {
        echo json_encode(array("message" => "حدث خطأ أثناء رفع الملف."));
    }
} else {
    echo json_encode(array("message" => "لم يتم استلام الملف."));
}
?>
