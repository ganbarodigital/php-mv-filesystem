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
 * @package   Filesystem\V1\Filters
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem
 */

namespace GanbaroDigital\Filesystem\V1\Filters;

use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\Iterators;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

use RecursiveIteratorIterator;

/**
 * find folders on the filesystem that your function says are a match
 */
class FindMatchingFolders
{
    /**
     * find folders on the filesystem that your function says are a match
     *
     * @param Filesystem $fs
     *        the filesystem you want to search
     * @param string|PathInfo $path
     *        the path on $fs that you want to search from
     *        (will find folders in this folder, and in all child folders)
     * @param callable $matchFunc
     *        your function that decides whether a patch is a match or not
     *        - function(FileInfo $fileInfo) : bool
     * @param OnFatal $onFatal
     *        what do we do if we cannot iterate at all?
     * @param int $searchOrder
     *        RecursiveIteratorIterator flags to affect iterator behaviour
     * @return FileInfo
     *         yields every FileInfo that:
     *         - is a folder (according to Checks\IsFolder), and
     *         - makes $matchFunc() return `true`
     */
    public static function in(
        Filesystem $fs,
        $path,
        callable $matchFunc,
        OnFatal $onFatal,
        int $searchOrder = RecursiveIteratorIterator::CHILD_FIRST
    )
    {
        return FindMatching::in(
            Iterators\FindAllFolders::in($fs, $path, $onFatal, $searchOrder),
            $matchFunc
        );
    }
}