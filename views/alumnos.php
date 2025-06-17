<?php
$mysqli = new mysqli("localhost", "root", "", "registro_asistencia");
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$sql2 = "SELECT * FROM alumnos";
$result2 = $mysqli->query($sql2);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Alumnos</title>
</head>
<body>

<h2>Lista de Alumnos con sus códigos QR</h2>

<?php if ($result2->num_rows > 0): ?>
    <?php while ($alumno = $result2->fetch_assoc()): ?>
        <div style="margin-bottom: 20px;">
            <p><strong>Nombre:</strong> <?php echo $alumno['nombre']; ?></p>
            <p><strong>Matrícula:</strong> <?php echo $alumno['matricula']; ?></p>
            <img src="../qrcodes/<?php echo $alumno['matricula']; ?>.png" alt="QR de <?php echo $alumno['nombre']; ?>" width="150">
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No hay alumnos registrados.</p>
<?php endif; ?>

</body>
</html>
