<?php

namespace CAPTCHA;

/**
 * Class Captcha
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class Captcha
{
    /**
     * ID
     *
     * @var string
     */
    private $id;

    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Creation timestamp
     *
     * @var int
     */
    private $created;

    /**
     * Lifetime in seconds
     *
     * @var int
     */
    private $lifetime;

    /**
     * Image data for HTML
     *
     * @var string
     */
    private $imageData;

    /**
     * Font files
     *
     * @var array
     */
    private $fonts;

    /**
     * Default image options
     *
     * @var array
     */
    private $defaultOptions = array(
        'width' => 300,
        'height' => 100,
        'backgroundRedMin' => 230,
        'backgroundRedMax' => 255,
        'backgroundGreenMin' => 230,
        'backgroundGreenMax' => 255,
        'backgroundBlueMin' => 230,
        'backgroundBlueMax' => 255,
        'letterRedMin' => 0,
        'letterRedMax' => 60,
        'letterGreenMin' => 0,
        'letterGreenMax' => 60,
        'letterBlueMin' => 0,
        'letterBlueMax' => 60,
        'angleMin' => -10,
        'angleMax' => 10,
        'fontSize' => 30,
        'xMinPercentage' => 0.05,
        'xMaxPercentage' => 0.05,
        'yMinPercentage' => 0.4,
        'yMaxPercentage' => 0.4,
        'lineMin' => 5,
        'lineMax' => 10,
        'lineRedMin' => 150,
        'lineRedMax' => 200,
        'lineGreenMin' => 150,
        'lineGreenMax' => 200,
        'lineBlueMin' => 150,
        'lineBlueMax' => 200,
        'wordLetters' => 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789',
    );

    /**
     * Image options
     *
     * @var array
     */
    private $options;

    /**
     * @param int   $size     Word size
     * @param int   $lifetime Lifetime in seconds
     * @param array $options  Image options
     */
    function __construct($size, $lifetime, array $options = array())
    {
        $this->id = uniqid(mt_rand(), true);
        $this->lifetime = $lifetime;
        $this->created = time();
        $this->fonts = glob(__DIR__ . '/../Resources/fonts/*');
        $this->options = array_merge($this->defaultOptions, $options);
        $this->value = $this->generateValue($size);
    }

    /**
     * Test whether this captcha is valid or not
     *
     * @param string $value                   Captcha value
     * @param int    $wrongLetterCountAllowed Wrong letter count allowed (default to 0)
     *
     * @return bool
     */
    public function isValid($value, $wrongLetterCountAllowed = 0)
    {
        if ($this->isDeprecated()) {
            return false;
        }

        $valueLetters = str_split($value);
        $realLetters = str_split($this->value);
        $error = 0;
        foreach ($realLetters as $key => $letter) {
            if (false === array_key_exists($key, $valueLetters) || $letter !== $valueLetters[$key]) {
                $error++;
            }
        }

        return $error <= $wrongLetterCountAllowed;
    }

    /**
     * Test whether this captcha is deprecated or not
     *
     * @return bool
     */
    public function isDeprecated()
    {
        return (time() > ($this->created + $this->lifetime));
    }

    /**
     * Get image data for HTML
     *
     * @return string
     */
    public function getImageSrc()
    {
        if (null !== $this->imageData) {
            return $this->imageData;
        }

        // Choose one font
        $font = $this->fonts[mt_rand(0, count($this->fonts) - 1)];

        // Prepare size
        $width = $this->options['width'];
        $height = $this->options['height'];

        // Prepare text area
        $xMin = intval($this->options['xMinPercentage'] * $width);
        $xMax = intval($this->options['xMaxPercentage'] * $width);
        $letters = array_values(str_split($this->value));
        $letterX = intval(($width - $xMin - $xMax) / count($letters));

        // Create image and background
        $image = imagecreate($width, $height);
        imagecolorallocate(
            $image,
            mt_rand($this->options['backgroundRedMin'], $this->options['backgroundRedMax']),
            mt_rand($this->options['backgroundGreenMin'], $this->options['backgroundGreenMax']),
            mt_rand($this->options['backgroundBlueMin'], $this->options['backgroundBlueMax'])
        );

        // Add lines
        $lineCount = mt_rand($this->options['lineMin'], $this->options['lineMax']);
        $lineColor = imagecolorallocate(
            $image,
            mt_rand($this->options['lineRedMin'], $this->options['lineRedMax']),
            mt_rand($this->options['lineGreenMin'], $this->options['lineGreenMax']),
            mt_rand($this->options['lineBlueMin'], $this->options['lineBlueMax'])
        );
        for ($i = 0; $i < $lineCount; $i++) {
            imagedashedline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
        }

        // Add letters
        foreach ($letters as $pos => $letter) {
            $textColor = imagecolorallocate(
                $image,
                mt_rand($this->options['letterRedMin'], $this->options['letterRedMax']),
                mt_rand($this->options['letterGreenMin'], $this->options['letterGreenMax']),
                mt_rand($this->options['letterBlueMin'], $this->options['letterBlueMax'])
            );

            imagettftext(
                $image,
                $this->options['fontSize'],
                -45 + mt_rand($this->options['angleMin'] + 45, $this->options['angleMax'] + 45),
                $xMin + $pos * $letterX,
                mt_rand(intval($this->options['yMinPercentage'] * $height), $height - intval($this->options['yMaxPercentage'] * $height)),
                $textColor,
                $font,
                $letter
            );
        }

        // Flush the image
        ob_start();
        imagepng($image);
        $raw = ob_get_clean();

        // Destroy the resource
        imagedestroy($image);

        return $this->imageData = sprintf('data:image/png;charset=utf-8;base64,%s', base64_encode($raw));
    }

    /**
     * Generate value
     *
     * @param int $size
     *
     * @return string
     */
    public function generateValue($size)
    {
        $letters = str_split($this->options['wordLetters']);
        $str = '';
        do {
            $str .= $letters[mt_rand(0, count($letters) - 1)];
        } while (--$size > 0);

        return $str;
    }

    /**
     * Set Id
     *
     * @param string $id Id
     *
     * @return Captcha
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Value
     *
     * @param string $value Value
     *
     * @return Captcha
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Created
     *
     * @param int $created Created
     *
     * @return Captcha
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get Created
     *
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set Lifetime
     *
     * @param int $lifetime Lifetime
     *
     * @return Captcha
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get Lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set Options
     *
     * @param array $options Options
     *
     * @return Captcha
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get Options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set Fonts
     *
     * @param array $fonts Fonts
     *
     * @return Captcha
     */
    public function setFonts($fonts)
    {
        $this->fonts = $fonts;

        return $this;
    }

    /**
     * Get Fonts
     *
     * @return array
     */
    public function getFonts()
    {
        return $this->fonts;
    }
}
