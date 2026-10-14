document.addEventListener("DOMContentLoaded", () => {
    const campoTelefono = document.querySelector("#telefono");
    const contenedorResultados = document.querySelector("#resultados-turnos");
    const mensaje = document.querySelector("#mensaje-consulta");

    if (!campoTelefono || !contenedorResultados || !mensaje) {
        return;
    }

    const tarjetas = contenedorResultados.querySelectorAll(".resultado");

    if (tarjetas.length === 0) {
        return;
    }

    // El cliente consulta siempre con el teléfono original.
    const telefonoConsultado = campoTelefono.value.trim();

    // Crear el espacio para las alertas sin modificar el PHP.
    const alerta = document.createElement("div");
    alerta.id = "alerta-turno";
    alerta.className = "alerta-turno";
    alerta.setAttribute("role", "status");
    alerta.setAttribute("aria-live", "polite");
    alerta.hidden = true;

    contenedorResultados.parentNode.insertBefore(
        alerta,
        contenedorResultados
    );

    function actualizarAlerta(tarjeta) {
        const numero = Number(tarjeta.dataset.numero);
        const textoAtendido =
            tarjeta.querySelector(".ultimo-atendido").textContent.trim();

        const actual = Number(textoAtendido.split("-").pop());
        const faltan = Number(
            tarjeta.querySelector(".turnos-delante").textContent
        );

        if (
            !Number.isFinite(numero) ||
            !Number.isFinite(actual) ||
            !Number.isFinite(faltan)
        ) {
            alerta.hidden = true;
            return;
        }

        if (numero <= actual) {
            alerta.hidden = true;
            return;
        }

        alerta.hidden = false;
        alerta.className = "alerta-turno";

        if (faltan <= 5) {
            alerta.classList.add("alerta-final");
            alerta.innerHTML =
    "<strong>¡Tu turno está muy cerca!</strong>" +
    `Quedan ${faltan} turno(s) por delante. ` +
    "Mantente atento para no perder tu turno.";
        } else if (faltan <= 10) {
            alerta.classList.add("alerta-urgente");
            alerta.innerHTML =
                "<strong>Tu turno se está acercando</strong>" +
                `Quedan ${faltan} turno(s) por delante. ` +
                "Te recomendamos estar pendiente.";
        } else {
            alerta.hidden = true;
        }
    }

    // Mostrar una alerta desde la primera carga, si corresponde.
    actualizarAlerta(tarjetas[0]);

    async function actualizarTurnos() {
        if (campoTelefono.value.trim() !== telefonoConsultado) {
            return;
        }

        try {
            const datos = new FormData();
            datos.append("accion", "actualizar");
            datos.append("telefono", telefonoConsultado);

            const respuesta = await fetch(window.location.pathname, {
                method: "POST",
                body: datos,
                cache: "no-store"
            });

            if (!respuesta.ok) {
                return;
            }

            const resultado = await respuesta.json();

            if (
                resultado.estado !== "ok" ||
                !Array.isArray(resultado.turnos)
            ) {
                return;
            }

            resultado.turnos.forEach(turno => {
                const tipo = String(turno.tipo);
                const numero = Number(turno.numero);
                const actual = Number(turno.turno_actual);

                const tarjeta = Array.from(
                    contenedorResultados.querySelectorAll(".resultado")
                ).find(elemento =>
                    elemento.dataset.tipo === tipo &&
                    Number(elemento.dataset.numero) === numero
                );

                if (!tarjeta || !Number.isFinite(actual)) {
                    return;
                }

                const faltan = Math.max(0, numero - actual - 1);

                tarjeta.querySelector(".ultimo-atendido").textContent =
                    `${tipo}-${actual}`;

                tarjeta.querySelector(".turnos-delante").textContent =
                    faltan;

                tarjeta.querySelector(".situacion-turno").textContent =
                    numero <= actual
                        ? "Tu turno ya está siendo atendido o ya pasó."
                        : `Hay ${faltan} turno(s) delante de ti.`;

                actualizarAlerta(tarjeta);
            });

            mensaje.hidden = true;
        } catch (error) {
            console.error("Error al actualizar los turnos:", error);
        }
    }

    window.setInterval(actualizarTurnos, 30000);
});