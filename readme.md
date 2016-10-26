# SilverStripe imgix

Integrates [imgix](https://docs.imgix.com/) with silverstripe.

## Requirements
 * [SilverStripe ^3.2](https://www.silverstripe.org/)
 * [imgix-php](https://github.com/imgix/imgix-php)

## Installation
```
composer require plato-creative/silverstripe-imgix
```

## License
See [License](license.md)

## Configuration

```yaml

Imgix:
  sub_domain: 'example'
  secure_url_token: '1234567891234' # (Optional) Defines the signkey for private sources
  folder_path: 'assets/Uploads/' # (Optional) Default path id assets/Uploads/
```

## Maintainers
 * Gorrie <gorrie@platocreative.co.nz>

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

## Adding to a DataObject
Add a has_one or has_many relationship to "Imgix" in the same way you would with "Image". See example below

```
<?php
class MyCustomPage extends Page
{
	private static $has_one = array(
		'Image' => 'Imgix'
	);

    private static $has_many = array(
		'Images' => 'Imgix',
	);

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();
		$fields->addFieldsToTab(
			'Root.Main',
			array(
				UploadField::create(
					'Image',
					'Image'
				),
				UploadField::create(
					'Images',
					'Images'
				)
			)
		);
		return $fields;
	}
}
```

## Manipulating images in Templates

```
$Image.ScaleWidth(150) // Returns a 150x75px image
$Image.ScaleMaxWidth(100) // Returns a 100x50px image (like ScaleWidth but prevents up-sampling)
$Image.ScaleHeight(150) // Returns a 300x150px image (up-sampled. Try to avoid doing this)
$Image.ScaleMaxHeight(150) // Returns a 200x100px image (like ScaleHeight but prevents up-sampling)
$Image.Fit(300,300) // Returns an image that fits within a 300x300px boundary, resulting in a 300x150px image (up-sampled)
$Image.FitMax(300,300) // Returns a 200x100px image (like Fit but prevents up-sampling)

// Cropping functions
$Image.Fill(150,150) // Returns a 150x150px image resized and cropped to fill specified dimensions (up-sampled)
$Image.FillMax(150,150) // Returns a 100x100px image (like Fill but prevents up-sampling)
$Image.CropWidth(150) // Returns a 150x100px image (trims excess pixels off the x axis from the center)
$Image.CropHeight(50) // Returns a 200x50px image (trims excess pixels off the y axis from the center)
$Image.Fill(150,150).Top // Crop from the top of the image, down
$Image.Fill(150,150).Bottom // Crop from the bottom of the image, up
$Image.Fill(150,150).Left // Crop from the left of the image, right
$Image.Fill(150,150).Right // Crop from the right of the image, left
$Image.Fill(150,150).Faces // If faces are detected in the image, attempts to center the crop to them
$Image.Fill(150,150).Entropy // Automatically finds and crops to an area of interest by looking for busy sections of the image
$Image.Fill(150,150).Edges // Automatically finds an crops to an area of interest by performing edge detection looking for objects within an image

// Padding functions (add space around an image)
$Image.Pad(100,100) // Returns a 100x100px padded image, with white bars added at the top and bottom
$Image.Pad(100, 100, CCCCCC) // Same as above but with a grey background

// Responsive functions
$Image.Responsive() // Returns an image that is dynamically generated based on the size of the viewport

// Automatic functions
$Image.Compress() // Returns an image using imgix's best-effort techniques to reduce the size of the image
$Image.Enhance() // Returns an image with more vibrant appearance
$Image.Format() // Imgix chooses the most appropriate file format for delivering your image based on the requesting web browser
$Image.Redeye() // Returns an image with red–eye removal is applied to detected faces
```
