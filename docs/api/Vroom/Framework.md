---
title: Framework
---

# Class Framework

Class \Vroom\Framework









## Methods

### __construct()

```php

public __construct ( 
    \Vroom\Config\Config $config, 
    \Vroom\App\AbstractApp $app
 ): 
```






**Parameters**
: _config_ <code>[\Vroom\Config\Config](Config/Config.md)</code> 
: _app_ <code>[\Vroom\App\AbstractApp](App/AbstractApp.md)</code> 



### getConfig()

```php

public getConfig (  ): \Vroom\Config\Config
```







**Returns**
: <code>[\Vroom\Config\Config](Config/Config.md)</code> 


### getRouter()

```php

public static getRouter (  ): \Vroom\Router\Router
```







**Returns**
: <code>[\Vroom\Router\Router](Router/Router.md)</code> 


### newInstance()

```php

public static newInstance ( 
    string $configPath, 
    \Vroom\App\AbstractApp $app
 ): \Vroom\Framework
```






**Parameters**
: _configPath_ <code>string</code> 
: _app_ <code>[\Vroom\App\AbstractApp](App/AbstractApp.md)</code> 

**Returns**
: <code>[\Vroom\Framework](./Framework.md)</code> 




