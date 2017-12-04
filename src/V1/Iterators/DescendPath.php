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

use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\Path;
use GanbaroDigital\Filesystem\V1\PathInfo;
use GanbaroDigital\Filesystem\V1\TypeConverters;

/**
 * iterate over a path's constituent parts
 */
class DescendPath
{
    /**
     * iterate over a path's constituent parts
     *
     * @param  Filesystem $fs
     *         the filesystem that $path is on
     * @param  string|PathInfo $path
     *         the path we want to iterate over
     * @return PathInfo
     *         each parent folder in turn, starting with the root folder,
     *         last item yielded is whatever $path points at
     */
    public static function using(Filesystem $fs, $path)
    {
        // what are we looking at?
        $pathInfo = TypeConverters\ToPathInfo::from($path);

        // what are the individual parts?
        $parts = explode("/", $pathInfo->getFullPath());

        $pathSoFar = new Path($fs->getFilesystemPrefix() . PathInfo::FS_SEPARATOR);
        foreach ($parts as $part) {
            // skip anything that's empty
            if (empty($part)) {
                continue;
            }

            // we return the full path so far, and it is up to the caller
            // whether they only want the new part of the path or not
            $pathSoFar = $pathSoFar->withChild("/{$part}");
            yield $pathSoFar;
        }
    }
}