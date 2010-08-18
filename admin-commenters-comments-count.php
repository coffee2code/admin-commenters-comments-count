<?php
/**
 * @package Admin_Commenters_Comments_Count
 * @author Scott Reilly
 * @version 1.1.1
 */
/*
Plugin Name: Admin Commenters Comments Count
Version: 1.1.1
Plugin URI: http://coffee2code.com/wp-plugins/admin-commenters-comments-count/
Author: Scott Reilly
Author URI: http://coffee2code.com
Description: Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.

Compatible with WordPress 2.8+, 2.9+, 3.0+.

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/admin-commenters-comments-count/

*/

/*
Copyright (c) 2009-2010 by Scott Reilly (aka coffee2code)

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

if ( is_admin() && !class_exists( 'AdminCommentersCommentsCount' ) ) :

class AdminCommentersCommentsCount {

	/**
	 * Class constructor: initializes class variables and adds actions and filters.
	 */
	function init() {
		add_action( 'admin_head', array( __CLASS__, 'add_css' ) );
		add_filter( 'comment_author', array( __CLASS__, 'comment_author' ) );
		add_filter( 'get_comment_author_link', array( __CLASS__, 'get_comment_author_link' ) );
	}

	/**
	 * Outputs CSS within style tags
	 */
	function add_css() {
		echo <<<CSS
		<style type="text/css">
		.author-com-count {float:right;text-align:center;margin-right:5px;margin-top:2px;height:1.3em;line-height:1.1em;}
		.author-com-count:hover {background-position:22% -3px;}
		</style>

CSS;
	}

	/**
	 * Returns the comment author link for the specified author along with the
	 * markup indicating the number of times the comment author has commented
	 * on the site.  The comment count links to a listing of all of that
	 * person's comments.
	 *
	 * Commenters are identified by the email address they provided when
	 * commenting.
	 *
	 * @param string $author_name Name of the comment author.
	 * @return string Comment author link plus linked comment count markup.
	 */
	function comment_author( $author_name ) {
		global $comment, $wpdb;
		$type = get_comment_type();
		if ( 'comment' == $type ) {
			$author_email = $comment->comment_author_email;
			$author_name  = $comment->comment_author;
			if ( empty( $author_email ) ) {
				$field = 'comment_author';
				$value = $author_name;
			} else {
				$field = 'comment_author_email';
				$value = $author_email;
			}
			$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE " . $field . ' = %s AND comment_approved = %d';
			$comment_count = $wpdb->get_var( $wpdb->prepare( $query, $value, 1 ) );
			$pending_count = $wpdb->get_var( $wpdb->prepare( $query, $value, 0 ) );
			$msg = sprintf( _n( '%d comment', '%d comments', $comment_count ), $comment_count );
		} else {
			$author_url = $comment->comment_author_url;
			// Want to get the root domain and not use the exact pingback/trackback source link
			$parsed_url = parse_url( $author_url );
			$author_url = $parsed_url['scheme'] . '://' . $parsed_url['host'];
			$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_author_url LIKE %s AND comment_type = %s AND comment_approved = %d";
			$comment_count = $wpdb->get_var( $wpdb->prepare( $query, $author_url.'%', $type, 1 ) );
			$pending_count = $wpdb->get_var( $wpdb->prepare( $query, $author_url.'%', $type, 0 ) );
			$author_email = $author_url;
			/* Translators: sorry, but I'm not supplying explicit translation strings for all possible other comment types.
			   You can at least expect '%d trackback', '%d trackbacks', '%d pingback' and '%d pingbacks' */
			$msg = sprintf( _n( '%d %s', '%d %ss', $comment_count ), $comment_count, $type );
		}
		if ( $pending_count )
			$msg .= '; ' . sprintf( __( '%s pending' ), $pending_count );
		$url = $comment_count+$pending_count > 0 ? 'edit-comments.php?s=' . esc_attr( $author_email ) : '#';
		return "
			<div class='post-com-count-wrapper' style='position:relative; display:inline;'><strong>
			<a class='author-com-count post-com-count' href='$url' title='$msg' style=''><span class='comment-count'>$comment_count</span></a>
			</strong></div>$author_name";
	}

	/**
	 * Filter for WP's get_comment_author_link() that returns the value of
	 * comment_author() when in the admin.
	 */
	function get_comment_author_link( $author_link ) {
		if ( !is_admin() ) return $author_link;
		return AdminCommentersCommentsCount::comment_author( get_comment_author() );
	}
} // end AdminCommentersCommentsCount

AdminCommentersCommentsCount::init();

endif; // end if !class_exists()

?>