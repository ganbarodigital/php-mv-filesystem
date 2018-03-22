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
  - Added `FindMatchingFiles` high-level iterator
  - Added `FindMatchingFolders` high-level iterator
  - Added `FindMatching` high-lvel iterator
* Added support for understanding a path
  - Added `DescendParentFolders` iterator
  - Added `DescendPath` iterator
* Added support for understanding filesystem contents
  - Added `IsFile` check
  - Added `IsFolder` check
* Added helpful type converters
  - Added `ToFileInfo`
  - Added `ToPathComponents`
  - Added `ToPathInfo`
  - Added `ToPrefixedPath`
* Added helpful tools for building new paths
  - Added `AddChild`
  - Added `StripExtension`
  - Added `WithExtension`
  - Added `WithFilesystem`
* Added support for factories to create filesystems
  - Added `FilesystemFactory` interface
* Added exceptions for when things go wrong
  - Added `FilesystemException` interface
  - Added `CannotBuildFileInfo` exception
* Added things we can do to files and folders
  - Added `Copy` operation
  - Added `CreateTemporaryCopy` operation
  - Added `GetFileContents` operation
  - Added `GetImageDimensions` operation
  - Added `Move` operation
  - Added `PutFileContents` operation
  - Added `Transform` interface
  - Added `ResizeImage` transform
* Added support for key/value pair metadata
  - Added `GetFileMetadata` operation
  - Added `PutFileMetadata` operation
