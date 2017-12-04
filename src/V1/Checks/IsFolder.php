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
 * @package   Filesystem\Checks
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem
 */

namespace GanbaroDigital\Filesystem\V1\Checks;

use GanbaroDigital\AdaptersAndPlugins\V1\Operations\CallPlugin;
use GanbaroDigital\Filesystem\V1\Exceptions\CannotBuildFileInfo;
use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\TypeConverters;
use GanbaroDigital\MissingBits\Checks\Check;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

/**
 * do we have a folder on the filesystem?
 */
class IsFolder implements Check
{
    /**
     * the filesystem we are checking against
     * @var Filesystem
     */
    protected $fs;

    /**
     * create a new Check
     *
     * @param Filesystem $fs
     *        the filesystem we are checking against
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * do we have a folder on the filesystem?
     *
     * @param  Filesystem $fs
     *         the filesystem we are checking against
     * @param  string|FileInfo $path
     *         the path that we want to check
     * @return boolean
     *         - true if we have a real folder
     *         - true if we have a symlink to a real folder
     *         - false otherwise
     */
    public static function check(Filesystem $fs, $path)
    {
        // when something goes wrong, we need to tell others about it
        $onFatal = new OnFatal(function($path, $reason) {
            throw CannotBuildFileInfo::newFromInputParameter($path, '$path', ['reason' => $reason]);
        });

        /** @var FileInfo */
        $file = TypeConverters\ToFileInfo::from($fs, $path, $onFatal);

        // is this a real folder?
        if ($file->isFolder()) {
            return true;
        }

        // is it a symlink that points to a folder?
        if ($file->isLink()) {
            return static::check($fs, $file->getRealPath());
        }

        // if we get here, then all hope is lost
        return false;
    }

    /**
     * do we have a folder on the filesystem?
     *
     * @param  string|FileInfo $path
     *         the path that we want to check
     * @return boolean
     *         - true if we have a real folder
     *         - true if we have a symlink to a real folder
     *         - false otherwise
     */
    public function inspect($path)
    {
        return static::check($this->fs, $path);
    }

    /**
     * do we have a folder on the filesystem?
     *
     * @param  string|FileInfo $path
     *         the path that we want to check
     * @return boolean
     *         - true if we have a real folder
     *         - true if we have a symlink to a real folder
     *         - false otherwise
     */
    public function __invoke($path)
    {
        return static::check($this->fs, $path);
    }

    /**
     * create a new Check
     *
     * @param  Filesystem $fs
     *         the filesystem we are checking against
     * @return Check
     */
    public static function using(Filesystem $fs)
    {
        return new static($fs);
    }
}