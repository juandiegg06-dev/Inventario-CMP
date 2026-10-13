# Base de datos - Inventario-CMP

Este archivo (`inventario_cmp.sql`) contiene la estructura de tablas y los datos
para correr el proyecto localmente con XAMPP.

## Cómo importarla

1. Abre XAMPP y arranca Apache y MySQL.
2. Ve a `http://localhost/phpmyadmin` (o el puerto que uses, ej. `localhost:3307` según tu config.php).
3. Click en "Nueva" (crear base de datos) y créala con el nombre `inventario_cmp`.
4. Entra a esa base, ve a la pestaña "Importar", selecciona `inventario_cmp.sql` y dale a "Continuar".
5. Verifica que `php/config.php` tenga estos datos (ya vienen así por defecto):
   - Host: `localhost:3307`
   - Usuario: `root`
   - Contraseña: (vacía)
   - Base de datos: `inventario_cmp`

Listo, el proyecto debería funcionar apuntando a `http://localhost/Inventario-CMP/`.

## Tabla `vigencias`

Guarda licencias, garantías y suscripciones (de hardware o software) con su fecha de vencimiento, opcionalmente ligadas a un equipo.
Se crea automáticamente la primera vez que se abre la página; `vigencias.sql` sirve para crearla a mano y ya viene incluida en `inventario_cmp.sql`.
Estados: vigente, por vencer (30 días o menos) y vencida. Al eliminar una vigencia va a la papelera.

## Impresoras

Tablas `impresoras` y `impresora_lecturas` (se crean solas; `impresoras.sql` para crearlas a mano). La página consulta el contador de la impresora desde el servidor local (PHP) y guarda cada lectura con fecha y hora: copia, impresión y escaneado, en color y B/N. Solo acepta IPs de red local.

Las impresoras **no se agregan ni se eliminan desde la página**: se definen en `php/impresoras.php` (IP, nombre y ubicación). Al abrir la página, la tabla `impresoras` se sincroniza con esa lista. Si quitas una impresora del archivo, deja de mostrarse pero su historial de lecturas se conserva.

La actualización automática de contadores (20, 30, 40 min, 1 h, 3 h o un intervalo personalizado de 1 a 1440 minutos) se ejecuta desde el navegador, así que solo funciona mientras la página está abierta en la sección Impresoras.

## Requisiciones

Tablas `requisiciones` y `requisicion_items` (se crean solas; `requisiciones.sql` para crearlas a mano). Reproducen el FORMATO DE REQUISICIÓN DE COMPRAS (MA-PC-FT-001): la página permite llenarlo, lo guarda en el historial y descarga el PDF.

- **Listas** (empresas, áreas, solicitantes, autorizado por, prioridades, unidades y centros de trabajo): se editan en `php/requisiciones.php`. Cada requisición guarda una copia de los nombres, así que cambiar una lista no altera las ya exportadas.
- **Consecutivo**: se asigna al guardar/exportar, de uno en uno y sin repetirse (aunque dos personas exporten al mismo tiempo). `consecutivo_inicial` en `php/requisiciones.php` define el número de la primera.
- **REQ N°**: `EMPRESA-ÁREA-SOLICITANTE-CONSECUTIVO`, por ejemplo `CMPN-ST-44-7`.
- **PDF**: se genera en el navegador con `js/jspdf.umd.min.js` (incluido en el proyecto, no necesita internet) y `js/requisicion-pdf.js`. El logo está en `img/logo-coomulpinort.jpg`.

