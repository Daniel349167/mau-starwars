# Registro de verificación

Verificación inicial: 15 de noviembre de 2026.

## Entorno

- Windows, PHP 8.2.12 y Node 22.19.0.
- Laravel 12.72.0, Vue 3.5.45 e Inertia Laravel 2.0.28 / Vue 2.3.29.
- MariaDB 10.4.32 mediante la conexión `mysql` y SQLite en memoria para pruebas.
- Base de demostración y base de pruebas separadas.

## Automatizado

- 48 casos y 197 aserciones correctas en SQLite.
- Los mismos 48 casos y 197 aserciones correctas en MariaDB.
- Compilación de producción con Vite correcta.
- Composer y npm no reportaron vulnerabilidades al instalar las dependencias.

## Comprobaciones en navegador

- Crear un personaje con medidas y planeta; comprobar mensaje y datos guardados.
- Abrir la edición con campos prellenados; guardar un nuevo nombre.
- Abrir la confirmación de eliminación y eliminar el registro creado para la prueba.
- Importar el ID 1 desde SWAPI real; comprobar Luke Skywalker, 172 cm, 77 kg y Tatooine.
- Intentar nuevamente el ID 1; comprobar el mensaje de duplicado en el modal.
- Consultar el ID 17; comprobar el error 404 sin crear un registro.
- Buscar por nombre; comprobar que Luke sea el único resultado y limpiar el filtro.
- Revisar el diseño a 1280 px y 390 px de ancho.

Los fallos HTTP, timeouts, respuestas inválidas y condiciones de carrera se reproducen de manera determinista con HTTP fakes en las pruebas de integración.
