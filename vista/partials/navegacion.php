<?php

$directorioActual = basename(dirname($_SERVER['SCRIPT_NAME']));
$rutaBase = $directorioActual === 'vista' ? '../' : '';
$paginaActual = basename($_SERVER['SCRIPT_NAME']);
$nombreUsuario = $_SESSION['nombre_completo'] ?? 'Usuario';
$perfilUsuario = $_SESSION['perfil'] ?? '';

?>
<svg class="iconos-definicion" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <symbol id="i-inicio" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></symbol>
    <symbol id="i-paquete" viewBox="0 0 24 24"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8M12 13v9"/></symbol>
    <symbol id="i-tipo" viewBox="0 0 24 24"><path d="M4 5h16M4 12h16M4 19h16"/><circle cx="8" cy="5" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="10" cy="19" r="2"/></symbol>
    <symbol id="i-cliente" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></symbol>
    <symbol id="i-camion" viewBox="0 0 24 24"><path d="M3 6h12v12H3zM15 10h4l3 4v4h-7z"/><circle cx="7.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/></symbol>
    <symbol id="i-destino" viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></symbol>
    <symbol id="i-reporte" viewBox="0 0 24 24"><path d="M4 19V5M4 19h17"/><path d="m7 15 4-4 3 2 6-7"/><path d="M16 6h4v4"/></symbol>
    <symbol id="i-usuario" viewBox="0 0 24 24"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M19 8v6M16 11h6"/></symbol>
    <symbol id="i-empleado" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0M17 4l2-2 2 2"/></symbol>
    <symbol id="i-perfil" viewBox="0 0 24 24"><path d="M12 3 20 7v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="i-permiso" viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></symbol>
    <symbol id="i-salida" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></symbol>
    <symbol id="i-dinero" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M7 9h.01M17 15h.01"/></symbol>
    <symbol id="i-lista" viewBox="0 0 24 24"><path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1 1 2-2M3 12l1 1 2-2M3 18l1 1 2-2"/></symbol>
</svg>

<header class="app-header">
    <div class="barra-superior">
        <a class="marca" href="<?= $rutaBase ?>index.php" aria-label="Jet Express, inicio">
            <span class="marca-icono"><svg class="icono" aria-hidden="true"><use href="#i-paquete"></use></svg></span>
            <span><strong>JET EXPRESS</strong><small>Gestión de envíos</small></span>
        </a>
    </div>
    <p class="sidebar-caption">Panel control</p>
    <nav class="navegacion-modulos" aria-label="Módulos principales">
        <a class="enlace-modulo <?= $paginaActual === 'index.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>index.php" aria-label="Inicio" title="Inicio" <?= $paginaActual === 'index.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-inicio"></use></svg><span>Inicio</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'tipo_guia.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/tipo_guia.php" aria-label="Tipos de guía" title="Tipos de guía" <?= $paginaActual === 'tipo_guia.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-tipo"></use></svg><span>Tipos de guía</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'guia.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/guia.php" aria-label="Guías" title="Guías" <?= $paginaActual === 'guia.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-paquete"></use></svg><span>Guías</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'cliente.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/cliente.php" aria-label="Clientes" title="Clientes" <?= $paginaActual === 'cliente.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-cliente"></use></svg><span>Clientes</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'despacho.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/despacho.php" aria-label="Despachos" title="Despachos" <?= $paginaActual === 'despacho.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-camion"></use></svg><span>Despachos</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'tarifa_destino.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/tarifa_destino.php" aria-label="Tarifas y destinos" title="Tarifas y destinos" <?= $paginaActual === 'tarifa_destino.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-destino"></use></svg><span>Tarifas y destinos</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'reportes.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/reportes.php" aria-label="Reportes" title="Reportes" <?= $paginaActual === 'reportes.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-reporte"></use></svg><span>Reportes</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'usuarios.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/usuarios.php" aria-label="Usuarios" title="Usuarios" <?= $paginaActual === 'usuarios.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-usuario"></use></svg><span>Usuarios</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'empleados.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/empleados.php" aria-label="Empleados" title="Empleados" <?= $paginaActual === 'empleados.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-empleado"></use></svg><span>Empleados</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'perfiles.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/perfiles.php" aria-label="Perfiles" title="Perfiles" <?= $paginaActual === 'perfiles.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-perfil"></use></svg><span>Perfiles</span>
        </a>
        <a class="enlace-modulo <?= $paginaActual === 'permisos.php' ? 'activo' : '' ?>" href="<?= $rutaBase ?>vista/permisos.php" aria-label="Permisos" title="Permisos" <?= $paginaActual === 'permisos.php' ? 'aria-current="page"' : '' ?>>
            <svg class="icono" aria-hidden="true"><use href="#i-permiso"></use></svg><span>Permisos</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="usuario-sesion">
            <span class="usuario-avatar" aria-hidden="true"><svg class="icono" aria-hidden="true"><use href="#i-usuario"></use></svg></span>
            <span class="usuario-detalle"><strong><?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?></strong><?php if ($perfilUsuario !== ''): ?><small><?= htmlspecialchars($perfilUsuario, ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?></span>
        </div>
        <a class="salir-sesion" href="<?= $rutaBase ?>controlador/contCerrarSesion.php" title="Cerrar sesión">
            <svg class="icono" aria-hidden="true"><use href="#i-salida"></use></svg><span>Cerrar sesión</span>
        </a>
    </div>
</header>
