<?php
/**
 * Represents an Image via Imgix
 *
 * @package silverstripe
 * @subpackage filesystem
 */

use Imgix\UrlBuilder;

class Imgix extends File
{
    const ORIENTATION_SQUARE = 0;
    const ORIENTATION_PORTRAIT = 1;
    const ORIENTATION_LANDSCAPE = 2;

    private static $sub_domain = null;

    private static $folder_path = 'assets/Uploads/';

    protected $responsive = false;

    protected $parameters = array();

    private static $casting = array(
        'Tag' => 'HTMLText',
    );

    /**
	 * @config
	 * @var int The width of an image thumbnail in a strip.
	 */
	private static $strip_thumbnail_width = 50;

	/**
	 * @config
	 * @var int The height of an image thumbnail in a strip.
	 */
	private static $strip_thumbnail_height = 50;

	/**
	 * @config
	 * @var int The width of an image thumbnail in the CMS.
	 */
	private static $cms_thumbnail_width = 100;

	/**
	 * @config
	 * @var int The height of an image thumbnail in the CMS.
	 */
	private static $cms_thumbnail_height = 100;

	/**
	 * @config
	 * @var int The width of an image thumbnail in the Asset section.
	 */
	private static $asset_thumbnail_width = 100;

	/**
	 * @config
	 * @var int The height of an image thumbnail in the Asset section.
	 */
	private static $asset_thumbnail_height = 100;

	/**
	 * @config
	 * @var int The width of an image preview in the Asset section.
	 */
	private static $asset_preview_width = 400;

	/**
	 * @config
	 * @var int The height of an image preview in the Asset section.
	 */
	private static $asset_preview_height = 200;

    /**
     * Return an XHTML img tag for this Image,
     * or NULL if the image file doesn't exist on the filesystem.
     *
     * @return string
     */
    public function getTag()
    {
        if($this->exists()) {
            $url = $this->getURL();
            $title = ($this->Title) ? $this->Title : $this->Filename;
            if($this->Title) {
                $title = Convert::raw2att($this->Title);
            } else {
                if(preg_match("/([^\/]*)\.[a-zA-Z0-9]{1,6}$/", $title, $matches)) {
                    $title = Convert::raw2att($matches[1]);
                }
            }
            if ($this->responsive) {
                $this->responsive = false; // reset for next image
                Requirements::javascript(SSIMGIX_DIR.'/thirdparty/imgix.js/dist/imgix.min.js');
                return "<img ix-src=\"$url\" alt=\"$title\" />";
            }
            return "<img src=\"$url\" alt=\"$title\" />";
        }
    }

    /**
     * Return an XHTML img tag for this Image.
     *
     * @return string
     */
    public function forTemplate()
    {
        return $this->getTag();
    }

    /**
     * Gets the relative URL accessible through the web.
     *
     * @uses Director::baseURL()
     * @return string
     */
    public function getURL()
    {
        $subDomain = $this->config()->get('sub_domain');
        if (!$subDomain) {
            user_error("Undefined sub_domain: Please Imgix sub_domain in your config", E_USER_ERROR);
        }
        $domain = "{$subDomain}.imgix.net";
        $urlBuilder = new UrlBuilder($domain);
        $originalFilePath = $this->getRelativePath();
        $imgixFilePath = str_ireplace($this->config()->get('folder_path'), '', $originalFilePath);
        $parameters = $this->parameters;
        $this->parameters = array(); // reset all parameters ready for the next image
        $this->extend('updateParameters', $parameters);
        return $urlBuilder->createURL($imgixFilePath, $parameters);
    }

    /**
     * Scale image proportionally to fit within the specified bounds
     *
     * @param integer $width The width to size within
     * @param integer $height The height to size within
     * @return Image|null
     */
    public function Fit($width, $height)
    {
        $this->setDimensions($width, $height);
        $this->setResample('fit','clip');
        return $this;
    }

    /**
     * Proportionally scale down this image if it is wider or taller than the specified dimensions.
     * Similar to Fit but without up-sampling. Use in templates with $FitMax.
     *
     * @uses Image::Fit()
     * @param integer $width The maximum width of the output image
     * @param integer $height The maximum height of the output image
     * @return Image
     */
    public function FitMax($width, $height)
    {
        $this->setDimensions($width, $height);
        $this->setResample('fit','max');
        return $this;
    }

    /**
     * Resize and crop image to fill specified dimensions.
     * Use in templates with $Fill
     *
     * @param integer $width Width to crop to
     * @param integer $height Height to crop to
     * @return Image|null
     */
    public function Fill($width, $height)
    {
        $this->setDimensions($width, $height);
        $this->setResample('fit','crop');
        return $this;
    }

    /**
     * Crop this image to the aspect ratio defined by the specified width and height,
     * then scale down the image to those dimensions if it exceeds them.
     * Similar to Fill but without up-sampling. Use in templates with $FillMax.
     *
     * @param integer $width The relative (used to determine aspect ratio) and maximum width of the output image
     * @param integer $height The relative (used to determine aspect ratio) and maximum height of the output image
     * @return Image
     */
    public function FillMax($width, $height)
    {
        $this->Fill($width, $height);
        $this->setParameter('max-w', $this->getOriginalWidth());
        $this->setParameter('max-h', $this->getOriginalHeight());
        return $this;
    }


    /**
     * Fit image to specified dimensions and fill leftover space with a solid colour (default white). Use in templates with $Pad.
     *
     * @param integer $width The width to size to
     * @param integer $height The height to size to
     * @return Image|null
     */
    public function Pad($width, $height, $backgroundColor='FFFFFF')
    {
        $this->setDimensions($width, $height);
        $this->setResample('fit','fill');
        $this->setParameter('bg', $backgroundColor);
        return $this;
    }


    /**
     * Scale image proportionally by width. Use in templates with $ScaleWidth.
     *
     * @param integer $width The width to set
     * @return Image|null
     */
    public function ScaleWidth($width)
    {
        $this->setDimensions($width);
        $this->setResample('fit','clip');
        return $this;
    }

    /**
    * Proportionally scale down this image if it is wider than the specified width.
    * Similar to ScaleWidth but without up-sampling. Use in templates with $ScaleMaxWidth.
    *
    * @uses Image::ScaleWidth()
    * @param integer $width The maximum width of the output image
    * @return Image
    */
    public function ScaleMaxWidth($width)
    {
        $this->ScaleWidth($width);
        $this->setParameter('max-w', $this->getOriginalWidth());
        return $this;
    }

    /**
     * Scale image proportionally by height. Use in templates with $ScaleHeight.
     *
     * @param integer $height The height to set
     * @return Image|null
     */
    public function ScaleHeight($height)
    {
        $this->setDimensions(null, $height);
        $this->setResample('fit','clip');
        return $this;
	}

    /**
     * Proportionally scale down this image if it is taller than the specified height.
     * Similar to ScaleHeight but without up-sampling. Use in templates with $ScaleMaxHeight.
     *
     * @uses Image::ScaleHeight()
     * @param integer $height The maximum height of the output image
     * @return Image
     */
    public function ScaleMaxHeight($height)
    {
        $this->ScaleHeight($height);
        $this->setParameter('max-h', $this->getOriginalHeight());
        return $this;
    }


    /**
     * Crop image on X axis if it exceeds specified width. Retain height.
     * Use in templates with $CropWidth. Example: $Image.ScaleHeight(100).$CropWidth(100)
     *
     * @uses Image::Fill()
     * @param integer $width The maximum width of the output image
     * @return Image
     */
    public function CropWidth($width)
    {
        if ($this->getOriginalWidth() > $width) {
            $this->Fill($width, $this->getOriginalHeight());
        }
        return $this;
    }

    /**
    * Crop image on Y axis if it exceeds specified height. Retain width.
    * Use in templates with $CropHeight. Example: $Image.ScaleWidth(100).CropHeight(100)
    *
    * @uses Image::Fill()
    * @param integer $height The maximum height of the output image
    * @return Image
    */
    public function CropHeight($height)
    {
        if ($this->getOriginalHeight() > $height) {
            $this->Fill($height, $this->getOriginalWidth());
        }
        return $this;
    }

    public function Responsive($boolean = true)
    {
        $this->responsive = $boolean;
        return $this;
    }

    /**
     * Resize this image for the CMS. Use in templates with $CMSThumbnail
     *
     * @return Image_Cached|null
     */
    public function CMSThumbnail()
    {
        return $this->Pad($this->stat('cms_thumbnail_width'),$this->stat('cms_thumbnail_height'));
    }

    /**
     * Resize this image for use as a thumbnail in a strip. Use in templates with $StripThumbnail.
     *
     * @return Image_Cached|null
     */
    public function StripThumbnail()
    {
        return $this->Fill($this->stat('strip_thumbnail_width'),$this->stat('strip_thumbnail_height'));
    }

    public function setResample($key, $value)
    {
        $this->setParameter($key, $value);
        return $this;
    }

    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
        return $this;
    }

    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    /**
     * Determine if this image is of the specified size
     *
     * @param integer $width Width to check
     * @param integer $height Height to check
     * @return boolean
     */
    public function isSize($width, $height)
    {
        return $this->isWidth($width) && $this->isHeight($height);
    }

    /**
    * Determine if this image is of the specified width
    *
    * @param integer $width Width to check
    * @return boolean
    */
    public function isWidth($width)
    {
        return !empty($width) && $this->getWidth() == $width;
    }

    /**
    * Determine if this image is of the specified width
    *
    * @param integer $height Height to check
    * @return boolean
    */
    public function isHeight($height)
    {
        return !empty($height) && $this->getHeight() == $height;
    }

    public function setDimensions($width = null, $height = null)
    {
        if (isset($width)) {
            $this->setParameter('w', $width);
        }
        if (isset($height)) {
            $this->setParameter('h', $height);
        }
        return $this;
    }

    /**
     * Get the dimensions of this Image.
     * @param string $dim If this is equal to "string", return the dimensions in string form,
     * if it is 0 return the height, if it is 1 return the width.
     * @return string|int|null
     */
    public function getDimensions($dim = "string")
    {
        if($this->getField('Filename')) {

            $imagefile = $this->getFullPath();
            if($this->exists()) {
                $size = getimagesize($imagefile);
                return ($dim === "string") ? "$size[0]x$size[1]" : $size[$dim];
            } else {
                return ($dim === "string") ? "file '$imagefile' not found" : null;
            }
        }
    }

    public function getOriginalWidth() {
        return $this->getDimensions(1);
    }

    /**
    * Get the width of this image.
    * @return int
    */
    public function getWidth()
    {
        return ($this->getParameter('w')) ? $this->getParameter('w') : $this->getOriginalWidth(0);
    }

    public function getOriginalHeight() {
        return $this->getDimensions(1);
    }

    /**
     * Get the height of this image.
     * @return int
     */
    public function getHeight() {
        return ($this->getParameter('h')) ? $this->getParameter('h') : $this->getOriginalHeight(1);
    }

    /**
     * Get the orientation of this image.
     * @return ORIENTATION_SQUARE | ORIENTATION_PORTRAIT | ORIENTATION_LANDSCAPE
     */
    public function getOrientation()
    {
        $width = $this->getWidth();
        $height = $this->getHeight();
        if($width > $height) {
            return self::ORIENTATION_LANDSCAPE;
        } elseif($height > $width) {
            return self::ORIENTATION_PORTRAIT;
        } else {
            return self::ORIENTATION_SQUARE;
        }
    }
}
