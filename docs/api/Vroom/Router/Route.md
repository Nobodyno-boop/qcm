---
title: Route
---

# Class Route

Class \Vroom\Router\Route

## Methods

### __construct()

```php

public __construct ( 
    array $data, 
    string $controller, 
    string $method
 ): 
```

**Parameters**
: _data_ <code>array</code>
: _controller_ <code>string</code>
: _method_ <code>string</code>

### getController()

```php

public getController (  ): string
```

**Returns**
: <code>string</code>

### getControllerMethod()

```php

public getControllerMethod (  ): mixed|string
```

**Returns**
: <code>mixed|string</code>

### getMethod()

```php

public getMethod (  ): string
```

**Returns**
: <code>string</code>

### getName()

```php

public getName (  ): string
```

**Returns**
: <code>string</code>

### getParameters()

```php

public getParameters (  ): array
```

**Returns**
: <code>array</code>

### getPath()

```php

public getPath (  ): string
```

**Returns**
: <code>string</code>

### getVars()

```php

public getVars (  ): array
```

**Returns**
: <code>array</code>

### getVarsNames()

```php

public getVarsNames (  ): array
```

**Returns**
: <code>array</code>

### hasVars()

```php

public hasVars (  ): bool
```

**Returns**
: <code>bool</code>

### match()

```php

public match ( 
    string $path, 
    string $method
 ): bool
```

**Parameters**
: _path_ <code>string</code>
: _method_ <code>string</code>

**Returns**
: <code>bool</code>

### trimPath()

```php

public static trimPath ( 
    string $path
 ): string
```

**Parameters**
: _path_ <code>string</code>

**Returns**
: <code>string</code> 




