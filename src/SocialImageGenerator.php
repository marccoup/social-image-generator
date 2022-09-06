<?php

namespace Marccoup\SocialImageGenerator;

use Intervention\Image\AbstractFont;
use Intervention\Image\AbstractShape;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class SocialImageGenerator
{
    public Image $image;

    // canvas props

    public int $width = 1200;

    public int $height = 630;

    public int $safeAreaBoundary = 100;

    public string $backgroundColorHex = '#ffffff';

    // title props

    public string $titleText = '';

    public int $titleTextSize = 72;

    public string $titleTextColorHex = '#000000';

    // footer props

    public string $footerText = '';

    public int $footerTextSize = 36;

    public string $footerTextColorHex = '#000000';

    // lattice props

    public bool $withLattice = true;

    public int $latticeSize = 30;

    public string $latticeColorHex = '#dddddd';

    public int $latticeLineWidth = 1;

    // border props

    public bool $withBorder = true;

    public string $borderColorHex = '#000000';

    public int $borderWidth = 30;

    // object creation methods

    public function __construct(public ImageManager $manager, public string $fontFile) { }

    public static function make(ImageManager $manager, string $fontFile): self
    {
        return new self(manager: $manager, fontFile: $fontFile);
    }

    // canvas methods

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function safeAreaBoundary(int $safeAreaBoundary): self
    {
        $this->safeAreaBoundary = $safeAreaBoundary;

        return $this;
    }

    public function backgroundColorHex(string $backgroundColorHex): self
    {
        $this->backgroundColorHex = $backgroundColorHex;

        return $this;
    }

    // title methods

    public function titleText(string $titleText): self
    {
        $this->titleText = $titleText;

        return $this;
    }

    public function titleTextSize(int $titleTextSize): self
    {
        $this->titleTextSize = $titleTextSize;

        return $this;
    }

    public function titleTextColorHex(string $titleTextColorHex): self
    {
        $this->titleTextColorHex = $titleTextColorHex;

        return $this;
    }

    // footer methods

    public function footerText(string $footerText): self
    {
        $this->footerText = $footerText;

        return $this;
    }

    public function footerTextSize(int $footerTextSize): self
    {
        $this->footerTextSize = $footerTextSize;

        return $this;
    }

    public function footerTextColorHex(string $footerTextColorHex): self
    {
        $this->footerTextColorHex = $footerTextColorHex;

        return $this;
    }

    // lattice methods

    public function withLattice(): self
    {
        $this->withLattice = true;

        return $this;
    }

    public function latticeSize(int $latticeSize): self
    {
        $this->latticeSize = $latticeSize;

        return $this;
    }

    public function withoutLattice(): self
    {
        $this->withLattice = false;

        return $this;
    }

    public function latticeColorHex(string $latticeColorHex): self
    {
        $this->latticeColorHex = $latticeColorHex;

        return $this;
    }

    public function latticeLineWidth(int $latticeLineWidth): self
    {
        $this->latticeLineWidth = $latticeLineWidth;

        return $this;
    }

    // border methods

    public function withBorder(): self
    {
        $this->withBorder = true;

        return $this;
    }

    public function withoutBorder(): self
    {
        $this->withBorder = false;

        return $this;
    }

    public function borderColorHex(string $borderColorHex): self
    {
        $this->borderColorHex = $borderColorHex;

        return $this;
    }

    public function borderWidth(string $borderWidth): self
    {
        $this->borderWidth = $borderWidth;

        return $this;
    }

    // Image generation methods

    public function start(): self
    {
        $this->image ??= $this->manager->canvas($this->width, $this->height, $this->backgroundColorHex);

        return $this;
    }

    public function drawLattice(): self
    {
        $this->start();

        $polygonPoints = [];
        $xStep         = intval($this->width / $this->latticeSize);
        $yStep         = intval($this->height / $this->latticeSize);
        foreach (['top-left', 'bottom-right'] as $section) {
            $defaultY = $section === 'top-left' ? 0 : $this->height;
            $defaultX = $section === 'top-left' ? 0 : $this->width;

            for ($i = 1; $i <= $this->latticeSize; $i++) {
                $polygonPoints = array_merge($polygonPoints, [
                    $xStep * $i, $defaultY, // x, y
                    $defaultX, $yStep * $i, // x, y
                ]);

                if ($i < $this->latticeSize) {
                    $polygonPoints[] = $defaultX; // x
                    $polygonPoints[] = $yStep * ($i + 1); // y
                }
            }
        }

        $defaultX = $this->width;
        $defaultY = 0;

        for ($i = 0; $i <= ($this->latticeSize * 2); $i++) {
            $polygonPoints = array_merge($polygonPoints, [
                $defaultX, $yStep * $i, // x, y
                $defaultX - ($xStep * $i), $defaultY, // x, y
            ]);

            if ($i < ($this->latticeSize * 2)) {
                $polygonPoints[] = $defaultX - ($xStep * ($i + 1)); // x
                $polygonPoints[] = $defaultY; // y
            }
        }

        $polygonPoints[] = 0; // x
        $polygonPoints[] = 0; // y

        $this->image->polygon($polygonPoints, function (AbstractShape $draw) {
            $draw->border($this->latticeLineWidth, $this->latticeColorHex);
        });

        return $this;
    }

    public function drawBorder(): self
    {
        $this->start();

        $this->image->rectangle(0, 0, $this->width, $this->height, function (AbstractShape $shape) {
            $shape->border($this->borderWidth, $this->borderColorHex);
        });

        return $this;
    }

    public function writeTitle(): self
    {
        $this->start();

        $this->image->text($this->titleText, $this->safeAreaBoundary, $this->safeAreaBoundary,
            function (AbstractFont $font) {
                $font->align('left')
                     ->valign('top')
                     ->file($this->fontFile)
                     ->size($this->titleTextSize)
                     ->color($this->titleTextColorHex);
            });

        return $this;
    }

    public function writeFooter(): self
    {
        $this->start();

        $this->image->text($this->footerText, $this->safeAreaBoundary, $this->height - $this->safeAreaBoundary,
            function (AbstractFont $font) {
                $font->align('left')
                     ->valign('top')
                     ->file($this->fontFile)
                     ->size($this->footerTextSize)
                     ->color($this->footerTextColorHex);
            });

        return $this;
    }

    public function generate(): Image
    {
        $this->start();

        if ($this->withLattice) {
            $this->drawLattice();
        }

        if ($this->withBorder) {
            $this->drawBorder();
        }

        $this->writeTitle()
             ->writeFooter();

        return $this->image;
    }
}