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
