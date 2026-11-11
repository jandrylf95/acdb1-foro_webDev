# Agenda Personal - Foro Desarrollo Web (Bimestre 1)

**Autor:** Jandry Loayza Feijoo

---

## 📌 Descripción del Proyecto
Aplicación web para la gestión de una agenda de contactos personal. Permite registro e inicio de sesión de usuarios, y operaciones CRUD completas (crear, listar, editar y eliminar contactos) con aislamiento de datos por usuario y consultas seguras mediante PDO.

---

## 🛠️ Tecnologías Utilizadas
* **Lenguaje Backend:** PHP 8.2.12
* **Base de Datos:** MySQL (conexión vía PDO)
* **Frontend:** HTML5 y JavaScript básico
* **Entorno de Servidor:** XAMPP (Apache + MySQL)

---

## 📋 Requisitos para su Ejecución
* Servidor local **XAMPP** (servicios de Apache y MySQL activos).
* PHP 8.0 o superior con extensión `pdo_mysql` habilitada.

---

## 🚀 Pasos para Instalar la Aplicación

1. **Ubicar el proyecto:**
   Colocar la carpeta `acdb1-foro_webDev` dentro del directorio `htdocs` de XAMPP:
   ```
   C:\xampp\htdocs\acdb1-foro_webDev
   ```

2. **Crear la Base de Datos:**
   * Ingresar a phpMyAdmin (`http://localhost/phpmyadmin`).
   * Crear la base de datos `acdb1-foro_webdev`.
   * Ejecutar el siguiente script SQL:
   ```sql
   CREATE TABLE usuarios (
       id_usuario INT AUTO_INCREMENT PRIMARY KEY,
       nombre VARCHAR(100) NOT NULL,
       correo VARCHAR(100) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

   CREATE TABLE contactos (
       id_contacto INT AUTO_INCREMENT PRIMARY KEY,
       id_usuario INT NOT NULL,
       nombre VARCHAR(100) NOT NULL,
       telefono VARCHAR(20) NOT NULL,
       correo VARCHAR(100),
       ciudad VARCHAR(50),
       FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
   )ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

3. **Verificar conexión:**
   Revisar que en [conexion.php](file:///c:/xampp/htdocs/acdb1-foro_webDev/conexion.php) coincidan el puerto (por defecto `3307`), usuario (`root`) y contraseña (`""`).

4. **Acceder a la aplicación:**
   Abrir en el navegador:
   ```
   http://localhost/acdb1-foro_webDev/login.php
   ```
