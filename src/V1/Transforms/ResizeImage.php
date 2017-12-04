<?php

/**
 * Copyright (c) 2017-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Filesystem\V1\Transforms
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem
 */

namespace GanbaroDigital\Filesystem\V1\Transforms;

use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\TypeConverters;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;
use Imagick;

/**
 * resize an image on a filesystem
 */
class ResizeImage implements Transform
{
    /**
     * how wide do we want the image to become, in pixels?
     * @var int
     */
    protected $width;

    /**
     * how high do we want the image to become, in pixels?
     * @var int
     */
    protected $height;

    /**
     * how big are we scaling the image, whilst we're at it?
     *
     * 1 - normal resolution
     * 2 - 'Retina' resolution
     * 3 - 'iPhone X' resolution
     *
     * @var int
     */
    protected $scale;

    /**
     * what image quality (0-100%) do you want?
     * @var float
     */
    protected $quality;

    /**
     * our constructor
     *
     * @param int $width
     *        how wide do we want the image to become, in pixels?
     * @param int $height
     *        how high do we want the image to become, in pixels?
     * @param int $scale
     *        how big are we scaling the image?
     *        - 1 for normal resolution
     *        - 2 for 'Retina' resolution
     *        - 3 for 'iPhone X' resolution
     * @param float $quality
     *        image quality (0-100%)
     */
    public function __construct(int $width, int $height, int $scale, float $quality)
    {
        $this->width = $width;
        $this->height = $height;
        $this->scale = $scale;
        $this->quality = $quality;
    }

    /**
     * create a new ResizeImage transform object
     *
     * @param int $width
     *        how wide do we want the image to become, in pixels?
     * @param int $height
     *        how high do we want the image to become, in pixels?
     * @param int $scale
     *        how big are we scaling the image?
     *        - 1 for normal resolution
     *        - 2 for 'Retina' resolution
     *        - 3 for 'iPhone X' resolution
     * @param float $quality
     *        image quality (0-100%)
     * @return Transform
     *         a Transform object, ready to use
     */
    public static function apply(int $width, int $height, int $scale, float $quality) : Transform
    {
        return new static($width, $height, $scale, $quality);
    }

    /**
     * resize an image
     *
     * @param Filesystem $fs
     *        the filesystem we are working with
     * @param string|PathInfo $path
     *        the image we want to resize
     * @param int $width
     *        how wide do we want the image to become, in pixels?
     * @param int $height
     *        how high do we want the image to become, in pixels?
     * @param int $scale
     *        how big are we scaling the image?
     *        - 1 for normal resolution
     *        - 2 for 'Retina' resolution
     *        - 3 for 'iPhone X' resolution
     * @param float $quality
     *        image quality (0-100%)
     * @return void
     */
    public static function transform(Filesystem $fs, $path, int $width, int $height, int $scale, int $quality)
    {
        // what are we transforming?
        $pathInfo = TypeConverters\ToPathInfo::from($path);

        $image = new Imagick($pathInfo->getFullPath());
        $image->setOption('filter:support', '2.0');
        $image->unsharpMaskImage(0.25, 0.25, 1, 0.065);
        $image->thumbnailImage($width * $scale, $height * $scale, true, false, Imagick::FILTER_TRIANGLE);
        $image->setImageCompressionQuality($quality);
        $image->setOption('jpeg:fancy-upsamping', 'off');
        $image->setInterlaceScheme(Imagick::INTERLACE_NO);
        $image->setColorspace(Imagick::COLORSPACE_SRGB);
        $image->stripImage();

        file_put_contents($pathInfo->getFullPath(), $image->getImageBlob());
    }

    /**
     * resize an image
     *
     * @param Filesystem $fs
     *        the filesystem we are working with
     * @param string|PathInfo $path
     *        the image we want to resize
     * @return void
     */
    public function __invoke(Filesystem $fs, $path)
    {
        return static::transform($fs, $path, $this->width, $this->height, $this->scale, $this->quality);
    }

    /**
     * resize an image
     *
     * @param Filesystem $fs
     *        the filesystem we are working with
     * @param string|PathInfo $path
     *        the image we want to resize
     * @return void
     */
    public function to(Filesystem $fs, $path)
    {
        return static::transform($fs, $path, $this->width, $this->height, $this->scale, $this->quality);
    }
}