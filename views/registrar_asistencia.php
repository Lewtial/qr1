<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


$url = "localhost";
$usuario = "root";
$contraseña = "";
$base_datos = "registro_asistencia";

$mysqli = new mysqli($url, $usuario, $contraseña, $base_datos);
if ($mysqli->connect_error) {
    die(json_encode(["registrado" => false, "error" => "Error de conexión"]));
}



$matricula = $_POST['matricula'] ?? '';
$matricula = $mysqli->real_escape_string($matricula);

// Buscar al alumno
$result = $mysqli->query("SELECT id_alumno FROM alumnos WHERE matricula = '$matricula'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_alumno = $row['id_alumno'];

    // Verificar si ya tiene asistencia hoy
    $fecha_hoy = date('Y-m-d');
    $check = $mysqli->query("SELECT id FROM asistencia WHERE id_alumno = $id_alumno AND DATE(fecha_hora) = '$fecha_hoy'");

    if ($check && $check->num_rows > 0) {
        echo json_encode(["registrado" => false, "error" => "Ya tiene asistencia registrada hoy"]);
    } else {
        // Registrar asistencia
        $stmt = $mysqli->prepare("INSERT INTO asistencia (id_alumno) VALUES (?)");
        $stmt->bind_param("i", $id_alumno);
        if ($stmt->execute()) {
            echo json_encode(["registrado" => true]);
        } else {
            echo json_encode(["registrado" => false, "error" => "Error al guardar"]);
        }
        $stmt->close();
    }
} else {
    echo json_encode(["registrado" => false, "error" => "Matrícula no encontrada"]);
}

$mysqli->close();
?>