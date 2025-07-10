
///catalogo
const CATALOGO_SUBSISTEMAS = {
    1: [ // Tipo usuario 1 ADMINISTRADOR
        { id: 1, nombre: 'AGENDAR CITA', imagen: 'img/nuevaCita.png', clase: 'agendarCita' },
        { id: 2, nombre: 'REAGENDAR CITA', imagen: 'img/editarCita.png', clase: 'reagendarCita' },
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 4, nombre: 'NUEVO PACIENTE', imagen: 'img/nuevoPaciente.png', clase: 'nuevoPaciente' },
        { id: 5, nombre: 'BUSCAR PACIENTES', imagen: 'img/buscarPaciente.png', clase: 'buscarPaciente' },
        { id: 6, nombre: 'EDITAR PACIENTES', imagen: 'img/editarPaciente.png', clase: 'editarPaciente' },
        { id: 7, nombre: 'VALIDAR PAGO', imagen: 'img/validar.png', clase: 'validar' },
        { id: 8, nombre: 'CONSULTORIO', imagen: 'img/consultorio.png', clase: 'consultorio' },
        { id: 9, nombre: 'INDICADORES', imagen: 'img/indicadores.png', clase: 'indicadores' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 11, nombre: 'BLOQUEAR DIAS', imagen: 'img/bloquear.png', clase: 'bloquear' },
        { id: 12, nombre: 'EDITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'edit_diagnostico' },
        { id: 13, nombre: 'HABILITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'habilitar_diagnostico' },
        { id: 14, nombre: 'HORARIOS', imagen: 'img/horarios.png', clase: 'horarios' },
        { id: 15, nombre: 'HOJA DE TRABAJO', imagen: 'img/hojaTrabajo.png', clase: 'hojaTrabajo' },
        { id: 16, nombre: 'BLOQUEAR HORARIOS', imagen: 'img/bloquearHoras.png', clase: 'bloquearHoras' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' },
        { id: 18, nombre: 'ASIGNAR EXPEDIENTE', imagen: 'img/exp.png', clase: 'asignarExpediente' }


    ],

    2: [ // Tipo usuario 2 DIRECCION
        { id: 1, nombre: 'AGENDAR CITA', imagen: 'img/nuevaCita.png', clase: 'agendarCita' },
        { id: 2, nombre: 'REAGENDAR CITA', imagen: 'img/editarCita.png', clase: 'reagendarCita' },
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 4, nombre: 'NUEVO PACIENTE', imagen: 'img/nuevoPaciente.png', clase: 'nuevoPaciente' },
        { id: 5, nombre: 'BUSCAR PACIENTES', imagen: 'img/buscarPaciente.png', clase: 'buscarPaciente' },
        { id: 6, nombre: 'EDITAR PACIENTES', imagen: 'img/editarPaciente.png', clase: 'editarPaciente' },
        { id: 7, nombre: 'VALIDAR PAGO', imagen: 'img/validar.png', clase: 'validar' },
        { id: 8, nombre: 'CONSULTORIO', imagen: 'img/consultorio.png', clase: 'consultorio' },
        { id: 9, nombre: 'INDICADORES', imagen: 'img/indicadores.png', clase: 'indicadores' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 11, nombre: 'BLOQUEAR DIAS', imagen: 'img/bloquear.png', clase: 'bloquear' },
        { id: 12, nombre: 'EDITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'edit_diagnostico' },
        { id: 13, nombre: 'HABILITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'habilitar_diagnostico' },
        { id: 14, nombre: 'HORARIOS', imagen: 'img/horarios.png', clase: 'horarios' },
        { id: 15, nombre: 'HOJA DE TRABAJO', imagen: 'img/hojaTrabajo.png', clase: 'hojaTrabajo' },
        { id: 16, nombre: 'BLOQUEAR HORARIOS', imagen: 'img/bloquearHoras.png', clase: 'bloquearHoras' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' },
        { id: 18, nombre: 'ASIGNAR EXPEDIENTE', imagen: 'img/exp.png', clase: 'asignarExpediente' }
    ],

    3: [ // Tipo usuario 3 CORRDINACION ADMINISTRACION
        { id: 1, nombre: 'AGENDAR CITA', imagen: 'img/nuevaCita.png', clase: 'agendarCita' },
        { id: 2, nombre: 'REAGENDAR CITA', imagen: 'img/editarCita.png', clase: 'reagendarCita' },
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 5, nombre: 'BUSCAR PACIENTES', imagen: 'img/buscarPaciente.png', clase: 'buscarPaciente' },
        { id: 7, nombre: 'VALIDAR PAGO', imagen: 'img/validar.png', clase: 'validar' },
        { id: 9, nombre: 'INDICADORES', imagen: 'img/indicadores.png', clase: 'indicadores' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 15, nombre: 'HOJA DE TRABAJO', imagen: 'img/hojaTrabajo.png', clase: 'hojaTrabajo' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' }
    ],

    4: [ // Tipo usuario 4 CORRDINACION
        { id: 1, nombre: 'AGENDAR CITA', imagen: 'img/nuevaCita.png', clase: 'agendarCita' },
        { id: 2, nombre: 'REAGENDAR CITA', imagen: 'img/editarCita.png', clase: 'reagendarCita' },
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 5, nombre: 'BUSCAR PACIENTES', imagen: 'img/buscarPaciente.png', clase: 'buscarPaciente' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 13, nombre: 'HABILITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'habilitar_diagnostico' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' }

    ],
    5: [ // Tipo usuario 5 CONSULTORIO
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 8, nombre: 'CONSULTORIO', imagen: 'img/consultorio.png', clase: 'consultorio' },
        { id: 12, nombre: 'EDITAR DIAGNÓSTICO', imagen: 'img/edit_diagnostico.png', clase: 'edit_diagnostico' }

    ],
    6: [ // Tipo usuario 6 FILTRO
        { id: 4, nombre: 'NUEVO PACIENTE', imagen: 'img/nuevoPaciente.png', clase: 'nuevoPaciente' },
        { id: 6, nombre: 'EDITAR PACIENTES', imagen: 'img/editarPaciente.png', clase: 'editarPaciente' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' }
    ],
    7: [ // Tipo usuario 7 RECEPCION VALIDACION
        { id: 1, nombre: 'AGENDAR CITA', imagen: 'img/nuevaCita.png', clase: 'agendarCita' },
        { id: 2, nombre: 'REAGENDAR CITA', imagen: 'img/editarCita.png', clase: 'reagendarCita' },
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 6, nombre: 'EDITAR PACIENTES', imagen: 'img/editarPaciente.png', clase: 'editarPaciente' },
        { id: 7, nombre: 'VALIDAR PAGO', imagen: 'img/validar.png', clase: 'validar' },
        { id: 10, nombre: 'DISPONIBILIDAD', imagen: 'img/disponibilidad.png', clase: 'disponibilidad' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' }


    ],
    8: [ // Tipo usuario 8 ARCHIVO
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 15, nombre: 'HOJA DE TRABAJO', imagen: 'img/hojaTrabajo.png', clase: 'hojaTrabajo' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' },
        { id: 18, nombre: 'ASIGNAR EXPEDIENTE', imagen: 'img/exp.png', clase: 'asignarExpediente' }


    ],
    9: [ // Tipo usuario 9 RAYOS X
        { id: 3, nombre: 'CONSULTAR CITAS', imagen: 'img/citas.png', clase: 'consultarCita' },
        { id: 8, nombre: 'CONSULTORIO', imagen: 'img/consultorio.png', clase: 'consultorio' },
        { id: 17, nombre: 'GENERAR CÓDIGOS', imagen: 'img/qr.png', clase: 'qr' }

    ]
};  