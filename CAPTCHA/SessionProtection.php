<?php

namespace CAPTCHA;

/**
 * Class SessionProtection
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class SessionProtection
{
    /**
     * @var Generator
     */
    private static $generator;

    /**
     * Get Generator
     *
     * @return Generator
     */
    public static function getGenerator()
    {
        // Create a generator if it doesn't exist
        if (null === self::$generator) {
            // Start session ? (PHP < 5.4)
            if ('' === session_id()) {
                session_start();
            }

            self::$generator = new Generator(new Storage($_SESSION));
        }

        return self::$generator;
    }

    /**
     * Create a captcha
     *
     * @param int   $size     Word size (default to Generator::CAPTCHA_SIZE)
     * @param int   $lifetime Captcha lifetime (default to Generator::CAPTCHA_LIFETIME)
     * @param array $options  Captcha image options
     *
     * @return Captcha
     */
    public static function create($size = Generator::CAPTCHA_SIZE, $lifetime = Generator::CAPTCHA_LIFETIME, array $options = array())
    {
        return self::getGenerator()->create($size, $lifetime, $options);
    }

    /**
     * Test whether a couple intention/value is valid or not
     *
     * @param string $id                      Captcha ID
     * @param string $value                   Captcha value
     * @param int    $wrongLetterCountAllowed Wrong letter count allowed (default to 0)
     *
     * @return bool
     */
    public static function isValid($id, $value, $wrongLetterCountAllowed = 0)
    {
        return self::getGenerator()->isValid($id, $value, $wrongLetterCountAllowed);
    }

    /**
     * Remove deprecated captchas from storage
     */
    public static function clean()
    {
        self::getGenerator()->clean();
    }
}
