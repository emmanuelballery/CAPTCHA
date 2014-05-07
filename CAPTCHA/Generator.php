<?php

namespace CAPTCHA;

/**
 * Class Generator
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Generator
{
    /**
     * Default captcha width
     *
     * @var int
     */
    const CAPTCHA_WIDTH = 300;

    /**
     * Default captcha height
     *
     * @var int
     */
    const CAPTCHA_HEIGHT = 80;

    /**
     * Default captcha lifetime
     *
     * @var int
     */
    const CAPTCHA_LIFETIME = 300;

    /**
     * Captcha storage key
     *
     * @var string
     */
    const CAPTCHA_STORAGE_KEY = '_captcha';

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Create a captcha
     *
     * @param int $width    Width (default to self::CAPTCHA_WIDTH)
     * @param int $height   Height (default to self::CAPTCHA_HEIGHT)
     * @param int $lifetime Captcha lifetime (default to self::CAPTCHA_LIFETIME)
     *
     * @return Captcha
     */
    public function create($width = self::CAPTCHA_WIDTH, $height = self::CAPTCHA_HEIGHT, $lifetime = self::CAPTCHA_LIFETIME)
    {
        // Get captchas
        if (null === $captchas = $this->getStorage()->get(self::CAPTCHA_STORAGE_KEY)) {
            $captchas = array();
        }

        // Store this captcha
        $captcha = new Captcha($width, $height, $lifetime);
        $captchas[$captcha->getId()] = $captcha;
        $this->getStorage()->set(self::CAPTCHA_STORAGE_KEY, $captchas);

        return $captcha;
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
    public function isValid($id, $value, $wrongLetterCountAllowed = 0)
    {
        if (null !== $captchas = $this->getStorage()->get(self::CAPTCHA_STORAGE_KEY)) {
            if (array_key_exists($id, $captchas)) {
                $captcha = $captchas[$id];
                if ($captcha instanceof Captcha) {
                    return $captcha->isValid($value, $wrongLetterCountAllowed);
                }
            }
        }

        return false;
    }

    /**
     * Remove deprecated captchas from storage
     */
    public function clean()
    {
        $preservedCaptchas = array();

        if (null !== $captchas = $this->getStorage()->get(self::CAPTCHA_STORAGE_KEY)) {
            foreach ($captchas as $captcha) {
                if ($captcha instanceof Captcha) {
                    if (false === $captcha->isDeprecated()) {
                        $preservedCaptchas[] = $captcha;
                    }
                }
            }
        }

        $this->getStorage()->set(self::CAPTCHA_STORAGE_KEY, $preservedCaptchas);
    }

    /**
     * Get Storage
     *
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
