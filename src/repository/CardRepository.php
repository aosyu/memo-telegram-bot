<?php

namespace App\Repository;

class CardRepository extends AbstractRepository
{
    public function add($category_id, string $question, string $answer)
    {
        $stmt = $this->mysqli->prepare(
            "INSERT INTO cards (category_id, question, answer, grade) VALUES (?, ?, ?, 'bad')");
        $stmt->bind_param("iss", $category_id, $question, $answer);
        $stmt->execute();
    }

    public function getRandomCard($categoryId): bool|array|null
    {
        $stmt = $this->mysqli->prepare(
            "SELECT * FROM cards 
         WHERE category_id = ?
         ORDER BY RAND() 
         LIMIT 1");
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_array();
    }

    public function setGrade(int $cardId, string $grade): void
    {
        $stmt = $this->mysqli->prepare(
            "UPDATE cards
         SET grade = ? 
         WHERE id = ?");
        $stmt->bind_param("si", $grade, $cardId);
        $stmt->execute();
    }

    public function getRandomCardByGrade($categoryId, $grade): bool|array|null
    {
        $stmt = $this->mysqli->prepare(
            "SELECT * FROM cards 
         WHERE category_id = ? AND grade = ?
         ORDER BY RAND() 
         LIMIT 1");
        $stmt->bind_param("is", $categoryId, $grade);
        $stmt->execute();
        return $stmt->get_result()->fetch_array();
    }

    public function findByQuestion(string $question, int $categoryId): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM cards where question = ? and category_id = ?");
        $stmt->bind_param("si", $question, $categoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all();
    }
}