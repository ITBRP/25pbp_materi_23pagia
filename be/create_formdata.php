<?php
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

// cek payload
$errors = [];

if (!isset($_POST['nim'])) {
    $errors['nim'] = "nim tidak di kirim";
} else {
    if ($_POST['nim'] == '') {
        $errors['nim'] = "NIM tidak boleh kosong";
    } else {
        if (!preg_match('/^[1-9][0-9]{2}$/', $_POST['nim'])) {
            $errors['nim'] = "Format NIM harus 3 digit angka, angka tidak boleh 0";
        }
    }
}

if (!isset($_POST['nama'])) {
    $errors['nama'] = "Nama tidak di kirim";
} else {
    if ($_POST['nama'] == "") {
        $errors['nama'] = "Nama tidak boleh kosong";
    }
}
if (!isset($_POST['hp'])) {
    $errors['hp'] = "HP tidak di kirim";
} else {
    if ($_POST['hp'] == "") {
        $errors['hp'] = "HP tidak boleh kosong";
    }
}

$anyPhoto = false;
$namaPhoto = '';
$fileExt = '';
if (isset($_FILES['photo'])) {

    // User memilih file
    if ($_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileName = $_FILES['photo']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            $errors['photo'] = "File harus jpg atau png";
        } else {
            $anyPhoto = true; // photo valid, siap disave
        }
    }

}

if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data tidak valid',
        'errors' => $errors
    ]);
    exit();
}

// insert db
$koneksi = new mysqli('localhost', 'root', '', 'pagiapbp');
$nim = $_POST['nim'];
$nama = $_POST['nama'];
$hp = $_POST['hp'];
if ($anyPhoto) {
    $namaPhoto = md5(date('dmyhis')) . "." . $fileExt;
    move_uploaded_file($_FILES['photo']['tmp_name'], 'img/' . $namaPhoto);
}

// NULL jika tidak upload file
$q = "INSERT INTO mahasiswa(nim, nama, hp, photo) 
      VALUES ($nim, '$nama', '$hp', " . ($namaPhoto ? "'$namaPhoto'" : "NULL") . ")";


$koneksi->query($q);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $koneksi->insert_id,
        'nim' => $nim,
        'nama' => $nama,
        'hp' => $hp,
        'photo' => $namaPhoto
    ]
]);