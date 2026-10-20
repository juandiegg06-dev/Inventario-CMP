<?php
/*
 * Listas del FORMATO DE REQUISICIÓN DE COMPRAS (MA-PC-FT-001, versión 1).
 * Copiadas de la hoja LISTADOS del Excel original.
 *
 * Para agregar o cambiar un solicitante, área, unidad, etc., edítalo aquí.
 * Las requisiciones ya guardadas no cambian: guardan una copia de los nombres.
 *
 * consecutivo_inicial: número que tendrá la PRIMERA requisición que se exporte
 * desde la página (las siguientes van 1 en 1). Si ya llevaban una numeración en
 * Excel, pon aquí el número que sigue.
 */
return [
    'consecutivo_inicial' => 1,

    // Bloque de firma del SOLICITANTE en el formato (fijo: no se edita desde la página).
    // Revisó y Aprobó quedan en blanco para firmar a mano.
    'firma_solicitante' => [
        'nombre' => 'JAVIER ALEXANDER CRIADO RODRIGUEZ',
        'cedula' => '1193275333',
        'cargo'  => 'Responsable Área de Tecnología',
    ],

    // Datos fijos del encabezado del formato
    'formato' => ['codigo' => 'MA-PC-FT-001', 'version' => '1', 'fecha' => '15 de marzo del 2022'],

    // código => nombre
    'empresas' => [
        'CMPN' => 'COOPERATIVA MULTIACTIVA DE PIMPINEROS DEL NORTE - COOMULPINORT',
    ],

    'areas' => [
        'OP' => 'OPERATIVA-PLANTA',
        'SO' => 'SOCIAL',
        'ST' => 'TECNOLOGÍA',
        'SST' => 'SEGURIDAD Y SALUD EN EL TRABAJO',
        'RRHH' => 'RECURSOS HUMANOS',
        'FC' => 'FINANCIERA Y CONTABLE',
        'ARC' => 'ARCHIVO',
        'JUR' => 'JURIDICA',
        'OV' => 'OFICIOS VARIOS',
        'GR' => 'GERENCIA',
    ],

    // código numérico => nombre (el código va en el REQ N°)
    'solicitantes' => [
        1 => 'ERIKA YURLEY JAIMES SALCEDO',
        2 => 'MARIO ALBERTO AREVALO PERDOMO',
        3 => 'ALVARO OMAÑA SAAVEDRA',
        4 => 'ANDREA VALENTINA RAMIREZ JAIMES',
        5 => 'DAMIR DARIO RINCON GARCIA',
        6 => 'MARIA FERNANDA GUZMAN GONZALEZ',
        7 => 'INGRID KARIME HERNANDEZ NIÑO',
        8 => 'MAIRA ALEJANDRA JAIMES MARTINEZ',
        9 => 'ISMELDA SUAREZ ARCHILA',
        10 => 'LUDY GUERRERO GUTIERREZ',
        11 => 'KAREN LISBETH BALLESTEROS MORA',
        12 => 'MIGUEL ANGEL CAPACHO ORTIZ',
        13 => 'MARITZA LORENA BONILLA FLORES',
        14 => 'WILBER D\'ALEXANDRO SABOGAL CARDENAS',
        15 => 'EMILSE GISELA GOMEZ GOMEZ',
        16 => 'YASKARA ENDRINA GARCIA MORA',
        17 => 'EDWIN ALEXIS FERNANDEZ GALVIS',
        18 => 'YINET XIOMARA PEREZ DIAZ',
        19 => 'MERLY DAYANA JURADO SANCHEZ',
        20 => 'MERCEDES DEL PILAR RODRIGUEZ MENDOZA',
        21 => 'HARVEY OMAR GUERRERO RUIZ',
        22 => 'EDWARD JESUS FERNANDEZ ORTEGA',
        23 => 'MAYRA LISETH ORTIZ PRADA',
        24 => 'LUIS HERNANDO DUARTE GUERRERO',
        25 => 'JESUS RAMIREZ MEDRANO',
        26 => 'LEONEL AYAZO LIZARAZO',
        27 => 'ADULFO ORTIZ ZAMBRANO',
        28 => 'OSCAR EFRAIN GUERRERO GUTIERREZ',
        29 => 'SANDRA LILIANA FERNANDEZ MARTINEZ',
        30 => 'EDUAR ALBERTO DUARTE GUERRERO',
        31 => 'JAIRO FABIAN ARENAS ACEVEDO',
        32 => 'JANNY MAURICIO ROJAS SUAREZ',
        33 => 'LISBETH KATHERINE HERNANDEZ DOMINGUEZ',
        34 => 'YILMER HUMBERTO VARGAS AVILA',
        35 => 'JORGE LUIS MORENO MARTINEZ',
        36 => 'MARLYN YESENIA NORIEGA VILLAMIZAR',
        37 => 'RITA ELISA ROA CAJIGAS',
        38 => 'KEVIN JAVIER BAUTISTA TARAZONA',
        39 => 'SAUL GENTIL PINEDA',
        40 => 'DIANA MARCELA DURAN ALVARADO',
        41 => 'EDWIN SAYED VERA GAMBOA',
        42 => 'YANETH AMADO SANCHEZ',
        43 => 'ROBERTO PLATA GOMEZ',
        44 => 'JAVIER ALEXANDER CRIADO RODRIGUEZ',
    ],

    'autorizado_por' => [
        'MARIO ALBERTO AREVALO PERDOMO',
    ],

    'prioridades' => [
        'URGENTE',
        'ALTA',
        'MEDIA',
        'BAJA',
    ],

    'unidades' => [
        'GALONES',
        'KILO',
        'LITRO',
        'METROS',
        'PAQUETE',
        'ROLLO',
        'UNIDAD',
        'PAR',
        'BULTO',
        'LIBRA',
        'PULGADAS',
        'M^2',
        'CUÑETE',
        'PIMPINA',
        'OTRO',
    ],

    'centros_trabajo' => [
        'SEDE ADMINISTRATIVA',
        'SEDE PLANTA',
        'TRANSVERSAL',   // aplica a ambas sedes
    ],
];
