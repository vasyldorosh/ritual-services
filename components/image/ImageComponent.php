<?php


namespace app\components\image; 

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;


class ImageComponent extends Component
{
    /**
     * Drivers available:
     *  GD - The default driver, requires GD2 version >= 2.0.34 (Debian / Ubuntu users note: Some functions, eg. sharpen may not be available)
     *  ImageMagick - Windows users must specify a path to the binary. Unix versions will attempt to auto-locate.
     * @var string
     */
    public $driver = 'GD';

    /**
     * ImageMagick driver params
     * @var array
     */
    public $params = array();

    public function init()
    {
        parent::init();
        if ($this->driver != 'GD' && $this->driver != 'ImageMagick') {
            die('driver must be GD or ImageMagick');
        }
    }

    public function load($image)
    {
        $config = array(
            'driver' => $this->driver,
            'params' => $this->params,
        );

        return new Image($image, $config);
    }
	
}

?>
