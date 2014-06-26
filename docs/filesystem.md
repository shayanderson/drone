## \Drone\Filesystem
Drone offers filesystem support for directories and files.

#### \Drone\Filesystem\Directory
The `\Drone\Filesystem\Directory` class can be used for directory handling, for example:
```php
$dir = new \Drone\Filesystem\Directory('_app/my_dir');

if($dir->exists()) // do something
```
Directory class methods:
- `copy()`
- `create()`
- `exists()`
- `getCount()` - count of directory items
- `getPath()`
- `move()`
- `read()` - read directory items into array
- `remove()`
- `writable()`

#### \Drone\Filesystem\File
The `\Drone\Filesystem\File` class can be used for file handling, for example:
```php
$file = new \Drone\Filesystem\File('_app/my_file.txt');

if($file->exists()) // do something
```
File class methods:
- `chmod()`
- `copy()`
- `create()`
- `exists()`
- `getModifiedTime()`
- `getPath()`
- `getSize()` - in bytes
- `move()`
- `read()` - read file contents to string
- `remove()`
- `writable()`
- `write()` - write data to file