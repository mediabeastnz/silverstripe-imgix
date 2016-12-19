<?php
/**
 * Converts Images to Imgix or vise versa
 *
 * @package silverstripe
 * @subpackage mysite
 */
class ImageImgixTask extends BuildTask
{
    /**
     * Title for CMS
     * @var string
     */
    protected $title = 'Image to Imgix';
    /**
     * Description for CMS
     * @var string
     */
    protected $description = 'Converts all Image classes to Imgix';
    public function run($request)
    {
        Debug::dump($request);
        $objects = Image::get();
        foreach ($objects as $image) {
            $image->setClassName('Imgix')->write();
        }
        echo "Converted {$objects->count()} Image objects to Imgix Objects";
    }
}

class ImgixImageTask extends BuildTask
{
    /**
     * Title for CMS
     * @var string
     */
    protected $title = 'Imgix to Image';
    /**
     * Description for CMS
     * @var string
     */
    protected $description = 'Converts all Imgix classes to Image';
    public function run($request)
    {
        $objects = Imgix::get();
        foreach ($objects as $image) {
            $image->setClassName('Image')->write();
        }
        echo "Converted {$objects->count()} Imgix objects to Image Objects";
    }
}
