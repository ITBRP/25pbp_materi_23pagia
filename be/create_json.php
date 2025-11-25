<?php
header("Content-Type: application/json; charset=UTF-8");
// validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Method Salah !'
    ]);
    exit;
}

// Ambil raw input JSON
$dataJson = file_get_contents("php://input");
$data = json_decode($dataJson, true);

$errors = [];
// Validasi nama
if (!isset($data['nama'])) {
    $errors['nama'] = "Nama tidak di kirim";
} else {
    if ($data['nama'] === "") {
        $errors['nama'] = "Nama tidak boleh kosong";
    }
}

// Validasi NIM
if (!isset($data['nim'])) {
    $errors['nim'] = "nim tidak di kirim";
} else {
    if ($data['nim'] == '') {
        $errors['nim'] = "NIM tidak boleh kosong";
    } else {
        if (!preg_match('/^[1-9][0-9]{9}$/', $data['nim'])) {
            $errors['nim'] = "Format NIM salah";
        }
    }
}

if (!isset($data['hp'])) {
    $errors['hp'] = "hp tidak di kirim";
} else {
    if ($data['hp'] === "") {
        $errors['hp'] = "hp tidak boleh kosong";
    }
}

// Jika ada errors
if (count($errors) > 0) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'msg' => 'Data tidak valid !',
        'errors' => $errors
    ]);
    exit;
}

$koneksi = new mysqli('localhost', 'root', '', 'pagiapbp');
$nim = $data['nim'];
$nama = $data['nama'];
$hp = $data['hp'];

// NULL jika tidak upload file
$q = "INSERT INTO mahasiswa(nim, nama, hp) 
      VALUES ($nim, '$nama', '$hp')";

$koneksi->query($q);

echo json_encode([
    'status' => 'success',
    'msg' => 'Proses berhasil',
    'data' => [
        'id' => $koneksi->insert_id,
        'nim' => $nim,
        'nama' => $nama,
        'hp' => $hp,
        'photo' => ''
    ]
]);
