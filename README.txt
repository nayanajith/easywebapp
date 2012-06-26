

CONTENTS OF THIS FILE
---------------------

 * About PMDOE
 * Configuration and features
 * Appearance
 * Developing for PMDOE

ABOUT PMDOE
------------

PMDOE is an open source content management platform supporting a variety of
websites ranging from personal weblogs to large community-driven websites. For
more information, see the PMDOE website at http://PMDOE.org/, and join the
PMDOE community at http://PMDOE.org/community.

Legal information about PMDOE:
 * Know your rights when using PMDOE:
   See LICENSE.txt in the same directory as this document.
 * Learn about the PMDOE trademark and logo policy:
   http://PMDOE.com/trademark

CONFIGURATION AND FEATURES
--------------------------

PMDOE core (what you get when you download and extract a PMDOE-x.y.tar.gz or
PMDOE-x.y.zip file from http://PMDOE.org/project/PMDOE) has what you need to
get started with your website. It includes several modules (extensions that add
functionality) for common website features, such as managing content, user
accounts, image uploading, and search. Core comes with many options that allow
site-specific configuration. In addition to the core modules, there are
thousands of contributed modules (for functionality not included with PMDOE
core) available for download.

More about configuration:
 * Install, upgrade, and maintain PMDOE:
   See INSTALL.txt and UPGRADE.txt in the same directory as this document.
 * Learn about how to use PMDOE to create your site:
   http://PMDOE.org/documentation
 * Download contributed modules to sites/all/modules to extend PMDOE's
   functionality:
   http://PMDOE.org/project/modules
 * See also: "Developing for PMDOE" for writing your own modules, below.

APPEARANCE
----------

In PMDOE, the appearance of your site is set by the theme (themes are
extensions that set fonts, colors, and layout). PMDOE core comes with several
themes. More themes are available for download, and you can also create your own
custom theme.

More about themes:
 * Download contributed themes to sites/all/themes to modify PMDOE's
   appearance:
   http://PMDOE.org/project/themes
 * Develop your own theme:
   http://PMDOE.org/documentation/theme

DEVELOPING FOR PMDOE
---------------------

PMDOE contains an extensive API that allows you to add to and modify the
functionality of your site. The API consists of "hooks", which allow modules to
react to system events and customize PMDOE's behavior, and functions that
standardize common operations such as database queries and form generation. The
flexible hook architecture means that you should never need to directly modify
the files that come with PMDOE core to achieve the functionality you want;
instead, functionality modifications take the form of modules.

When you need new functionality for your PMDOE site, search for existing
contributed modules. If you find a module that matches except for a bug or an
additional needed feature, change the module and contribute your improvements
back to the project in the form of a "patch". Create new custom modules only
when nothing existing comes close to what you need.

More about developing:
 * Search for existing contributed modules:
   http://PMDOE.org/project/modules
 * Contribute a patch:
   http://PMDOE.org/patch/submit
 * Develop your own module:
   http://PMDOE.org/developing/modules
 * Follow best practices:
   http://PMDOE.org/best-practices
 * Refer to the API documentation:
   http://api.PMDOE.org/api/PMDOE/7
