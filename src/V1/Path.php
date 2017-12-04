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

use GanbaroDigital\Filesystem\V1\BuildPathInfo;

/**
 * represents a path on a filesystem
 *
 * this knows which filesystem it is on
 */
class Path implements PathInfo
{
    /**
     * which filesystem (by prefix) are we on?
     *
     * @var string
     */
    protected $fsPrefix = 'unknown';

    /**
     * what is the path on this filesystem?
     * @var string
     */
    protected $fsPath;

    /**
     * our constructor
     *
     * @param string $path
     *        the 'fsPrefix::fsPath' that we will represent
     */
    public function __construct(string $path)
    {
        $parts = TypeConverters\ToPathComponents::from($path);
        $this->fsPrefix = $parts[0];
        $this->fsPath = $parts[1];
    }

    /**
     * which filesystem does this path belong to?
     *
     * @return string
     */
    public function getFilesystemPrefix() : string
    {
        return $this->fsPrefix;
    }

    /**
     * what is our full path, including our filesystem prefix?
     *
     * @return string
     */
    public function getPrefixedPath() : string
    {
        return $this->fsPrefix . '::' . $this->fsPath;
    }

    /**
     * automatic type-conversion to string
     *
     * this returns the full prefixed path
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->getPrefixedPath();
    }

    /**
     * what is the filename itself?
     *
     * this includes any parent folders, and the filename extension
     *
     * @return string
     */
    public function getFullPath() : string
    {
        return $this->fsPath;
    }

    /**
     * what is the filename, without any parent folders?
     *
     * @return string
     */
    public function getBasename() : string
    {
        // all done
        return basename($this->fsPath);
    }

    /**
     * what is the parent folder for this filename?
     *
     * returns '.' if there is no parent folder
     *
     * @return string
     */
    public function getDirname() : string
    {
        return dirname($this->fsPath);
    }

    /**
     * what is the file extension of this path info?
     *
     * we return an empty string if the filename has no extension
     *
     * @return string
     */
    public function getExtension() : string
    {
        return pathinfo($this->fsPath, PATHINFO_EXTENSION);
    }

    /**
     * build a new Path with a child file|folder appended
     *
     * @param  string $child
     *         the value to add onto the end of the current Path
     * @return PathInfo
     */
    public function withChild(string $child) : PathInfo
    {
        $builder = function($newPath) {
            return new Path($newPath);
        };
        return BuildPathInfo\AddChild::to($this, $child, $builder);
    }

    /**
     * build a new Path without any file extension
     *
     * @return PathInfo
     */
    public function stripExtension() : PathInfo
    {
        $builder = function($newPath) {
            return new Path($newPath);
        };
        return BuildPathInfo\StripExtension::from($this, $builder);
    }

    /**
     * build a new Path with a different file extension
     *
     * @param  string $newExtension
     *         the file extension (.XXX) that you want
     * @return PathInfo
     */
    public function withExtension(string $newExtension) : PathInfo
    {
        $builder = function($newPath) {
            return new Path($newPath);
        };
        return BuildPathInfo\WithExtension::using($this, $newExtension, $builder);
    }

    /**
     * build a new Path for a path on a given filesystem
     *
     * @param  Filesystem $fs
     *         the filesystem we want the path to be on
     * @param  string|PathInfo $path
     *         the path that's on the filesystem
     * @return PathInfo
     */
    public static function onFilesystem($fsOrPrefix, $path) : PathInfo
    {
        $builder = function($newPath) {
            return new Path($newPath);
        };
        return BuildPathInfo\WithFilesystem::using($fsOrPrefix, $path, $builder);
    }
}