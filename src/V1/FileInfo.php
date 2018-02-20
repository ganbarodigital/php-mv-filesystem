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
 * @package   Filesystem\V1
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2017-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-filesystem
 */

namespace GanbaroDigital\Filesystem\V1;

use DateTime;

/**
 * represents something that a path points to
 *
 * this value knows things about what the path is pointing to
 */
interface FileInfo extends PathInfo
{
    /**
     * what is the real path to this file on the filesystem?
     *
     * @return string
     */
    public function getRealPath() : string;

    /**
     * how big is this file?
     *
     * @return int
     */
    public function getSize() : int;

    /**
     * what is the checksum for this file?
     *
     * ETags are a common technique to tell if a file has changed anywhere
     * or not
     *
     * @return string
     */
    public function getETag() : string;

    /**
     * can we execute this file?
     *
     * @return bool
     */
    public function isExecutable() : bool;

    /**
     * is this a real file on the filesystem?
     *
     * @return bool
     *         - `false` if this is a symlink
     *         - `false` if this is a folder
     *         - `true` otherwise
     */
    public function isFile() : bool;

    /**
     * is this a folder on the filesystem?
     *
     * @return bool
     *         - `false` if this is a file
     *         - `false` if this is a symlink
     *         - `true` otherwise
     */
    public function isFolder() : bool;

    /**
     * is this a symlink on the filesystem?
     *
     * @return bool
     */
    public function isLink() : bool;

    /**
     * can we read this file?
     *
     * @return bool
     */
    public function isReadable() : bool;

    /**
     * can we write into this file?
     *
     * @return bool
     */
    public function isWritable() : bool;

    /**
     * when was this file last modified?
     *
     * @return DateTime
     */
    public function getLastModified() : DateTime;
}