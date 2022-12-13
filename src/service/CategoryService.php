<?php

namespace App\service;

use App\exceptions\ValidationException;
use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws ValidationException
     */
    private function validateName(string $name, int $userId): void
    {
        if (strlen($name) > 255) {
            throw new ValidationException('Слишком длинный вопрос, максимально допустимое число символов - 255.');
        }
        if (!empty($this->categoryRepository->findByNameAndUserId($name, $userId))) {
            throw new ValidationException("Категория '$name' уже существует.");
        }
    }

    public function deleteById($categoryId): void
    {
        $this->categoryRepository->delete($categoryId);
    }

    /**
     * @throws ValidationException
     */
    public function addCategory(string $name, int $userId): void
    {
        $this->validateName($name, $userId);
        $this->categoryRepository->add($name, $userId);
    }

    public function findAllByUserId(int $userId): array
    {
        return $this->categoryRepository->findAllByUserId($userId);
    }

    public function findById(int $categoryId): bool|array|null
    {
        return $this->categoryRepository->findById($categoryId);
    }
}