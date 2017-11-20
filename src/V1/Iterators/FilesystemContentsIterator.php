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
 * @link      http://ganbarodigital.github.io/php-mv-s3-filesystem-sdk3
 */

namespace GanbaroDigital\S3Filesystem\V1\Iterators;

use OutOfBoundsException;
use SeekableIterator;
use GanbaroDigital\Filesystem\V1\FilesystemContents;
use GanbaroDigital\Filesystem\V1\FileInfo;

class FilesystemContentsIterator implements SeekableIterator
{
    const CURRENT_AS_FILEINFO = 0;
    const CURRENT_AS_PATHNAME = 1;
    const KEY_AS_FULLPATH = 0;
    const KEY_AS_FILENAME = 256;
    const FOLLOW_SYMLINKS = 512;
    const SKIP_DOTS = 4096;

    private $position = 0;
    private $contents;

    private $keys;

    public function __construct(FilesystemContents $contents, int $flags = self::KEY_AS_FULLPATH | self::CURRENT_AS_FILEINFO | self::SKIP_DOTS)
    {
        $this->contents = $contents;
        $this->keys = $contents->getKeys();
        $this->flags = $flags;
    }

    // ==================================================================
    //
    // SeekableIterator interface
    //
    // ------------------------------------------------------------------

    public function seek(int $position)
    {
        if (!isset($this->keys[$position])) {
            throw new OutOfBoundsException("invalid seek position ($position)");
        }

        $this->position = $position;
    }

    // ==================================================================
    //
    // Iterator API
    //
    // ------------------------------------------------------------------

    public function current()
    {
        if ($this->flags && self::CURRENT_AS_FULLPATH) {
            return $this->keys[$this->position];
        }

        return $this->listing->getFileInfo($this->keys[$this->position]);
    }

    public function key() : string
    {
        // shorthand
        $retval = $this->keys[$this->position];
        if ($this->flags && self::KEY_AS_PATHNAME) {
            return $retval;
        }

        return basename($retval);
    }

    public function next()
    {
        $this->position++;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid() : bool
    {
        return isset($this->keys[$this->position]);
    }
}