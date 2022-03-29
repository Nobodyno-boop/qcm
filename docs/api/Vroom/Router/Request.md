---
title: Request
---

# Class Request

Class \Vroom\Router\Request









## Methods

### __construct()

```php

public __construct ( 
    \Vroom\Router\Route $route
 ): 
```






**Parameters**
: _route_ <code>[\Vroom\Router\Route](./Route.md)</code> 



### get()

```php

public get ( 
    mixed $path
 ): mixed
```






**Parameters**
: _path_ <code>mixed</code> 

**Returns**
: <code>mixed</code> 


### getBody()

```php

public getBody (  ): mixed
```







**Returns**
: <code>mixed</code> 


### getRoute()

```php

public getRoute (  ): \Vroom\Router\Route
```







**Returns**
: <code>[\Vroom\Router\Route](./Route.md)</code> 


### post()

```php

public post ( 
    mixed $path
 ): mixed
```






**Parameters**
: _path_ <code>mixed</code> 

**Returns**
: <code>mixed</code> 


### query()

```php

public query ( 
    string $key = ""
 ): mixed
```






**Parameters**
: _key_ <code>string</code> 

**Returns**
: <code>mixed</code> 


### redirect()

```php

public redirect ( 
    mixed $url
 ): mixed
```






**Parameters**
: _url_ <code>mixed</code> 

**Returns**
: <code>mixed</code> 




