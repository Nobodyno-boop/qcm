<?php

namespace App\Qcm;

class Qcm
{
    /**
     * @var array{Question}
     */
    private array $qcm;
    /**
     * @var array must
     */
    private array $responses;

    /**
     * @var string Version of qcm when update by new Question
     */
    private string $version;

    /**
     * @param $qcm
     */
    public function __construct($qcm, string $version = "")
    {
        if (empty($version)) {
            $this->version = bin2hex(random_bytes(15));
        } else {
            $this->version = $version;
        }
        $this->qcm = $qcm;
    }

    /**
     * @param array|null $response
     * @return bool
     */
    public function isValid(array $response = null): bool
    {
        if ($response == null) {
            if ($this->responses != null) {
                $response = $this->responses;
            } else return false;
        }

        if (count($response) !== count($this->qcm)) {
            return false;
        }

        for ($i = 0; $i < count($response); $i++) {
            $choice = $response[$i];
            if (!isset($choice['id'])) {
                return false;
            }
            $answer = $this->getQuestionById($choice['id']);
            if ($answer) {
                if (is_array($answer->getCorrect())) {
                    return true;
                } else {
                    if (!(0 <= $choice['answer']) && $choice['answer'] <= count($answer->getAnswers()) - 1 && $answer->getId() !== $choice['id']) {
                        return false;
                    }
                }

            } else return false;
        }
        return true;
    }

    public function checkResponse(): array
    {
        $wrongAnswers = [];
        for ($i = 0; $i < count($this->responses); $i++) {
            $answer = $this->responses[$i];
            if (isset($answer['id'])) {
                $question = $this->getQuestionById($answer['id']);
                if ($question) {
                    $correct = $question->getCorrect();
                    if (is_array($correct)) {
                        $check = $answer['answer'];
                        if (count($check) !== count($correct)) {
                            $wrongAnswers[] = [...$answer, "correct" => $correct];
                        }
                    } else {
                        $check = intval($answer['answer']);
                        if ($check !== -1 && $check !== $correct) {
                            $wrongAnswers[] = [...$answer, "correct" => $correct];
                        }
                    }
                }
            }
        }
        return $wrongAnswers;
    }

    public function generateStats(): array
    {
        if ($this->isValid()) {
            $questionCount = count($this->qcm);
            $wrong = $this->checkResponse();
            $wrongCount = count($wrong);
            //remove decimal by passing float to int
            $percent = intval(floor(($questionCount - $wrongCount) * 100 / $questionCount));
            if ($percent < 0) {
                $percent = 0;
            }
            return ["version" => $this->version, "percent" => $percent, "question" => $questionCount, "error" => $wrongCount, "errors" => $wrong, "answers" => $this->responses];
        }
        return [];
    }


    private function getQuestionById(string $id): Question|null
    {
        return array_values(array_filter($this->qcm, function ($map) use ($id) {
                if ($map->getId() === $id) {
                    return $map;
                }
            }))[0] ?? null;
    }

    /**
     * @param array $qcm
     * @throws \Exception
     */
    public function setQcm(array $qcm): void
    {
        $this->qcm = $qcm;
        $this->version = bin2hex(random_bytes(15));
    }

    /**
     * @return array
     */
    public function getQcm(): array
    {
        return $this->qcm;
    }

    /**
     * @return array
     */
    public function getQcmAsJson(): array
    {
        $question = array_map(function ($value) {
            return $value->toUserJson();
        }, $this->qcm);

        return ["version" => $this->version, "question" => $question];
    }

    public static function from(array $data): Qcm
    {

        $version = $data['version'];
        $questions = [];
        foreach ($data['question'] as $question) {
            $questions[] = Question::from($question['question'], $question['answers'], $question['correct'], $question['id']);
        }
        return new Qcm($questions, $version);
    }


    /**
     * @param array $responses
     */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

}