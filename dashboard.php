<?php
include("db.php");
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$username = $_SESSION["username"];
$is_admin = ($username === 'admin'); // Admin kontrolü
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori'] ?? '';

// Beğeni işlemi
if (isset($_GET['like'])) {
    $tarif_id = $_GET['like'];

    $like_check_query = "SELECT * FROM likes WHERE tarif_id = '$tarif_id' AND username = '$username'";
    $like_check_result = mysqli_query($conn, $like_check_query);

    if (mysqli_num_rows($like_check_result) > 0) {
        $query = "DELETE FROM likes WHERE tarif_id = '$tarif_id' AND username = '$username'";
    } else {
        $query = "INSERT INTO likes (tarif_id, username) VALUES ('$tarif_id', '$username')";
    }
    
 
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
    exit;
}

// Favorilere ekleme işlemi
if (isset($_GET['favorite'])) {
    $tarif_id = $_GET['favorite'];

    $favorite_check_query = "SELECT * FROM favorites WHERE tarif_id = '$tarif_id' AND username = '$username'";
    $favorite_check_result = mysqli_query($conn, $favorite_check_query);

    if (mysqli_num_rows($favorite_check_result) > 0) {
        $query = "DELETE FROM favorites WHERE tarif_id = '$tarif_id' AND username = '$username'";
    } else {
        $query = "INSERT INTO favorites (tarif_id, username) VALUES ('$tarif_id', '$username')";
    }
    mysqli_query($conn, $query);
    header("Location: dashboard.php");
    exit;
}

// Tarif silme işlemi
if (isset($_GET['delete'])) {
    $tarif_id = $_GET['delete'];

    // Kullanıcının tarifin sahibi olup olmadığını veya admin olup olmadığını kontrol et
    $check_owner_query = "SELECT * FROM tarifler WHERE id = '$tarif_id' AND (username = '$username' OR '$is_admin')";
    $owner_result = mysqli_query($conn, $check_owner_query);

    if (mysqli_num_rows($owner_result) > 0) {
        // Tarif sahibiyse veya adminse silme işlemini gerçekleştir
        $delete_query = "DELETE FROM tarifler WHERE id = '$tarif_id'";
        mysqli_query($conn, $delete_query);
    }

    header("Location: dashboard.php");
    exit;
}

// Tarif güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tarif'])) {
    $tarif_id = $_POST['tarif_id']; // Kullanıcıdan gelen veri kontrol edilmeden alınıyor
    $isim = $_POST['isim'];         // Kullanıcıdan gelen veri kontrol edilmeden alınıyor
    $isim= str_replace("'", "\'", $isim);
    $malzemeler = $_POST['malzemeler']; // Kullanıcıdan gelen veri kontrol edilmeden alınıyor
     $malzemeler= str_replace("'", "\'", $malzemeler); 
    $tarif = $_POST['tarif'];       // Kullanıcıdan gelen veri kontrol edilmeden alınıyor
    $kategori = $_POST['kategori']; // Kullanıcıdan gelen veri kontrol edilmeden alınıyor

    // Kullanıcıya ait tarif mi veya admin mi kontrol etme kısmı (Güvensiz)
    $check_owner_query = "SELECT * FROM tarifler WHERE id = $tarif_id AND (username = '$username' OR '$is_admin')";
    $owner_result = mysqli_query($conn, $check_owner_query);

    if (mysqli_num_rows($owner_result) > 0) {
        // Kullanıcı girişleri doğrulanmadan SQL sorgusunda kullanılıyor
        $update_query = "UPDATE tarifler 
                        SET isim = '$isim', malzemeler = '$malzemeler', tarif = '$tarif', kategori = '$kategori' 
                        WHERE id = $tarif_id";
        mysqli_query($conn, $update_query); // Güvensiz sorgu çalıştırılıyor
    }

    header("Location: dashboard.php");
    exit;
}


$baseQuery = "SELECT * FROM tarifler"; //tariflerin listelendigi kod kismi
$conditions = [];

if ($filter === 'mine') {
    $conditions[] = "username = '$username'";
}

if ($filter === 'favorites') { 
    $baseQuery = "SELECT t.* FROM tarifler t JOIN favorites f ON t.id = f.tarif_id WHERE f.username = '$username'";
} else {
    if (!empty($search)) {
        $search = strtolower($search);
        $conditions[] = "(LOWER(isim) LIKE '%$search%' OR LOWER(tarif) LIKE '%$search%' OR LOWER(malzemeler) LIKE '%$search%')";
    }

    if (!empty($kategori_filter)) {
        $conditions[] = "kategori = '$kategori_filter'";
    }

    if (count($conditions) > 0) {
        $baseQuery .= " WHERE " . implode(" AND ", $conditions);
    }
}
$baseQuery .= " ORDER BY id DESC";
$tarifler = mysqli_query($conn, $baseQuery);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tarifler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar {
            margin-bottom: 20px;
        }
        .card img {
            max-width: 300px;
            max-height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body class="container my-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Tariflerim</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="adding-recipe.php">Yeni Tarif Ekle</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?filter=all">Tüm Tarifler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?filter=mine">Benim Tariflerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?filter=favorites">Favorilerim</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger " href="?logout=true">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Arama ve Kategori Formu -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tarif ara..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-3">
            <select name="kategori" class="form-select">
                <option value="">Tüm Kategoriler</option>
                <option value="yemek" <?php if ($kategori_filter == 'yemek') echo 'selected'; ?>>Yemek</option>
                <option value="tatlı" <?php if ($kategori_filter == 'tatlı') echo 'selected'; ?>>Tatlı</option>
                <option value="çorba" <?php if ($kategori_filter == 'çorba') echo 'selected'; ?>>Çorba</option>
                <option value="içecek" <?php if ($kategori_filter == 'içecek') echo 'selected'; ?>>İçecek</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
        </div>
    </form>

    <h3>Tarifler:</h3>

    <?php while ($row = mysqli_fetch_assoc($tarifler)) { 
        $like_count_query = "SELECT COUNT(*) AS like_count FROM likes WHERE tarif_id = {$row['id']}";
        $like_count_result = mysqli_query($conn, $like_count_query);
        $like_count = mysqli_fetch_assoc($like_count_result)['like_count'];

        $favorite_count_query = "SELECT COUNT(*) AS favorite_count FROM favorites WHERE tarif_id = {$row['id']}";
        $favorite_count_result = mysqli_query($conn, $favorite_count_query);
        $favorite_count = mysqli_fetch_assoc($favorite_count_result)['favorite_count'];


    ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo ($row["isim"]); ?> <small class="text-muted">(<?php echo htmlspecialchars($row["kategori"]); ?>)</small></h5>
                <h6 class="card-subtitle mb-2 text-muted">👤 <?php echo htmlspecialchars($row["username"]); ?></h6>

                <?php if (!empty($row["foodpicture"])) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($row["foodpicture"]); ?>" class="img-fluid my-2 rounded" alt="Tarif Resmi">
                <?php } ?>

                <p><strong>🧾 Malzemeler:</strong><br><?php echo nl2br(($row["malzemeler"])); ?></p>
                <p><strong>📋 Tarif:</strong><br><?php echo nl2br(($row["tarif"])); ?></p>
                <p>👍 Beğeniler: <?php echo $like_count; ?></p>
                <p>⭐ Favoriler: <?php echo $favorite_count; ?></p>

                <div class="d-flex gap-2">
                    <a href="?like=<?php echo $row["id"]; ?>" class="btn btn-outline-success">👍 Beğen</a>
                    <a href="?favorite=<?php echo $row["id"]; ?>" class="btn btn-outline-warning">⭐ Favorilere Ekle</a>

                    <?php if ($row['username'] === $username || $is_admin) { ?>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editForm_<?php echo $row['id']; ?>" aria-expanded="false" aria-controls="editForm_<?php echo $row['id']; ?>">✏️ Düzenle</button>
                    <?php if ($row['username'] === $username || $is_admin) { ?>
                        <a href="?delete=<?php echo $row["id"]; ?>" class="btn btn-outline-danger">🗑️ Sil</a>
                    <?php } ?>
                    <?php } ?>

                    
                </div>

                <?php if ($row['username'] === $username || $is_admin) { ?>
                    <div class="collapse mt-3" id="editForm_<?php echo $row['id']; ?>">
                        <form method="POST">
                            <input type="hidden" name="tarif_id" value="<?php echo $row['id']; ?>">
                            <div class="mb-3">
                                <label for="isim_<?php echo $row['id']; ?>" class="form-label">Tarif Adı:</label>
                                <input type="text" class="form-control" id="isim_<?php echo $row['id']; ?>" name="isim" value="<?php echo $row['isim']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="malzemeler_<?php echo $row['id']; ?>" class="form-label">Malzemeler:</label>
                                <textarea class="form-control" id="malzemeler_<?php echo $row['id']; ?>" name="malzemeler"><?php echo $row['malzemeler']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="tarif_<?php echo $row['id']; ?>" class="form-label">Tarif:</label>
                                <textarea class="form-control" id="tarif_<?php echo $row['id']; ?>" name="tarif"><?php echo $row['tarif']; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="kategori_<?php echo $row['id']; ?>" class="form-label">Kategori:</label>
                                <select class="form-select" id="kategori_<?php echo $row['id']; ?>" name="kategori">
                                    <option value="yemek" <?php if ($row['kategori'] == 'yemek') echo 'selected'; ?>>Yemek</option>
                                    <option value="tatlı" <?php if ($row['kategori'] == 'tatlı') echo 'selected'; ?>>Tatlı</option>
                                    <option value="çorba" <?php if ($row['kategori'] == 'çorba') echo 'selected'; ?>>Çorba</option>
                                    <option value="içecek" <?php if ($row['kategori'] == 'içecek') echo 'selected'; ?>>İçecek</option>
                                </select>
                            </div>
                            <button type="submit" name="update_tarif" class="btn btn-primary">Kaydet</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</body>
</html>