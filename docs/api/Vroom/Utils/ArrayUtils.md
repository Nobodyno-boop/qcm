---
title: ArrayUtils
---

# Class ArrayUtils

Class \Vroom\Utils\ArrayUtils









## Methods

### __construct()

```php

public __construct ( 
    array $array
 ): 
```






**Parameters**
: _array_ <code>array</code> 



### from()

```php

public static from ( 
    array $array
 ): \Vroom\Utils\ArrayUtils
```


Return a new instance of ArrayUtils



**Parameters**
: _array_ <code>array</code> 

**Returns**
: <code>[\Vroom\Utils\ArrayUtils](./ArrayUtils.md)</code> 


### get()

```php

public get ( 
    string $path
 ): mixed
```


Return search in the array with a path

```php
ArrayUtils::from([...])->get('user.id');
```

**Parameters**
: _path_ <code>string</code> 

**Returns**
: <code>mixed</code> 




