# O2System Image
O2System Image is a PHP image handling and manipulation library for O2System Framework providing an easier and expressive way to manipulate an image. Allows different PHP Image Processor and Generator to be used.
 
### Supported PHP Image Processors and Generator Drivers
| Processors | Support | Tested  | &nbsp; |
| ------------- |:-------------:|:-----:| ----- |
| GD2 | ```Yes``` | ```Yes``` | http://php.net/image |
| GMagick | ```Yes``` | ```Yes``` | http://php.net/gmagick |
| ImageMagick | ```Yes``` | ```Yes``` | http://php.net/imagemagick |

Installation
------------
The best way to install [O2System Image](https://packagist.org/packages/o2system/image) is to use [Composer](http://getcomposer.org)
```
composer require o2system/image
```

Manual Installation
------------
1. Download the [master zip file](https://github.com/o2system/image/archive/master.zip).
2. Extract into your project folder.
3. Require the autoload.php file.<br>
```php
require your_project_folder_path/image/src/autoload.php
```

Usage Example
-------------
```php
use O2System\Image;

$manipulation = new Image\Manipulation( new Image\Datastructures\Config() );
$manipulation->setImageFile( PATH_STORAGE . 'images/kawah-putih.jpg' );

if( $manipulation->scaleImage( 15 ) ) {
    $manipulation->displayImage();
}
```

Documentation is available on this repository [wiki](https://github.com/o2system/image/wiki) or visit this repository [github page](https://o2system.github.io/image).

Ideas and Suggestions
---------------------
Please kindly mail us at [o2system.framework@gmail.com](mailto:o2system.framework@gmail.com).

Bugs and Issues
---------------
Please kindly submit your [issues at Github](http://github.com/o2system/image/issues) so we can track all the issues along development and send a [pull request](http://github.com/o2system/image/pulls) to this repository.

System Requirements
-------------------
- PHP 5.6+
- [Composer](http://getcomposer.org)

Credits
-------
* Founder and Lead Projects: [Steeven Andrian Salim](http://steevenz.com)
* Github Pages Designer and Writer: [Teguh Rianto](http://teguhrianto.tk)

Fonts Credits
-------------
* Jellyka Saint Andrew's Queen by [Jellyka Neveran](http://www.cuttyfruty.com/enhtml/jellyka.php) used as default signature font.
* Express Way Regular - Truetype Font by [Typodermic Fonts](http://typodermicfonts.com) used as default copyright font.

Photographs Example Credits
--------------
* Kawah Putih by Poniman Mulijadi - Braunberrie Timeless Portraiture
> All photographs above is used as examples in the script O2System Framework.