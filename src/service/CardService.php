<?php

namespace App\service;

use App\exceptions\ValidationException;
use App\Repository\CardRepository;
use App\utils\Grades;

class CardService
{
    private CardRepository $cardRepository;

    public function __construct(CardRepository $cardRepository)
    {
        $this->cardRepository = $cardRepository;
    }

    public function getRandomCard($categoryId): bool|array|null
    {
        $card = $this->cardRepository->getRandomCardByGrade($categoryId, Grades::GRADE_BAD);
        if ($card == null) {
            $card = $this->cardRepository->getRandomCardByGrade($categoryId, Grades::GRADE_OK);
            if ($card == null) {
                $card = $this->cardRepository->getRandomCardByGrade($categoryId, Grades::GRADE_EXC);
            }
        }
        return $card;
    }

    public function setGrade(int $cardId, string $grade): void
    {
        $this->cardRepository->setGrade($cardId, $grade);
    }

    /**
     * @throws ValidationException
     */
    public function add(int $categoryId, string $question, string $answer): void
    {
        if (!empty($this->cardRepository->findByQuestion($question, $categoryId))) {
            throw new ValidationException('Такой вопрос уже существует в выбранной категории.');
        }
        $this->cardRepository->add($categoryId, $question, $answer);
    }
}