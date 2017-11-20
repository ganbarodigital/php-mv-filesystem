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

namespace GanbaroDigital\Filesystem\V1\Iterators;

use OutOfBoundsException;
use SeekableIterator;
use GanbaroDigital\Filesystem\V1\FilesystemContents;
use GanbaroDigital\Filesystem\V1\FileInfo;

/**
 * iterate across a tree of filesystem contents
 */
class FilesystemContentsIterator implements SeekableIterator
{
    const CURRENT_AS_FILEINFO = 0;
    const CURRENT_AS_FULLPATH = 1;
    const KEY_AS_FULLPATH = 0;
    const KEY_AS_FILENAME = 256;
    const FOLLOW_SYMLINKS = 512;
    const SKIP_DOTS = 4096;

    /**
     * the data that we are iterating over
     *
     * @var FilesystemContents
     */
    private $contents;

    /**
     * keep track of where we are when the iterator is seeking
     *
     * we use this as an index into $this->filenames
     * @var integer
     */
    private $position = 0;

    /**
     * a list of the filenames in $contents
     *
     * we cache them here to make the iterator code easier
     *
     * @var string[]
     */
    private $filenames;

    /**
     * our constructor
     *
     * @param FilesystemContents $contents
     *        the data that we will iterate over
     * @param int $flags
     *        change these to change the behaviour of this iterator
     */
    public function __construct(FilesystemContents $contents, int $flags = self::KEY_AS_FULLPATH | self::CURRENT_AS_FILEINFO | self::SKIP_DOTS)
    {
        $this->contents = $contents;
        $this->filenames = $contents->getFilenames();
        $this->flags = $flags;
    }

    /**
     * what flags were set for this iterator?
     *
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
    }

    // ==================================================================
    //
    // SeekableIterator interface
    //
    // ------------------------------------------------------------------

    public function seek($position)
    {
        if (!isset($this->filenames[$position])) {
            throw new OutOfBoundsException("invalid seek position ($position)");
        }

        $this->position = $position;
    }

    // ==================================================================
    //
    // Iterator API
    //
    // ------------------------------------------------------------------

    /**
     * get the file or folder that the iterator is currently pointing at
     *
     * the return type is decided by the flags passed into the constructor
     *
     * @return string|FileInfo
     */
    public function current()
    {
        if ($this->flags & self::CURRENT_AS_FULLPATH) {
            return $this->filenames[$this->position];
        }

        return $this->contents->getFileInfo($this->filenames[$this->position]);
    }

    /**
     * get the filename or folder name that the iterator is currently
     * pointing at
     *
     * the return value (full path, or just the filename with the parent
     * folders stripped off) is decided by the flags passed into the
     * constructor
     *
     * @return string
     */
    public function key() : string
    {
        // shorthand
        $retval = $this->filenames[$this->position];
        if ($this->flags & self::KEY_AS_PATHNAME) {
            return $retval;
        }

        return basename($retval);
    }

    /**
     * move to the next position in the contents list
     *
     * @return void
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * move back to the first item in the contents list
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * is the iterator currently pointing at something in the contents list?
     *
     * @return bool
     *         - `true` if we are
     *         - `false` if we have iterated past the end of the list
     */
    public function valid() : bool
    {
        return isset($this->filenames[$this->position]);
    }
}