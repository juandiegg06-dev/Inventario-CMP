<?php
/*
 * Lista de impresoras de la empresa.
 *
 * Las impresoras SOLO se administran aquí (no desde la página).
 *  - Para agregar una: añade una línea con su IP, nombre y ubicación (opcional).
 *  - Para cambiar el nombre o la ubicación: edítalo aquí; se actualiza solo al abrir la página.
 *  - Para quitar una: borra su línea. Deja de mostrarse en la página, pero su historial
 *    de lecturas se conserva en la base de datos (si vuelves a agregar la misma IP, reaparece).
 *
 * La IP debe ser de red local (192.168.x.x, 10.x.x.x o 172.16-31.x.x).
 */
return [
    ['ip' => '192.168.2.179', 'nombre' => 'RICOH MP C6004ex (TESORERIA)', 'ubicacion' => null],
    ['ip' => '192.168.2.35',  'nombre' => 'RICOH IM C3500 (RECEPCION)',   'ubicacion' => null],
    ['ip' => '192.168.2.80',  'nombre' => 'CONSEJO',                      'ubicacion' => null],
];
