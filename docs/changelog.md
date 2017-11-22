# CHANGELOG

## develop branch

### New

* Added abstractions for use in plugins
  - Added `Filesystem` interface
  - Added `FilesystemContents` interface
  - Added `PathInfo` interface
  - Added `FileInfo` interface
  - Added `Path` value class
* Added support for iterating over filesystems
  - Added `FilesystemContentsIterator` iterator
  - Added `RecursiveFilesystemContentsIterator` iterator
  - Added `FindAllFiles` high-level iterator
  - Added `FindAllFolders` high-level iterator
* Added support for understanding filesystem contents
  - Added `IsFile` check
  - Added `IsFolder` check
* Added helpful type converters
  - Added `ToFileInfo`
  - Added `ToPathComponents`
  - Added `ToPathInfo`
  - Added `ToPrefixedPath`
