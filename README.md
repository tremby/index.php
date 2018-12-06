index.php
=========

This script is intended to be dropped in to a web server
to give a simple directory index.

It targets PHP 5.2.

![screenshot](https://i.imgur.com/6s6zCvH.png)

Excluded files
--------------

Files starting with `.` and files called `index.php` are excluded from listings.

Automatically applying to subdirectories
----------------------------------------

On Apache servers, if overrides are allowed,
it can apply also to child directories
by adding a `.htaccess` file in the same directory as this script
with the following contents:

```
DirectoryIndex /path/to/index.php
```

This path should start from the document root.

Icons
-----

Icons are expected to be found in `/usr/share/icons/gnome/32x32`,
which on Ubuntu comes from the package `gnome-icon-theme`.
It's likely most or all GNOME icon themes follow the same naming scheme,
so there are probably a lot of available drop-in replacements.
