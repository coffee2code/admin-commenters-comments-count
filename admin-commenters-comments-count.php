<?php
/*
Plugin Name: Admin Commenters Comments Count
Version: 1.0
Plugin URI: http://coffee2code.com/wp-plugins/admin-commenters-comments-count
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.

Next to all appearances of each commenter's name in the admin, shows a count of their total number of comments,
linked to a listing of those comments.

By default, it is not possible to tell via a single glance whether a particular commenter has commented before,
and if so, how many times.

This plugin adds a handy feature to the WordPress admin pages to allow you to:

* Quickly identify a first-time commenter
* Quickly identify unfamiliar commenters that have in fact commented before
* Quickly see how many total comments a particular commenter has made, and how many comments are pending
* Easily go to a listing of all comments by a commenter, in order to see what and when they last commented, or what/when they first commented

This plugin adds a linked comment count next to every appearance of a commenter in the admin.  The link takes you to the admin
page listing all comments for that particular commenter.  The count displays all approved comments attributed to that commenter.  If you hover
over the comment count, the hover text indicates how many pending comments they also have, if any.

Specifically, the linked comment count appears next to commenters in:

* The "Edit Comments" listing of comments (including comment search results)
* The "Edit Comments for 'Post Title'" listing of post-specific comments
* The "Discussion" box of the "Edit Post" page for a post with comments
* The "Recent Comments" admin dashboard widget

Commenters are identified by the email address they provided when commenting.  If your site does not require that commenters
submit their email address when commenting, then this plugin will be of little value to you.

Compatible with WordPress 2.6+, 2.7+, 2.8+.

=>> Read the accompanying readme.txt file for more information.  Also, visit the plugin's homepage
=>> for more information and the latest updates

Installation:

1. Download the file http://www.coffee2code.com/wp-plugins/admin-commenters-comments-count.zip and unzip it into your 
/wp-content/plugins/ directory.
2. Activate the plugin through the 'Plugins' admin menu in WordPress

*/

/*
Copyright (c) 2009 by Scott Reilly (aka coffee2code)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, 
modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the 
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

if ( !class_exists('AdminCommentersCommentsCount') ) :

class AdminCommentersCommentsCount {

	function AdminCommentersCommentsCount() {
		if ( is_admin() ) {
			add_action('admin_head', array(&$this, 'add_css'));
			add_filter('comment_author', array(&$this, 'comment_author'));
			add_filter('get_comment_author_link', array(&$this, 'get_comment_author_link'));
		}
	}

	function add_css() {
		echo <<<CSS
		<style type="text/css">
		.author-com-count {
			float:right;background-position:22% -55px; text-align:center; margin-right:5px;
		}
		.author-com-count:hover {
			background-position:22% -3px;
		}
		</style>

CSS;
	}

	function comment_author($author_name) {
		if ( !is_admin() )
			return $author_name;
		global $comment, $wpdb;
		$type = get_comment_type();
		if ( 'comment' == $type ) {
			$author_email = $comment->comment_author_email;
			if ( empty($author_email) ) {
				$comment_count = 0;
			} else {
				$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_author_email = %s AND comment_approved = %d";
				$comment_count = $wpdb->get_var( $wpdb->prepare($query, $author_email, 1) );
				$pending_count = $wpdb->get_var( $wpdb->prepare($query, $author_email, 0) );
			}
		} else {
			$author_url = $comment->comment_author_url;
			// Want to get the root domain and not use the exact pingback/trackback source link
			$parsed_url = parse_url($author_url);
			$author_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
			$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_author_url LIKE %s AND comment_type = %s AND comment_approved = %d";
			$comment_count = $wpdb->get_var( $wpdb->prepare($query, $author_url.'%', $type, 1) );
			$pending_count = $wpdb->get_var( $wpdb->prepare($query, $author_url.'%', $type, 0) );
			$author_email = $author_url;
		}
		$msg = "$comment_count $type";
		if ( $comment_count != 1 ) $msg .= 's';
		if ( $pending_count ) $msg .= "; $pending_count pending";
		$url = $comment_count+$pending_count > 0 ? 'edit-comments.php?s=' . attribute_escape($author_email) : '#';
		return "
			<div class='post-com-count-wrapper' style='position:relative; display:inline;'>
			<a class='author-com-count post-com-count' href='$url' title='$msg' style=''><span class='comment-count'>$comment_count</span></a>
			</div>$author_name";
	}

	function get_comment_author_link($author_link) {
		if ( !is_admin() ) return $author_link;
		return $this->comment_author(get_comment_author());
	}
} // end AdminCommentersCommentsCount

endif; // end if !class_exists()

if ( class_exists('AdminCommentersCommentsCount') )
	new AdminCommentersCommentsCount();

?>