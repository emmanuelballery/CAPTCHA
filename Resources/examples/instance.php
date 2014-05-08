<?php

require __DIR__ . '/../../CAPTCHA/Storage.php';
require __DIR__ . '/../../CAPTCHA/Generator.php';
require __DIR__ . '/../../CAPTCHA/Captcha.php';

use CAPTCHA\Generator;
use CAPTCHA\Storage;

// Create a storage
session_start();
$storage = new Storage($_SESSION);

// Create a generator
$generator = new Generator($storage);

// Create a captcha
$captcha = $generator->create();

// Debug
var_dump($captcha);

// Display img
echo sprintf('<input type="hidden" name="captcha" value="%s"/>', $captcha->getId());
echo sprintf('<img src="%s" alt="CAPTCHA"/>', $captcha->getImageSrc());

// Validate a captcha
$valid = $generator->isValid($captcha->getId(), $captcha->getValue());

// Debug
var_dump($valid);

// Remove deprecated captchas
$generator->clean();
