# Mini Biblioteca Personal - Proyecto PHP (sin frameworks)

Este proyecto es una implementación minimal funcional del *Mini Sistema de Biblioteca Personal* solicitado en el PDF.  
Contiene:
- Autenticación con Google OAuth (esqueleto; requiere Client ID/Secret)
- Búsqueda en Google Books API
- Guardado de libros favoritos en MySQL (PDO, consultas preparadas)
- Estructura OOP en PHP puro
- SQL script para crear la base de datos

**Importante:** Antes de ejecutar configure `config/config.php` con sus credenciales de Google y la conexión a la base de datos. Leer secciones siguientes.

## Estructura de carpetas
```
mini_library_project/
├─ assets/
│  └─ style.css
├─ config/
│  └─ config.php
├─ public/
│  └─ index.php
│  └─ callback.php
│  └─ logout.php
├─ src/
│  ├─ Database.php
│  ├─ Auth.php
│  ├─ GoogleBooks.php
│  ├─ User.php
│  └─ Book.php
├─ views/
│  ├─ header.php
│  ├─ footer.php
│  ├─ home.php
│  ├─ search_results.php
│  └─ my_books.php
├─ sql/
│  └─ schema.sql
└─ README.md
```

## Requisitos y setup rápido

1. Crear proyecto en Google Cloud Console:
   - Habilitar Google+ (o OAuth) / crear credenciales OAuth 2.0.
   - Configurar `Authorized redirect URIs` apuntando a `https://TU_DOMINIO/public/callback.php` (o `http://localhost/...` en desarrollo).
   - Obtener `CLIENT_ID` y `CLIENT_SECRET`.

2. Configurar `config/config.php`:
   - Rellenar `DB_*`, `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.

3. Importar la base de datos:
   - `mysql -u root -p < sql/schema.sql`

4. Servir `public/` como carpeta pública (DocumentRoot). Ejemplo en desarrollo: usar PHP built-in:
   ```
   cd public
   php -S localhost:8000
   ```

## Notas de seguridad
- Las credenciales nunca deben incluirse en repositorios públicos.
- Se usan consultas preparadas PDO para evitar SQL injection.
- El flujo de OAuth está implementado de forma básica: en producción usar almacenamiento seguro para tokens y HTTPS.



## Comentarios añadidos automáticamente

Se han añadido comentarios a los archivos PHP y vistas para facilitar la comprensión del proyecto.
