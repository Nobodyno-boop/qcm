---
title: AbstractController
---

# Class AbstractController

Class \Vroom\Controller\AbstractController

## Methods

### __construct()

```php

public __construct ( 
    \Vroom\Router\Request $request
 ): 
```

**Parameters**
: _request_ <code>[\Vroom\Router\Request](../Router/Request.md)</code>

### getToken()

```php

public getToken (  ): string
```

Make a fresh CRSF Token and return it

The token is putting in the session

**Returns**
: <code>string</code>

### matchToken()

```php

public matchToken ( 
    string $token
 ): bool
```

**Parameters**
: _token_ <code>string</code>

**Returns**
: <code>bool</code>

### renderView()

```php

public renderView ( 
    string $view, 
    array $context = []
 ): mixed
```

**Parameters**
: _view_ <code>string</code>
: _context_ <code>array</code>

**Returns**
: <code>mixed</code>

### response()

```php

public response (  ): \Vroom\Router\Response
```

**Returns**
: <code>[\Vroom\Router\Response](../Router/Response.md)</code>

### twig()

```php

public twig (  ): \Twig\Environment
```

**Returns**
: <code>\Twig\Environment</code>

### url()

```php

public url (  ): string
```

**Returns**
: <code>string</code> 




