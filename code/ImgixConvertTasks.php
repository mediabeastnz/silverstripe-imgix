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
        foreach (Image::get() as $image) {
            $image->setClassName('Imgix')->write();
        }
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
        foreach (Imgix::get() as $image) {
            $image->setClassName('Image')->write();
        }
    }
}
