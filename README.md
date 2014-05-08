CAPTCHA
=======

PHP5 CAPTCHA protection

## Instance

```php
// Create a storage (session here)
session_start();
$storage = new Storage($_SESSION);

// Create a generator
$generator = new Generator($storage);

// Create a captcha
$captcha = $generator->create();

// Validate a captcha
$generator->isValid($captcha->getId(), $captcha->getValue());

// Remove deprecated captchas
$generator->clean();
```

## Static

```php
// Create a captcha
$captcha = SessionProtection::create();

// Validate a captcha
$valid = SessionProtection::isValid($captcha->getId(), $captcha->getValue());

// Remove deprecated captchas
SessionProtection::clean();
```

## Examples

  * [Resources/examples/instance.php](https://github.com/emmanuelballery/CAPTCHA/blob/master/Resources/examples/instance.php)
  * [Resources/examples/static.php](https://github.com/emmanuelballery/CAPTCHA/blob/master/Resources/examples/static.php)
