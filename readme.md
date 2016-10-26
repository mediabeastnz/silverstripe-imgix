# SilverStripe imgix

Integrates [imgix](https://docs.imgix.com/) with silverstripe.

## Requirements
 * SilverStripe ^3.2

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
  folder_path: 'assets/Uploads/' # note that this is the default path
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

```php
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
