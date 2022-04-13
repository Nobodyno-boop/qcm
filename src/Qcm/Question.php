<?php

namespace App\Qcm;

use JetBrains\PhpStorm\ArrayShape;

class Question
{
    private string $question;
    private array $answers;
    private int $correct;

    /**
     * @param string $question
     * @param array $answers
     * @param int $correct
     */
    public function __construct(string $question, array $answers, int $correct)
    {
        $this->question = $question;
        $this->answers = $answers;
        $this->correct = $correct;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    /**
     * @return array
     */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @return int
     */
    public function getCorrect(): int
    {
        return $this->correct;
    }

    #[ArrayShape(["question" => "string", "answers" => "array", "correct" => "int"])]
    public function toJson(): array
    {
        return [
            "question" => $this->question,
            "answers" => $this->answers,
            "correct" => $this->correct
        ];
    }

}