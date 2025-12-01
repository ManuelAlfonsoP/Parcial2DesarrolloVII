<?php
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
