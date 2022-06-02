---
title: Router
---

# Class Router

Class \Vroom\Router\Router

Router take always the last better

## Constants

### CONTAINER_NAMESPACE

```php

public CONTAINER_NAMESPACE = "_router"

```

## Methods

### addRoute()

```php

public addRoute ( 
    array $data, 
    mixed $controller
 ): mixed
```

**Parameters**
: _data_ <code>array</code>
: _controller_ <code>mixed</code>

**Returns**
: <code>mixed</code>

### getFromPrefix()

```php

public static getFromPrefix ( 
    string $prefix
 ): mixed
```

**Parameters**
: _prefix_ <code>string</code>

**Returns**
: <code>mixed</code>

### getRoutes()

```php

public getRoutes (  ): \Vroom\Router\Route[]
```

**Returns**
: <code>[\Vroom\Router\Route](./Route.md)[]</code>

### handle()

```php

public handle (  ): mixed
```

**Returns**
: <code>mixed</code> 




