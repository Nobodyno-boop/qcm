---
title: Repository
---

# Class Repository

Class \Vroom\Orm\Repository

## Methods

### __construct()

```php

public __construct ( 
    string $model
 ): 
```

**Parameters**
: _model_ <code>string</code>

### findBy()

```php

public findBy ( 
    string $key, 
    mixed $value
 ): mixed
```

**Parameters**
: _key_ <code>string</code>
: _value_ <code>mixed</code>

**Returns**
: <code>mixed</code>

### get()

```php

public get ( 
    mixed $value, 
    mixed $key = null
 ): mixed
```

**Parameters**
: _value_ <code>mixed</code>
: _key_ <code>mixed</code>

**Returns**
: <code>mixed</code>

### getAll()

```php

public getAll ( 
    int $limit = 10
 ): mixed
```

**Parameters**
: _limit_ <code>int</code>

**Returns**
: <code>mixed</code>

### toModel()

```php

public toModel ( 
    mixed $var
 ): ?object
```

**Parameters**
: _var_ <code>mixed</code>

**Returns**
: <code>?object</code> 




