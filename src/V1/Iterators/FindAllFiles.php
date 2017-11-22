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
 * @package   Filesystem\V1\Iterators
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem
 */

namespace GanbaroDigital\Filesystem\V1\Iterators;

use GanbaroDigital\AdaptersAndPlugins\V1\Operations\CallPlugin;
use GanbaroDigital\Filesystem\V1\Checks;
use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

use RecursiveIterator;
use RecursiveIteratorIterator;

/**
 * find the files in a folder and its subfolders
 */
class FindAllFiles
{
    /**
     * find all files in a folder and its subfolders
     *
     * we match on:
     *
     * - real files, including 'hidden' files
     * - symlinks that point to real files
     *
     * @param  Filesystem $fs
     *         the filesystem we are searching
     * @param  string $path
     *         the path to search
     * @param  OnFatal $onFatal
     *         what do we do if we cannot find any files?
     * @param  int $searchOrder
     *         do we want child folders first (default), or ...?
     * @return FileInfo
     *         we yield a FileInfo object for every file found
     */
    public static function in(Filesystem $fs, string $path, OnFatal $onFatal, int $searchOrder = RecursiveIteratorIterator::CHILD_FIRST)
    {
        // how will we examine the filesystem?
        $iterator = static::getIterator($fs, $path, $onFatal);

        // we can re-use PHP's built-in recursive support to find
        // what we are looking for
        $matches = new RecursiveIteratorIterator(
            $iterator,
            $searchOrder
        );

        foreach ($matches as $fileInfo) {
            // skip over anything that isn't a file, or a symlink that
            // points to a file
            if (!Checks\IsFile::check($fs, $fileInfo)) {
                continue;
            }

            // return to caller
            yield $fileInfo;
        }
    }

    /**
     * obtain an iterator to use to examine the filesystem contents
     *
     * this is here entirely to support subclassing
     *
     * @param  Filesystem $fs
     *         the filesystem we are searching
     * @param  string $path
     *         the path to search
     * @param  OnFatal $onFatal
     *         what do we throw if we cannot create the iterator?
     * @return RecursiveIterator
     *         how we will access the filesystem contents
     */
    protected static function getIterator(Filesystem $fs, string $path, OnFatal $onFatal) : RecursiveIterator
    {
        // ask the filesystem to cough up a suitable iterator
        return CallPlugin::using($fs, 'Iterators\\GetContentsIterator', 'for', $fs, $path, $onFatal);
    }
}