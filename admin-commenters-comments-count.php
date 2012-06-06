<?php
/**
 * @package Admin_Commenters_Comments_Count
 * @author Scott Reilly
 * @version 1.2
 */
/*
Plugin Name: Admin Commenters Comments Count
Version: 1.2
Plugin URI: http://coffee2code.com/wp-plugins/admin-commenters-comments-count/
Author: Scott Reilly
Author URI: http://coffee2code.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Description: Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.

Compatible with WordPress 2.8 through 3.4+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/extend/plugins/admin-commenters-comments-count/

TODO:
	* When a comments gets approved/unapproved via comment action links, update commenter's count accordingly
*/

/*
	Copyright (c) 2009-2012 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( is_admin() && ! class_exists( 'c2c_AdminCommentersCommentsCount' ) ) :

class c2c_AdminCommentersCommentsCount {

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.1.4
	 */
	public static function version() {
		return '1.2';
	}

	/**
	 * Class constructor: initializes class variables and adds actions and filters.
	 */
	public static function init() {
		add_action( 'admin_head',              array( __CLASS__, 'add_css' ) );
		add_filter( 'comment_author',          array( __CLASS__, 'comment_author' ) );
		add_filter( 'get_comment_author_link', array( __CLASS__, 'get_comment_author_link' ) );
	}

	/**
	 * Outputs CSS within style tags
	 */
	public static function add_css() {
		echo <<<CSS
		<style type="text/css">
		.author-com-count {float:right;text-align:center;margin-right:5px;margin-top:2px;height:1.3em;line-height:1.1em;}
		#dashboard_recent_comments .author-com-count {margin-top:4px;}
		.author-com-count:hover {background-position:22% -3px;}
		#the-comment-list a.author-com-count {background-position:center -80px;}
		#the-comment-list a.author-com-count span {background-color:#bbb;color:#fff;}
		#the-comment-list a.author-com-count.author-com-pending {background-position:center -55px;}
		#the-comment-list a.author-com-count.author-com-pending span {background-color:#21759B;}
		#the-comment-list a.author-com-count:hover, #the-comment-list a.author-com-count.author-com-pending:hover {background-position:center -3px;}
		#the-comment-list a.author-com-count:hover span, #the-comment-list a.author-com-count.author-com-pending:hover span {background-color:#d54e21;}
		div.post-and-author-com-count-wrapper {position:relative; display:inline;}
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
	public static function comment_author( $author_name ) {
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
		} elseif ( 'pingback' == $type || 'trackback' == $type ) {
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
		} else {
			return $author_name;
		}

		if ( $pending_count ) {
			$msg .= '; ' . sprintf( __( '%s pending' ), $pending_count );
			$pclass = ' author-com-pending';
		} else {
			$pclass = '';
		}

		$url = $comment_count+$pending_count > 0 ? 'edit-comments.php?s=' . esc_attr( urlencode( $author_email ) ) : '#';

		return "
			<div class='post-com-count-wrapper post-and-author-com-count-wrapper'>
			<a class='author-com-count post-com-count $pclass' href='$url' title='" . esc_attr( $msg ) . "' style=''>
			<span class='comment-count'>$comment_count</span>
			</a></div>$author_name";
	}

	/**
	 * Filter for WP's get_comment_author_link() that returns the value of
	 * comment_author() when in the admin.
	 *
	 * @param string $author_link Author link
	 * @return string Modified author link
	 */
	public static function get_comment_author_link( $author_link ) {
		if ( ! is_admin() )
			return $author_link;
		return self::comment_author( get_comment_author() );
	}
} // end c2c_AdminCommentersCommentsCount

c2c_AdminCommentersCommentsCount::init();

endif; // end if !class_exists()
