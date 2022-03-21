<?php

namespace Vroom\Security;

class Token
{
    public string $token;
    public string $url;

    /**
     * @param string $token
     * @param string $url
     */
    public function __construct(string $token, string $url)
    {
        $this->token = $token;
        $this->url = $url;
    }


    public function __unserialize(array $data): void
    {
        $this->token = $data['token'];
        $this->url = $data['url'];
    }

    public function __serialize(): array
    {
        return [
            "token" => $this->token,
            "url" => $this->url
        ];
    }


    public function match(string $token, string $url): bool
    {
        return ($token === $this->token && $url === $this->url);
    }

    public static function getToken(int $length = 15, $url = "")
    {
        $h = null;
        try {
            $h = bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            die($e);
        }

        return new Token($h, $url);
    }
}