![](/og-img.png)

Generate simple social images for the [Open Graph protocol](https://ogp.me/)

## Requirements

- PHP >= 8.1
- Fileinfo Extension
- One of the following image libraries:
  - GD Library >= 2.0
  - Imagick PHP extension >= 6.5.7

## Installation

```bash
composer require marccoup/social-image-generator
```

## Usage

### Driver configuration
This package depends on [intervention/image](https://github.com/Intervention/image) and requires an instance of their 
`ImageManager` class to be injected into the `SocialImageGenerator` for driver configuration.

```php
<?php

use Intervention\Image\ImageManager;
use Marccoup\SocialImageGenerator\SocialImageGenerator;

$imageManager = new ImageManager(['driver' => 'imagick']);
$generator    = SocialImageGenerator::make(
    $imageManager, 
    __DIR__.'/path/to/my/font.ttf'
);

// Do stuff with $generator
```

Refer to [the documentation](https://image.intervention.io/v2/introduction/configuration#driver-configuration) on driver
configuration for more information.

### Fonts

The only other thing required for this package to work is any `.ttf` font file to be used for the text written to the
generated images.

```php
<?php

use Intervention\Image\ImageManager;
use Marccoup\SocialImageGenerator\SocialImageGenerator;

$myFont    = __DIR__.'/path/to/my/font.ttf'; 
$generator = SocialImageGenerator::make(
    new ImageManager(['driver' => 'imagick']),
    $myFont
);

// Do stuff with $generator
```

### Building your image

This package provides a fluid API for building an image, with sensible (if boring) defaults.

```php
/** @var Marccoup\SocialImageGenerator\SocialImageGenerator $generator */

// Pixel width of the image (default: 1200)
$generator->width(1200);

// Pixel height of the image (default: 630)
$generator->height(630);

// Pixels within the image to pain content (default: 100)
$generator->safeAreaBoundary(100);

// The primary background colour (default: '#ffffff')
$generator->backgroundColorHex('#ffffff');

// The text used for the primary text on the image (default: '')
$generator->titleText('My Awesome Blog Post');

// The font size of the title text (default: 72)
$generator->titleTextSize(72);

// The colour of the title text (default: '#000000')
$generator->titleTextColorHex('#000000');

// The text used for the smaller text at the bottom on the image (default: '')
$generator->footerText('my-blog.example');

// The font size of the footer text (default: 36)
$generator->footerTextSize(36);

// The colour of the footer text (default: '#000000')
$generator->footerTextColorHex('#000000');

// Enable the lattice background effect (default)
$generator->withLattice();

// Disable the lattice background effect
$generator->withoutLattice();

// The amount of "diamonds" in one "row" of the lattice (default: 30)
$generator->latticeSize(30);

// The color of the lines making up the lattice (default: #dddddd)
$generator->latticeColorHex('#dddddd');

// The width of the lines making up the lattice (default: 1)
$generator->latticeLineWidth(1);

// Enable the border (default)
$generator->withBorder();

// Disable the border
$generator->withoutBorder();

// The colour of the border (default: '#000000')
$generator->borderColorHex('#000000');

// The width of the border (default: 30)
$generator->borderWidth(30);
```
Once your generator object has been configured how you like, you can generate your image.

The only non-chainable method is the one that should always be the final one in the chain -  `$generator->generate()` -
which will return an instance of `Intervention\Image\Image` for you to save, or otherwise do with as you wish.

```php
/** @var Marccoup\SocialImageGenerator\SocialImageGenerator $generator */

// Generates an image according to the configuration of the object and returns the resulting `Intervention\Image\Image` instance
$generator->generate();
```

Usually using the `$generator->generate()` method will be all you need but if you wish to write the sections of the 
image in a different order, want multiple lattices, or do anything else with the image you can interact with it in the 
following ways:

```php
/** @var Marccoup\SocialImageGenerator\SocialImageGenerator $generator */

// Manually start the generation of the image if it hasn't already been started
$generator->start();

// Manually draw the lattice on the image, ignores any prior calls to `$generator->withoutLattice()`
$generator->drawLattice();

// Manually draw the border on the image, ignores any prior calls the `$generator->withoutBorder()`
$generator->drawBorder();

// Manually write the title text to the image
$generator->writeTitle();

// Manually write the footer text to the image
$generator->writeFooter();

// Access the Intervention\Image\Image object itself to manipulate it how you like
$generator->image;
```

### Working example

The best example I can give, how I generated the image at the top of this readme. As long as you have the font, this
code should generate the exact same image.
```php
<?php

use Intervention\Image\ImageManager;
use Marccoup\SocialImageGenerator\SocialImageGenerator;

$imageManager = new ImageManager(['driver' => 'imagick']);

SocialImageGenerator::make($imageManager, __DIR__.'/fonts/Roboto-Medium.ttf')
                    ->titleText('Social Image Generator')
                    ->titleTextSize(84)
                    ->titleTextColorHex('#4a4a4a')
                    ->backgroundColorHex('#f9f9f9')
                    ->footerTextColorHex('#1d7484')
                    ->borderColorHex('#1d7484')
                    ->footerText('`composer require marccoup/social-image-generator`')
                    ->latticeSize(15)
                    ->generate()
                    ->save(__DIR__.'/og-img.png');
```