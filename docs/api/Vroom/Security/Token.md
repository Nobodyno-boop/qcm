---
title: Token
---

# Class Token

Class \Vroom\Security\Token

Example usage:

```php
$freshToken = Token::(15, "mysuperurl");
```

## Properties

### token

```php
 public string token
```

### url

```php
 public string url
```

## Methods

### __construct()

```php

public __construct ( 
    string $token, 
    string $url
 ): 
```

**Parameters**
: _token_ <code>string</code>
: _url_ <code>string</code>

### __serialize()

```php

public __serialize (  ): array
```

**Returns**
: <code>array</code>

### __unserialize()

```php

public __unserialize ( 
    array $data
 ): void
```

**Parameters**
: _data_ <code>array</code>

### getToken()

```php

public static getToken ( 
    int $length = 15, 
    string $url = ""
 ): void|\Vroom\Security\Token
```

Generate a new instance of token with a random token.

Example:

```php
$freshToken = Token::(url: "myUrl");
$MyLongToken = Token::(30, "my");
```

**Parameters**
: _length_ <code>int</code>
: _url_ <code>string</code>

**Returns**
: <code>void|[\Vroom\Security\Token](./Token.md)</code>

### match()

```php

public match ( 
    string $token, 
    string $url
 ): bool
```

Verify if the token and url is the same.

Example:

```php
// request on /user/login
$token = "blalba";
$FreshToken = Token::(url: "myWrongUrl");
if(token->match($token, "/user/login") {
 // Will not pass because the url does not match with the token.
}

```

**Parameters**
: _token_ <code>string</code>
: _url_ <code>string</code>

**Returns**
: <code>bool</code> 




