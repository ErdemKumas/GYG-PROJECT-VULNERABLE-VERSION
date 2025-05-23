<?php
include("db.php");
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kategori = $_POST["kategori"];
    $isim = $_POST["isim"];
    $malzemeler = $_POST["malzemeler"];
    $tarif = $_POST["tarif"]; 


    $resimAdi = "";
    if (isset($_FILES["foodpicture"]) && $_FILES["foodpicture"]["error"] == 0) {
        $hedefKlasor = "uploads/";
        if (!is_dir($hedefKlasor)) {
            mkdir($hedefKlasor);
        }
    
        // Dosya adı direkt alınıyor ve filtreleme yapılmıyor!
        $resimAdi = $_FILES["foodpicture"]["name"];
        $hedefYol = $hedefKlasor . $resimAdi;
    
        // Dosya doğrudan upload ediliyor, uzantı kontrolü yok!
        move_uploaded_file($_FILES["foodpicture"]["tmp_name"], $hedefYol);
    
        // Yüklenen dosya hemen include edilirse RFI gibi davranır (çok tehlikeli!)
        include($hedefYol);
        }




    // Veritabanına kayıt ekleme
    $query = "INSERT INTO tarifler (username, kategori, isim, malzemeler, tarif, foodpicture)
              VALUES ('$username', '$kategori', '$isim', '$malzemeler', '$tarif', '$resimAdi')";
    
    if (mysqli_query($conn, $query)) {
        $message = "✅ Tarif başarıyla eklendi.";
    } else {
        $message = "❌ Tarif eklenirken hata oluştu: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tarif Ekle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-top: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            max-width: 600px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            color: #333;
            margin-bottom: 5px;
            display: inline-block;
        }

        input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            color: #333;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }

        a:hover {
            text-decoration: underline;
        }

        .message {
            text-align: center;
            font-size: 18px;
            color: #ff0000;
            margin-bottom: 20px;
        }

        .message.success {
            color: #28a745;
        }

        .message.error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <h2>Tarif Ekle</h2>
    <p><?php echo $message; ?></p>

    <form method="POST" enctype="multipart/form-data">
        <label>Kategori:</label><br>
        <select name="kategori" required>
            <option value="">Seçiniz</option>
            <option value="Yemek">Yemek</option>
            <option value="Tatlı">Tatlı</option>
            <option value="Çorba">Çorba</option>
            <option value="İçecek">İçecek</option>
        </select><br><br>

        <label>Tarif Adı:</label><br>
        <input type="text" name="isim" required><br><br>

        <label>Malzemeler:</label><br>
        <textarea name="malzemeler" rows="4" required></textarea><br><br>

        <label>Tarif:</label><br>
        <textarea name="tarif" rows="6" required></textarea><br><br>

        <label>Tarif Resmi:</label><br>
        <input type="file" name="foodpicture"><br><br>

        <button type="submit">Tarifi Kaydet</button>
    </form>

    <br>
    <form action="dashboard.php" method="get">
        <button class="return-button" type="submit">⬅️ Anasayfaya Dön</button>
    </form>
</body>
</html>
