
// Función para confirmar acciones importantes
function confirmarAccion(mensaje) {
    return confirm(mensaje);
}

// Función para limpiar formularios
function limpiarFormulario(idFormulario) {
    const formulario = document.getElementById(idFormulario);

    if (formulario) {
        formulario.reset();
    }
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje) {
    alert(mensaje);
}
