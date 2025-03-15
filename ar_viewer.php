<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تجربة الواقع المعزز</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128/examples/js/loaders/GLTFLoader.js"></script>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            touch-action: none;
            font-family: Arial, sans-serif;
        }
        #canvas {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .buttons-container {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
        }
        .button {
            padding: 10px 20px;
            font-size: 18px;
            border: none;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.3s;
        }
        .button:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }
    </style>
</head>
<body>

<?php
if (isset($_GET['model']) && !empty($_GET['model'])) {
    $model_3d = htmlspecialchars($_GET['model']);
} else {
    die("<p>❌ خطأ: لم يتم تحديد النموذج ثلاثي الأبعاد.</p>");
}
?>

<video id="video" autoplay playsinline></video>
<canvas id="canvas"></canvas>

<!-- أزرار التكبير والتصغير -->
<div class="buttons-container">
    <button class="button" onclick="scaleModel(1.2)">🔍 تكبير</button>
    <button class="button" onclick="scaleModel(0.8)">🔎 تصغير</button>
</div>

<script>
    // إعداد Three.js
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas: document.getElementById("canvas"), alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    // تشغيل الكاميرا الخلفية
    const video = document.getElementById("video");
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => console.error("❌ لا يمكن تشغيل الكاميرا:", err));

    // تحميل النموذج ثلاثي الأبعاد
    let model;
    let modelScale = 1; // الحجم الافتراضي
    const loader = new THREE.GLTFLoader();
    
    loader.load("<?php echo $model_3d; ?>", function (gltf) {
        model = gltf.scene;
        model.scale.set(modelScale, modelScale, modelScale); // تعيين الحجم الأولي
        model.position.set(0, -1, -3);
        scene.add(model);
    });

    // إضافة إضاءة
    const light = new THREE.AmbientLight(0xFFFFFF, 1.5);
    scene.add(light);

    // ضبط موضع الكاميرا
    camera.position.z = 5;

    // التحكم في **تحريك** النموذج فقط (إزالة الدوران)
    let isDragging = false;
    let previousMousePosition = { x: 0, y: 0 };

    window.addEventListener("pointerdown", (event) => {
        isDragging = true;
        previousMousePosition.x = event.clientX;
        previousMousePosition.y = event.clientY;
    });

    window.addEventListener("pointermove", (event) => {
        if (!isDragging || !model) return;

        let deltaX = event.clientX - previousMousePosition.x;
        let deltaY = event.clientY - previousMousePosition.y;

        // ❌ إزالة التدوير ⬇️
        // model.rotation.y += deltaX * 0.005;
        // model.rotation.x += deltaY * 0.005;

        // ✅ السماح فقط بتحريك النموذج
        model.position.x += deltaX * 0.01; 
        model.position.y -= deltaY * 0.01;

        previousMousePosition.x = event.clientX;
        previousMousePosition.y = event.clientY;
    });

    window.addEventListener("pointerup", () => {
        isDragging = false;
    });

    // وظيفة تكبير وتصغير النموذج
    function scaleModel(scaleFactor) {
        if (!model) return; // التأكد من تحميل النموذج أولاً

        // تحديث الحجم
        modelScale *= scaleFactor;

        // تحديد الحد الأدنى والأقصى للحجم
        if (modelScale < 0.3) modelScale = 0.3;
        if (modelScale > 5) modelScale = 5;

        // تطبيق الحجم الجديد
        model.scale.set(modelScale, modelScale, modelScale);

        // إعادة تحديث المشهد بعد تغيير الحجم
        renderer.render(scene, camera);
    }

    // تشغيل التحريك المستمر للمشهد
    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }

    animate();

</script>

</body>
</html>
