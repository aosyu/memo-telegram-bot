<?php

namespace App\repository;

class CategoryRepository extends AbstractRepository
{
    public function add($name, $userId): void
    {
        $stmt = $this->mysqli->prepare("INSERT INTO categories (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $name);
        $stmt->execute();
    }

    public function findById(int $id): bool|array|null
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM categories where id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_array();
    }

    public function findByName(string $name): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM categories where name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_all();
    }

    public function findByNameAndUserId(string $name, int $userId): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM categories where name = ? and user_id = ?");
        $stmt->bind_param("si", $name, $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all();
    }

    public function delete($categoryId): void
    {
        $stmt = $this->mysqli->prepare("delete from categories where id = ?");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
    }

    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM categories WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}