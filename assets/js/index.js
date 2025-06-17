
let ultimaMatricula = null;
let escaneando = true;

function onScanSuccess(decodedText, decodedResult) {
    if (!escaneando) return;

    if (decodedText.startsWith("ALUMNO:")) {
        const matricula = decodedText.split(":")[1].trim();

        if (matricula === ultimaMatricula) return;
        ultimaMatricula = matricula;
        escaneando = false;

        document.getElementById("result").innerText = "Procesando matrícula " + matricula + "...";

        fetch("registrar_asistencia.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "matricula=" + encodeURIComponent(matricula)
        })
        .then(response => response.json())
        .then(data => {
            if (data.registrado) {
                document.getElementById("result").innerText = "✅ Asistencia registrada para " + matricula;
                mostrarPantallaso("pantallaso-correcto");
            } else if (data.error === "Ya tiene asistencia registrada hoy") {
                document.getElementById("result").innerText = "⚠️ " + data.error;
                mostrarPantallaso("pantallaso-ya-registrado");
            } else {
                document.getElementById("result").innerText = "❌ " + data.error;
                mostrarPantallaso("pantallaso-error");
            }

            setTimeout(() => {
                escaneando = true;
                ultimaMatricula = null;
                document.getElementById("result").innerText = "Esperando escaneo...";
            }, 3000);
        })
        .catch(error => {
            console.error("Error al registrar:", error);
            mostrarPantallaso("pantallaso-error");
            escaneando = true;
        });

    } else {
        document.getElementById("result").innerText = "❌ Código no válido";
        mostrarPantallaso("pantallaso-error");
    }
}


function mostrarPantallaso(idDiv) {
    const div = document.getElementById(idDiv);
    div.style.display = "block";
    setTimeout(() => {
        div.style.display = "none";
    }, 2000);
}

const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
    fps: 30,
    qrbox: function(viewfinderWidth, viewfinderHeight) {
        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
        return { width: minEdge * 0.7, height: minEdge * 0.7 };
    }
});
html5QrcodeScanner.render(onScanSuccess);