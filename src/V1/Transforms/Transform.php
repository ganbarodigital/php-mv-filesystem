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

use GanbaroDigital\Filesystem\V1\Filesystem;
use GanbaroDigital\Filesystem\V1\PathInfo;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

/**
 * interface implemented by all available transforms
 *
 * they should also implement:
 *
 * `public static function apply(...) : FileTransform`
 *
 *   this static method should create a new instance of the transform,
 *   ready to be used
 *
 *   ::apply() should take the same parameters as the transform's
 *   constructor. It's there to take any extra parameters.
 *
 * `public static function transform(...)`
 *
 *   this static method does the actual transform
 *
 *   ::transform()'s parameters are:
 *   - $fs
 *   - $path
 *   - then the same arguments that ::apply() takes (in same order)
 *
 */
interface Transform
{
    /**
     * execute a transform
     *
     * @param  Filesystem $fs
     *         the filesystem we are working with
     * @param  string|PathInfo $path
     *         the file or folder we are transforming
     * @return void
     */
    public function __invoke(Filesystem $fs, $path);

    /**
     * execute a transform
     *
     * @param  Filesystem $fs
     *         the filesystem we are working with
     * @param  string|PathInfo $path
     *         the file or folder we are transforming
     * @return void
     */
    public function to(Filesystem $fs, $path);
}