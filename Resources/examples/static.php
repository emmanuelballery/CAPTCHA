<?php

require __DIR__ . '/../../CAPTCHA/Storage.php';
require __DIR__ . '/../../CAPTCHA/Generator.php';
require __DIR__ . '/../../CAPTCHA/SessionProtection.php';
require __DIR__ . '/../../CAPTCHA/Captcha.php';

use CAPTCHA\SessionProtection;

// Create a captcha
$captcha = SessionProtection::create();

// Debug
var_dump($captcha);

// Display img
echo sprintf('<input type="hidden" name="captcha" value="%s"/>', $captcha->getId());
echo sprintf('<img src="%s" alt="CAPTCHA"/>', $captcha->getImageSrc());

// Validate a captcha
$valid = SessionProtection::isValid($captcha->getId(), $captcha->getValue());

// Debug
var_dump($valid);

// Remove deprecated captchas
SessionProtection::clean();
