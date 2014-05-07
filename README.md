CAPTCHA
=======

PHP5 CAPTCHA protection

## Instance

### Create a storage (session here)

```php
session_start();
$storage = new Storage($_SESSION);
```

### Create a generator

```php
$generator = new Generator($storage);
```

### Create a captcha

```php
$captcha = $generator->create();
```

### Validate a captcha

```php
$generator->isValid($captcha->getId(), $captcha->getValue());
```

### Remove deprecated captchas

```php
$generator->clean();
```

## Static

### Create a captcha

```php
$captcha = SessionProtection::create();
```

### Validate a captcha

```php
$valid = SessionProtection::isValid($captcha->getId(), $captcha->getValue());
```

### Remove deprecated captchas

```php
SessionProtection::clean();
```