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
    protected $description = 'Converts all Image objects to Imgix';

    public function run($request)
    {
        $objects = Image::get();
        $count = $objects->count();
        foreach ($objects as $image) {
            $image->setClassName('Imgix')->write();
        }
        echo "Converted {$count} Image objects to Imgix Objects";
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
    protected $description = 'Converts all Imgix objects to Image';

    public function run($request)
    {
        $objects = Imgix::get();
        $count = $objects->count();
        foreach ($objects as $image) {
            $image->setClassName('Image')->write();
        }
        echo "Converted {$count} Imgix objects to Image Objects";
    }
}
