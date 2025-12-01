<?php
class User {
    private $db;
    public function __construct(Database $db) {
        $this->db = $db->pdo();
    }
    public function getByGoogleId($googleId) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE google_id = :gid LIMIT 1');
        $stmt->execute([':gid' => $googleId]);
        return $stmt->fetch();
    }
    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    public function create($email, $name, $googleId) {
        $stmt = $this->db->prepare('INSERT INTO users (email, nombre, google_id, fecha_registro) VALUES (:email, :nombre, :gid, NOW())');
        $stmt->execute([':email' => $email, ':nombre' => $name, ':gid' => $googleId]);
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }
}
