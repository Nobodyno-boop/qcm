<?php

namespace App\Qcm;

use JetBrains\PhpStorm\ArrayShape;

class Question
{
    const TYPE_MULTI = "multi";
    const TYPE_SINGLE = "single";
    private string $id;
    private string $question;
    private array $answers;
    private int|array $correct;

    /**
     * @param string $question
     * @param array $answers
     * @param int|array $correct
     * @param string $id
     * @throws \Exception
     */
    public function __construct(string $question, array $answers, int|array $correct, string $id = "")
    {
        if (empty($id)) {
            $this->id = bin2hex(random_bytes(15));
        } else {
            $this->id = $id;
        }
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    #[ArrayShape(["id" => "string", "question" => "string", "answers" => "array", "correct" => "int"])]
    public function toJson(): array
    {
        return [
            "id" => $this->id,
            "question" => $this->question,
            "answers" => $this->answers,
            "correct" => $this->correct
        ];
    }

    #[ArrayShape(["id" => "string", "question" => "string", "answers" => "array"])]
    public function toUserJson(): array
    {
        return [
            "id" => $this->id,
            "question" => $this->question,
            "answers" => $this->answers,
        ];
    }

    /**
     * Return the current type of the question
     * @return string
     */
    public function getType(): string
    {
        return is_array($this->correct) ? self::TYPE_MULTI : self::TYPE_SINGLE;
    }

    /**
     * Create a new instance
     * @param string $question
     * @param array $answers
     * @param $correct
     * @return Question
     */
    public static function from(string $question, array $answers, $correct, string $id = ""): Question
    {
        return new Question($question, $answers, $correct, $id);
    }

}