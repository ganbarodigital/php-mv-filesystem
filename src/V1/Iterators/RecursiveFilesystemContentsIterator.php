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
 * @package   S3Filesystem/V1/Iterators
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem-plugin-s3-sdk3
 */

namespace GanbaroDigital\Filesystem\V1\Iterators;

use SeekableIterator;
use RecursiveIterator;
use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\Filesystem\V1\FilesystemContents;

/**
 * iterate across a tree of filesystem contents
 */
class RecursiveFilesystemContentsIterator extends FilesystemContentsIterator
    implements RecursiveIterator, SeekableIterator
{
    /**
     * our constructor
     *
     * @param FilesystemContents $contents
     *        the data that we will iterate over
     */
    public function __construct(FilesystemContents $contents)
    {
        // we need to make sure that we're using the correct flags with
        // the iterator that we are extending
        parent::__construct(
            $contents,
            self::KEY_AS_FULLPATH | self::CURRENT_AS_FILEINFO | self::SKIP_DOTS
        );
    }

    // ==================================================================
    //
    // RecursiveIterator interface
    //
    // ------------------------------------------------------------------

    /**
     * return an iterator for the contents of the folder we are currently
     * pointing at
     *
     * @return RecursiveIterator
     */
    public function getChildren()
    {
        return new self(
            $this->current(),
            $this->getFlags()
        );
    }

    /**
     * is the iterator currently pointing at a folder (ie, something that
     * we can recurse down into?)
     *
     * @return bool
     *         - `true` if `$this->current()` is a folder
     *         - `false` otherwise
     */
    public function hasChildren()
    {
        $current = $this->current();
        return ($current instanceof FilesystemContents);
    }
}