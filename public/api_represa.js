
const API_REPRESA = "/represa_la_paz_mysql/backend";

async function apiRepresa(url, options = {}) {
    try {
        const response = await fetch(url, options);

        const texto = await response.text();

        let data;

        try {
            data = JSON.parse(texto);
        } catch {
            throw new Error("PHP no devolvió JSON: " + texto.substring(0, 200));
        }

        if (!response.ok || data.ok === false) {
            throw new Error(data.mensaje || "Error del servidor.");
        }

        return data;

    } catch (error) {
        console.error("Error API Represa:", error);
        throw error;
    }
}

async function guardarSimulacion(data) {
    return apiRepresa(`${API_REPRESA}/guardar_simulacion.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    });
}

async function listarSimulaciones() {
    const r = await apiRepresa(
        `${API_REPRESA}/listar_simulaciones.php`
    );

    return r.datos || [];
}

async function obtenerDetalleSimulacion(id) {
    return apiRepresa(
        `${API_REPRESA}/detalle_simulacion.php?id=${encodeURIComponent(id)}`
    );
}

async function eliminarSimulacion(id) {
    return apiRepresa(`${API_REPRESA}/eliminar_simulacion.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: id })
    });
}


async function borrarTodoHistorial() {
    return apiRepresa(`${API_REPRESA}/vaciar_historial.php`, {
        method: "POST"
    });
}
