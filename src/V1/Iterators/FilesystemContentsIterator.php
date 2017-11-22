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

use OutOfBoundsException;
use SeekableIterator;
use GanbaroDigital\Filesystem\V1\FilesystemContents;
use GanbaroDigital\Filesystem\V1\FileInfo;
use GanbaroDigital\MissingBits\ErrorResponders\OnFatal;

/**
 * iterate across a tree of filesystem contents
 */
class FilesystemContentsIterator implements SeekableIterator
{
    const CURRENT_AS_FILEINFO = 1;
    const CURRENT_AS_FULLPATH = 2;

    // 2^8
    const KEY_AS_FULLPATH = 256;
    // 2^9
    const KEY_AS_FILENAME = 512;

    // 2^16
    const FOLLOW_SYMLINKS = 65536;

    // 2^14
    const SKIP_DOTS = 16777216;

    /**
     * the data that we are iterating over
     *
     * @var FilesystemContents
     */
    private $contents;

    /**
     * the bitmask that decides on some of our behaviour
     *
     * @var int
     */
    private $flags;

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
     * what to do if we cannot get FileInfo about the current() item
     *
     * for performance, we define it once and re-use it many times
     *
     * @var OnFatal
     */
    private $currentOnFatal;

    /**
     * the return value that current() points at
     *
     * for performance, we generate this whenever we move the iterator,
     * to avoid generating it when there are multiple calls to current()
     * before the iterator moves again
     *
     * @var string|FileInfo
     */
    private $currentRetval;

    private $currentReturnsFullPath = false;
    private $currentReturnsFileInfo = false;

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
        $this->filenames = $contents->getFilenames($flags);
        $this->flags = $flags;

        // creating it once, here, delivers a significant
        // performance increase
        $this->currentOnFatal = new OnFatal(function() { return null; });

        // calculate the flags once, to save some CPU
        $this->currentReturnsFileInfo = ($flags & self::CURRENT_AS_FILEINFO) ? true : false;
        $this->currentReturnsFullPath = ($flags & self::CURRENT_AS_FULLPATH) ? true : false;

        // let's get sorted
        $this->rewind();
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
        if ($this->currentReturnsFullPath) {
            return $this->contents->getFullPath() . '/' .$this->filenames[$this->position];
        }

        // we've already built this, in either rewind() or next()
        return $this->currentFileInfo;
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
        if ($this->flags & self::KEY_AS_FULLPATH) {
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

        // prebuild the fileinfo we want $this->current() to return
        $this->buildCurrentFileInfo();
    }

    /**
     * move back to the first item in the contents list
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;

        // prebuild the fileinfo we want $this->current() to return
        $this->buildCurrentFileInfo();
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

    /**
     * update $this->currentFileInfo property
     *
     * @return void
     */
    protected function buildCurrentFileInfo()
    {
        // weirdly, it's faster to set this all the time, than to set it
        // in an `else` statement below ... go figure!
        $this->currentFileInfo = null;

        // do we really want to do this?
        if (!$this->valid() || (!$this->currentReturnsFileInfo)) {
            // no, we don't
            return;
        }

        // if we get here, then we need to do this
        $this->currentFileInfo = $this->contents->getFileInfo(
            $this->filenames[$this->position],
            $this->currentOnFatal
        );
    }
}