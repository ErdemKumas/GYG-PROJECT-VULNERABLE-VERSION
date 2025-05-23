🔓 GYG-PROJECT-VULNERABLE-VERSION 🔓

📋 Proje Hakkında
Bu proje, güvenlik açıkları içeren bir tarif paylaşım platformu örneğidir. Kullanıcılar kayıt olabilir, giriş yapabilir ve tariflerini paylaşabilir. Bu versiyon, güvenlik açıklarını göstermek ve eğitim amacıyla oluşturulmuştur.

⚠️ Güvenlik Açıkları

🔑 Kullanıcı Yönetimi

• Şifrelerin düz metin olarak saklanması

• Güvensiz oturum yönetimi

• Brute force korumasının olmaması


💉 Veritabanı Güvenliği
• SQL enjeksiyon açıkları
• Parametreli sorgu kullanılmaması
• Yetersiz giriş doğrulama

🌐 Web Güvenliği

• XSS (Cross-Site Scripting) açıkları

• CSRF (Cross-Site Request Forgery) korumasının olmaması

• Güvensiz dosya yükleme işlemleri


📁 Dosya Yapısı
Dosya	İşlev	Güvenlik Açığı

login.php	Kullanıcı kimlik doğrulama	Şifre güvenliği eksikliği

dashboard.php	Ana sayfa ve tarif listeleme	XSS açıkları

adding-recipe.php	Tarif ekleme işlemleri	CSRF koruması yok

db.php	Veritabanı bağlantısı	SQL enjeksiyon açıkları

🛠️ Kod Örnekleri

Güvensiz Giriş İşlemi

// Güvensiz sorgu - SQL enjeksiyona açık

$query = "SELECT * FROM users WHERE username='$username' AND password='$password'";

$result = mysqli_query($conn, $query);

Güvensiz Veri Ekleme

// Parametreli sorgu kullanılmadan veri ekleme

$query = "INSERT INTO recipes (title, ingredients, steps) VALUES ('$title', '$ingredients', '$steps')";

mysqli_query($conn, $query);

XSS Açığı

// Kullanıcı girdisi doğrudan çıktıya veriliyor

echo "<div class='recipe-title'>" . $_GET['title'] . "</div>";

🚫 Eksik Güvenlik Önlemleri
Şifre Güvenliği

• Şifreler hash'lenmeden saklanıyor
• Güçlü şifre politikası yok
• Salt kullanılmıyor

Oturum Güvenliği

• HttpOnly flag kullanılmıyor
• Oturum süreleri kontrolü yok
• Oturum fixation koruması yok

Dosya Yükleme
• Dosya türü kontrolü yetersiz
• Dosya boyutu sınırlaması yok
• Dosya adı doğrulama eksikliği
