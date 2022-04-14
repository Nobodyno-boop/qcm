---
title: QueryBuilder
---

# Class QueryBuilder

Class \Vroom\Orm\Sql\QueryBuilder

## Methods

### __construct()

```php

public __construct ( 
    string|null $model = null
 ): 
```

**Parameters**
: _model_ <code>string|null</code>

### __toString()

```php

public __toString (  ): string
```

**Returns**
: <code>string</code>

### delete()

```php

public delete (  ): \Vroom\Orm\Sql\QueryBuilder
```

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### from()

```php

public from ( 
    ?string $table = "", 
    ?string $alias = null
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _table_ <code>?string</code>
: _alias_ <code>?string</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### fromModel()

```php

public static fromModel ( 
    \Vroom\Orm\Model\Model $model
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _model_ <code>[\Vroom\Orm\Model\Model](../Model/Model.md)</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### insert()

```php

public insert ( 
    array|\Vroom\Orm\Model\Model $insert
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _insert_ <code>array|[\Vroom\Orm\Model\Model](../Model/Model.md)</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### limit()

```php

public limit ( 
    int $limit = 1
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _limit_ <code>int</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### newInstance()

```php

public static newInstance ( 
    string $model = null
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _model_ <code>string</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### select()

```php

public select ( 
    string $select
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _select_ <code>string</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### update()

```php

public update ( 
    array|\Vroom\Orm\Model\Model $update
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _update_ <code>array|[\Vroom\Orm\Model\Model](../Model/Model.md)</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code>

### where()

```php

public where ( 
    array $where
 ): \Vroom\Orm\Sql\QueryBuilder
```

**Parameters**
: _where_ <code>array</code>

**Returns**
: <code>[\Vroom\Orm\Sql\QueryBuilder](./QueryBuilder.md)</code> 




