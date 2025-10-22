<?php

/**
 * ============================================
 * Barra superior y navegación principal (header.php)
 * ============================================
 * Este componente muestra la barra superior fija del dashboard.
 * Incluye el logo, menú principal, accesos rápidos, perfil del usuario y botones flotantes.
 * Las opciones del menú y los accesos dependen del rol del usuario logueado.
 * 
 * - Los roles controlan el acceso a cada funcionalidad (Administrador, Control maestro, Empleabilidad, Permanencia, Académico, etc).
 * - Se integra con los componentes de barra lateral y correo flotante.
 * - Incluye menús desplegables para informes, PQRS, periodos, aulas y perfil.
 * - Permite la descarga de informes con control de tiempo y feedback visual.
 * - El diseño es responsivo y utiliza Bootstrap.
 */

$rol = $infoUsuario['rol']; // Obtener el rol del usuario
$extraRol = $infoUsuario['extra_rol']; // Obtener el extra_rol del usuario

include 'components/importBase/importSwal.php'; 

// Obtener el logo de la tabla company
$query = "SELECT logo FROM company LIMIT 1";
$result = $conn->query($query);
$company = $result->fetch_assoc();
$logo = $company['logo'] ?? 'gf_header.png'; // Fallback si no hay logo

?>

<nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container-fluid">
        <button class="btn btn-tertiary mr-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptionsLabel">
            <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand" href="#"><img src="img/logos/<?php echo htmlspecialchars($logo); ?>" alt="logo" width="120px"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="main.php">Inicio</a>
                </li>
                <?php if ($rol === 'Administrador' || $rol === 'Control maestro'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="btnAgregarSede">Sedes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="btnAgregarEntrega">Categoría</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" id="btnExportar">Exportar matriz</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <?php if ($rol === 'Administrador' || $rol === 'Control maestro'): ?>
            <button id="btnSubirBase" class="btn bg-magenta-dark me-2 text-white" type="button">
                <i class="bi bi-cloud-upload me-1"></i>Subir base
            </button>
            <button id="btnDescargarPlantilla" class="btn btn-success me-2" type="button"
                data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="hover"
                data-bs-content="Descargar plantilla base en Excel">
                <i class="bi bi-file-earmark-excel"></i>
            </button>
            <script>
                document.getElementById('btnDescargarPlantilla').addEventListener('click', function() {
                    window.location.href = 'uploads/plantilla_base.xlsx';
                });
            </script>
        <?php endif; ?>

        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo htmlspecialchars($infoUsuario['foto']); ?>" alt="Perfil" class="rounded-circle" width="40" height="40">
                <?php echo htmlspecialchars($infoUsuario['nombre']); ?>
                <div class="spinner-grow spinner-grow-sm" role="status" style="color:#00976a">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <button type="button" class="btn" data-bs-toggle="popover" data-bs-placement="top" data-bs-content="<?php echo htmlspecialchars($infoUsuario['rol']); ?>" data-bs-trigger="hover">
                    <i class="bi bi-info-circle-fill colorVerde" style="color: #00976a;"></i>
                </button>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="profile.php">Perfil</a></li>
                <li><a class="dropdown-item" href="close.php">Cerrar sesión</a></li>
            </ul>
        </div> <!-- Cierre del dropdown -->

    </div> <!-- Cierre del container-fluid -->
</nav>

<!-- Incluir SweetAlert2 si no está ya incluido -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('btnAgregarSede').addEventListener('click', function(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Qué deseas hacer con las Sedes?',
            text: 'Elige una opción para gestionar las sedes.',
            icon: 'question',
            showConfirmButton: false,
            html: `
                <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 24px;">
                    <button id="swalAgregarSede" class="btn bg-indigo-dark text-white btn-lg" style="width:100%">
                        <i class="bi bi-plus-circle"></i> Agregar Nueva Sede
                    </button>
                    <button id="swalGestionarSede" class="btn bg-teal-dark text-white btn-lg" style="width:100%">
                        <i class="bi bi-list-check"></i> Gestionar Sedes Existentes
                    </button>
                    <button id="swalCancelarSede" class="btn btn-secondary btn-lg" style="width:100%">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            `,
            didRender: () => {
                document.getElementById('swalAgregarSede').onclick = function() {
                    Swal.close();
                    // Agregar nueva sede
                    Swal.fire({
                        title: 'Agregar Nueva Sede',
                        input: 'text',
                        inputLabel: 'Nombre de la Sede',
                        inputPlaceholder: 'Ingresa el nombre de la sede',
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'El nombre no puede estar vacío!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('controller/guardar_sede.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'nombre_sede=' + encodeURIComponent(result.value)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('¡Éxito!', data.message, 'success');
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Hubo un problema al guardar.', 'error');
                                });
                        }
                    });
                };
                document.getElementById('swalGestionarSede').onclick = function() {
                    Swal.close();
                    // Gestionar sedes existentes
                    fetch('controller/obtener_sedes.php')
                        .then(response => response.json())
                        .then(sedes => {
                            if (sedes.length === 0) {
                                Swal.fire('Sin Sedes', 'No hay sedes registradas.', 'info');
                                return;
                            }
                            let html = '<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped table-hover table-bordered"><thead><tr><th>Nombre de Sede</th><th>Fecha de Creación</th><th>Creado por</th><th>Acciones</th></tr></thead><tbody>';
                            sedes.forEach(sede => {
                                html += `<tr><td>${sede.nombre}</td><td>${sede.fecha_creacion}</td><td>${sede.nombre_creador}</td><td><button class="btn btn-danger btn-sm" onclick="eliminarSede(${sede.id})">Eliminar</button></td></tr>`;
                            });
                            html += '</tbody></table></div>';
                            Swal.fire({
                                title: 'Gestionar Sedes Existentes',
                                html: html,
                                showCancelButton: true,
                                cancelButtonText: 'Cerrar',
                                width: '75%'
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'No se pudieron cargar las sedes.', 'error');
                        });
                };
                document.getElementById('swalCancelarSede').onclick = function() {
                    Swal.close();
                };
            }
        });
    });

    document.getElementById('btnAgregarEntrega').addEventListener('click', function(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Qué deseas hacer con las Categorías?',
            text: 'Elige una opción para gestionar los tipos de entrega.',
            icon: 'question',
            showConfirmButton: false,
            html: `
                <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 24px;">
                    <button id="swalAgregarEntrega" class="btn bg-indigo-dark text-white btn-lg" style="width:100%">
                        <i class="bi bi-plus-circle"></i> Agregar Nuevo Tipo de Entrega
                    </button>
                    <button id="swalGestionarEntrega" class="btn bg-teal-dark text-white btn-lg" style="width:100%">
                        <i class="bi bi-list-check"></i> Gestionar Tipos de Entrega Existentes
                    </button>
                    <button id="swalCancelarEntrega" class="btn btn-secondary btn-lg" style="width:100%">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                </div>
            `,
            didRender: () => {
                document.getElementById('swalAgregarEntrega').onclick = function() {
                    Swal.close();
                    // Agregar nuevo tipo de entrega
                    Swal.fire({
                        title: 'Agregar Nuevo Tipo de Entrega',
                        input: 'text',
                        inputLabel: 'Nombre del Tipo de Entrega',
                        inputPlaceholder: 'Ingresa el nombre del tipo de entrega',
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'El nombre no puede estar vacío!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('controller/guardar_tipo_entrega.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'nombre_tipo_entrega=' + encodeURIComponent(result.value)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('¡Éxito!', data.message, 'success');
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Hubo un problema al guardar.', 'error');
                                });
                        }
                    });
                };
                document.getElementById('swalGestionarEntrega').onclick = function() {
                    Swal.close();
                    // Gestionar tipos de entrega existentes
                    fetch('controller/obtener_tipos_entrega.php')
                        .then(response => response.json())
                        .then(tipos => {
                            if (tipos.length === 0) {
                                Swal.fire('Sin Tipos de Entrega', 'No hay tipos de entrega registrados.', 'info');
                                return;
                            }
                            let html = '<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped table-hover table-bordered"><thead><tr><th>Nombre del Tipo de Entrega</th><th>Fecha de Creación</th><th>Creado por</th><th>Acciones</th></tr></thead><tbody>';
                            tipos.forEach(tipo => {
                                html += `<tr><td>${tipo.nombre}</td><td>${tipo.fecha_creacion}</td><td>${tipo.nombre_creador}</td><td><button class="btn btn-danger btn-sm" onclick="eliminarTipoEntrega(${tipo.id})">Eliminar</button></td></tr>`;
                            });
                            html += '</tbody></table></div>';
                            Swal.fire({
                                title: 'Gestionar Tipos de Entrega Existentes',
                                html: html,
                                showCancelButton: true,
                                cancelButtonText: 'Cerrar',
                                width: '75%'
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'No se pudieron cargar los tipos de entrega.', 'error');
                        });
                };
                document.getElementById('swalCancelarEntrega').onclick = function() {
                    Swal.close();
                };
            }
        });
    });

    // Exportar matriz
    document.getElementById('btnExportar').addEventListener('click', function(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Exportando Matriz...',
            text: 'Esto puede tomar unos momentos dependiendo de la cantidad de registros.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Crear iframe oculto para la descarga
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = 'components/individualSearch/exportar_matriz.php';
        document.body.appendChild(iframe);

        // Cerrar el Swal después de un tiempo razonable
        setTimeout(() => {
            document.body.removeChild(iframe);
            Swal.fire({
                title: '¡Exportación completada!',
                text: 'El archivo Excel se está descargando.',
                icon: 'success',
                confirmButtonText: 'Entendido'
            });
        }, 3000);
    });

    // Funciones básicas para editar/eliminar (implementa en archivos PHP separados si es necesario)
    function editarSede(id) {
        Swal.fire('Editar', 'Funcionalidad de edición no implementada aún. ID: ' + id, 'info');
    }

    function eliminarSede(id) {
        Swal.fire({
            title: '¿Eliminar sede?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('controller/eliminar_sede.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_sede=' + id
                    })
                    .then(response => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success').then(() => {
                                // Opcional: Recargar la tabla o cerrar el SWAL
                                location.reload(); // Recarga la página para actualizar la vista
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Hubo un problema al eliminar.', 'error');
                    });
            }
        });
    }

    function eliminarTipoEntrega(id) {
        Swal.fire({
            title: '¿Eliminar tipo de entrega?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('controller/eliminar_tipo_entrega.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id_tipo_entrega=' + id
                    })
                    .then(response => response.json())
                    .then((data) => {
                        if (data.success) {
                            Swal.fire('¡Eliminado!', data.message, 'success').then(() => {
                                // Opcional: Recargar la tabla o cerrar el SWAL
                                location.reload(); // Recarga la página para actualizar la vista
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Hubo un problema al eliminar.', 'error');
                    });
            }
        });
    }
</script>

<?php include("info_flotante.php"); ?>
