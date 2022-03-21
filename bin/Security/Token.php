<?php

namespace Vroom\Security;

/**
 *
 * Example usage:
 * ```php
 * $freshToken = Token::(15, "mysuperurl");
 * ```
 *
 */
class Token
{
    /**
     * @var string random token
     */
    public string $token;
    /**
     * @var string url used for match with the token
     */
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


    /**
     *
     * Verify if the token and url is the same.
     * Example:
     * ```php
     * // request on /user/login
     * $token = "blalba";
     * $FreshToken = Token::(url: "myWrongUrl");
     * if(token->match($token, "/user/login") {
     *  // Will not pass because the url does not match with the token.
     * }
     *
     * ```
     *
     * @param string $token
     * @param string $url
     * @return bool
     */
    public function match(string $token, string $url): bool
    {
        return ($token === $this->token && $url === $this->url);
    }

    /**
     * Generate a new instance of token with a random token.
     *  Example:
     * ```php
     * $freshToken = Token::(url: "myUrl");
     * $MyLongToken = Token::(30, "my");
     * ```
     * @param int $length
     * @param $url
     * @return void|Token
     */
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