=== Admin Commenters Comments Count ===
Contributors: coffee2code
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6ARCFJ9TX3522
Tags: commenters, comment count, comment author, comments, comment, admin, coffee2code
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 3.8
Tested up to: 3.8
Stable tag: 1.3

Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.


== Description ==

Next to all appearances of each commenter's name in the admin, this plugin shows a count of their total number of comments, linked to a listing of those comments.

By default in WordPress, it is not possible to tell via a single glance whether a particular commenter has commented before, and if so, how many times.

This plugin adds a handy feature to the WordPress admin pages to allow you to:

* Quickly identify a first-time commenter
* Quickly identify unfamiliar commenters that have in fact commented before
* Quickly see how many total comments a particular commenter has made, and how many comments are pending
* Easily go to a listing of all comments by a commenter, in order to see what and when they last commented, or what/when they first commented

This plugin adds a linked comment count next to every appearance of a commenter in the admin. The link takes you to the admin page listing all comments for that particular commenter. The count displays all approved comments attributed to that commenter. If you hover over the comment count, the hover text indicates how many pending comments they also have, if any.

Specifically, the linked comment count appears next to commenters in:

* The "Comments" listing of comments (including comment search results)
* The "Comments for 'POST_TITLE'" listing of post-specific comments
* The "Discussion" box of the "Edit Post" page for a post with comments
* The "Recent Comments" admin dashboard widget

Commenters are identified by the email address they provided when commenting. If your site does not require that commenters submit their email address when commenting, this plugin will use the commenter's name as the identifier, though since this is a publicly viewable piece of data it's possible that multiple people could be posting under the same "name", so this method has the potential to be not as accurate.

Links: [Plugin Homepage](http://coffee2code.com/wp-plugins/admin-commenters-comments-count/) | [Plugin Directory Page](http://wordpress.org/plugins/admin-commenters-comments-count/) | [Author Homepage](http://coffee2code.com)


== Installation ==

1. Unzip `admin-commenters-comments-count.zip` inside the `/wp-content/plugins/` directory for your site (or install via the built-in WordPress plugin installer)
1. Activate the plugin through the 'Plugins' admin menu in WordPress


== Frequently Asked Questions ==

= Why would I want to see a count of how many comments someone made? =

There are many reasons, some of which might include:

* Quickly identifying a first-time commenter
* Quickly identifying unfamiliar commenters that have in fact commented before
* Quickly seeing how many total comments a particular commenter has made, and how many comments are pending
* Easily going to a listing of all comments by a commenter, in order to see what and when they last commented, or what/when they first commented

= How does the plugin know about all of the comments someone made to the site? =

Commenters are identified by the email address they provided when making a comment. If commenters are allowed to omit providing an email address, then their name is used to identify them (though this is potentially less accurate).

= Why does it report someone as having less comments than I know they've actually made? =

Since commenters are identified by the email address they provided when making a comment, if they supply an alternate email address for a comment, the plugin treats that email account as a separate person.

= Does this plugin include unit tests? =

Yes.


== Screenshots ==

1. A screenshot of the 'Comments' admin page with the comment count appearing next to the commenter's name. The most recent comment is from someone who has not commented on the site before. The second comment is from someone who has commented 13 times before. The hover text on the comment bubble reveals there are currently 13 approved comments and 3 pending comments for the visitor.
2. A screenshot of the 'Comments on POST TITLE' admin page with the comment count appearing next to the commenter's name.
3. A screenshot of the 'Activity' admin dashboard widget with the comment count appearing next to the commenter's name.
4. A screenshot of the 'Comments' metabox on the 'Edit Post' admin page with the comment count appearing next to the commenter's name.


== Changelog ==

= 1.3 (2013-12-23) =
* Enqueue custom CSS file instead of adding CSS to page head
* Change CSS to allow comment bubbles to take on colors of active admin theme
* Change initialization to fire on 'plugins_loaded'
* Add unit tests
* Minor documentation tweaks
* Note compatibility through WP 3.8+
* Drop compatibility with version of WP older than 3.8
* Update copyright date (2014)
* Change donate link
* Update screenshots for WP 3.8 admin refresh
* Update banner for WP 3.8 admin refresh

= 1.2.1 =
* Add check to prevent execution of code if file is directly accessed
* Note compatibility through WP 3.5+
* Update copyright date (2013)
* Move screenshots into repo's assets directory

= 1.2 =
* Add CSS rule to set text color to white to supersede CSS styling done by latest Akismet
* Default to gray comment bubble
* Show blue comment bubble for authors with pending comment (consistent with how WP does it for posts)
* Add 'author-com-pending' class to link when author has pending comments
* Show orange comment bubble on hover over comment bubble
* Re-license as GPLv2 or later (from X11)
* Add 'License' and 'License URI' header tags to readme.txt and plugin file
* Add banner image for plugin page
* Remove ending PHP close tag
* Note compatibility through WP 3.4+

= 1.1.4 =
* Bugfix for notices when non-standard comment types are present (by explicitly supporting pingbacks and trackbacks, and ignoring non-standard comment types)
* CSS tweak to prevent top of comment bubble from being clipped
* Prefix class name with 'c2c_'
* Add version() to return plugin version
* Note compatibility through WP 3.3+
* Fix typo in readme.txt
* Update screenshot-3
* Add link to plugin directory page to readme.txt
* Update copyright date (2012)

= 1.1.3 =
* Properly encode emails in links to commenter's comments listing (fixes bug where a '+' in email prevented being able to see their listing)
* Invoke class function internally via self instead of using actual classname
* Note compatibility through WP 3.2+
* Minor code formatting changes (spacing)
* Fix plugin homepage and author links in description in readme.txt

= 1.1.2 =
* Explicitly declare all class functions public static
* Minor code reformatting (spacing) and doc tweaks
* Note compatibility with WP 3.1+
* Update copyright date (2011)

= 1.1.1. =
* Bug fix (missing argument for sprintf() replacement)

= 1.1 =
* If a commenter does not have an email provided, search for other comments based on the provided name
* Treat class as a namespace rather than instantiating it as an object
* Check for is_admin() before defining class rather than during constructor
* Proper conditional string pluralization and localization support
* Use esc_attr() instead of attribute_escape()
* Fix dashboard display of commenter comment counts (prevent clipping of top of bubble, bubble background is now blue instead of gray)
* No longer define background-position in CSS
* Remove docs from top of plugin file (all that and more are in readme.txt)
* Minor code reformatting (spacing)
* Add package info to top of plugin file
* Remove trailing whitespace in docs
* Add Upgrade Notice section to readme.txt
* Note compatibility with WP 3.0+
* Drop compatibility with version of WP older than 2.8

= 1.0.1 =
* Add PHPDoc documentation
* Note compatibility with WP 2.9+
* Update copyright date and readme.txt

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.3 =
Recommended update: enqueue custom CSS file instead of adding to page head; added unit tests; modified initialization; noted compatibility through WP 3.8+; dropped pre-WP 3.8 compatibility

= 1.2.1 =
Trivial update: noted compatibility through WP 3.5+

= 1.2 =
Recommended update: minor interface changes related to comment bubble coloring; noted compatibility through WP 3.4+; explicitly stated license.

= 1.1.4 =
Minor bugfix update: prevent PHP notices when non-standard comment types are present; noted compatibility through WP 3.3+.

= 1.1.3 =
Minor bugfix update: properly encode emails in links to commenter's comments listing; noted compatibility through WP 3.2+.

= 1.1.2 =
Trivial update: noted compatibility with WP 3.1+ and updated copyright date.

= 1.1.1 =
Minor bug fix.

= 1.1 =
Recommended update. Highlights: search for other comments by commenter name if no email is provided, fixed clipping of comment bubble on admin dashboard, miscellaneous tweaks, verified WP 3.0 compatibility.