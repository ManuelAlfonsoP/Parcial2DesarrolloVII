<?php

/*
  Archivo: mini_library_project/mini_library_project/src/Book.php
  Propósito:
    - Explica la responsabilidad principal de este archivo.
    - Describe las clases/funciones definidas aquí (si aplica).
    - Indica cómo interactúa con otras partes del proyecto.
    - Menciona requisitos previos (p. ej. dependencias, variables de configuración).
  Notas:
    - Mantén las credenciales fuera del código: usa config/config.php.
    - En producción, asegúrate de usar HTTPS y almacenamiento seguro para tokens.
*/

class Book {
    private $db;
    public function __construct(Database $db) {
        $this->db = $db->pdo();
    }
    public function saveForUser($userId, $googleBooksId, $title, $author, $image, $review) {
        $stmt = $this->db->prepare('INSERT INTO libros_guardados (user_id, google_books_id, titulo, autor, imagen_portada, reseña_personal, fecha_guardado) VALUES (:uid, :gid, :titulo, :autor, :imagen, :reseña, NOW())');
        $stmt->execute([
            ':uid' => $userId,
            ':gid' => $googleBooksId,
            ':titulo' => $title,
            ':autor' => $author,
            ':imagen' => $image,
            ':reseña' => $review,
        ]);
        return $this->db->lastInsertId();
    }
    public function getByUser($userId) {
        $stmt = $this->db->prepare('SELECT * FROM libros_guardados WHERE user_id = :uid ORDER BY fecha_guardado DESC');
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
    public function deleteByIdAndUser($id, $userId) {
        $stmt = $this->db->prepare('DELETE FROM libros_guardados WHERE id = :id AND user_id = :uid');
        $stmt->execute([':id' => $id, ':uid' => $userId]);
        return $stmt->rowCount();
    }
}
