
// Verificar que los campos obligatorios no estén vacíos
function validarCamposObligatorios(idFormulario) {
    const formulario = document.getElementById(idFormulario);

    if (!formulario) {
        return false;
    }

    const campos = formulario.querySelectorAll('[required]');

    for (const campo of campos) {
        if (!campo.value.trim()) {
            alert('Por favor, complete todos los campos obligatorios.');
            campo.focus();
            return false;
        }
    }

    return true;
}

// Verificar que un valor sea numérico positivo
function validarNumeroPositivo(valor) {
    const numero = Number(valor);

    return Number.isFinite(numero) && numero > 0;
}

// Verificar formato básico de teléfono
function validarTelefono(telefono) {
    return /^[0-9+\-\s()]{7,20}$/.test(telefono);
}
