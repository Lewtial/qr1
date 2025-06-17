<?php
require '../librerias/phpqrcode/qrlib.php'; // o usa autoload si usaste composer

$mysqli = new mysqli("localhost", "root", "", "registro_asistencia");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$sql = "SELECT id_alumno, matricula FROM alumnos WHERE qr_code IS NULL";
$result = $mysqli->query($sql);

$carpeta_qr = "../qrcodes/";
if (!file_exists($carpeta_qr)) {
    mkdir($carpeta_qr, 0777, true);
}

while ($row = $result->fetch_assoc()) {
    $id = $row['id_alumno'];
    $matricula = $row['matricula'];
    $qr_texto = "ALUMNO:$matricula"; // puedes usar JSON u otro formato

    $nombre_archivo = $carpeta_qr . $matricula . ".png";
    QRcode::png($qr_texto, $nombre_archivo, QR_ECLEVEL_H, 6);

    $stmt = $mysqli->prepare("UPDATE alumnos SET qr_code = ? WHERE id_alumno = ?");
    $stmt->bind_param("si", $nombre_archivo, $id);
    $stmt->execute();
    $stmt->close();
}

$mysqli->close();

echo "Códigos QR generados con éxito.";


?>
