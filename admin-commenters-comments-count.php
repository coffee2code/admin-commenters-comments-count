<?php
/**
 * @package Admin_Commenters_Comments_Count
 * @author Scott Reilly
 * @version 1.3
 */
/*
Plugin Name: Admin Commenters Comments Count
Version: 1.3
Plugin URI: http://coffee2code.com/wp-plugins/admin-commenters-comments-count/
Author: Scott Reilly
Author URI: http://coffee2code.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Description: Displays a count of each commenter's total number of comments (linked to those comments) next to their name on any admin page.

Compatible with WordPress 3.8+

=>> Read the accompanying readme.txt file for instructions and documentation.
=>> Also, visit the plugin's homepage for additional information and updates.
=>> Or visit: http://wordpress.org/plugins/admin-commenters-comments-count/

TODO:
	* When a comments gets approved/unapproved via comment action links, update commenter's count accordingly
	* Allow admin to manually group commenters with different email addresses (allows grouping a person who
	  may be using multiple email addresses, or maybe admin prefers to group people per organization). The reported
	  counts would be for the group and not the individual. The link to see the emails would search for all of the
	  email addresses in the group.
*/

/*
	Copyright (c) 2009-2014 by Scott Reilly (aka coffee2code)

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

defined( 'ABSPATH' ) or die();

if ( is_admin() && ! class_exists( 'c2c_AdminCommentersCommentsCount' ) ) :

class c2c_AdminCommentersCommentsCount {

	/**
	 * Returns version of the plugin.
	 *
	 * @since 1.1.4
	 */
	public static function version() {
		return '1.3';
	}

	/**
	 * Initializer
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'do_init' ) );
	}

	/**
	 * Performs initialization
	 */
	public static function do_init() {
		add_filter( 'comment_author',          array( __CLASS__, 'comment_author'          ) );
		add_filter( 'get_comment_author_link', array( __CLASS__, 'get_comment_author_link' ) );
		add_action( 'admin_init',              array( __CLASS__, 'register_styles'         ) );
		add_action( 'admin_enqueue_scripts',   array( __CLASS__, 'enqueue_admin_css'       ) );
	}

	/**
	 * Registers styles.
	 *
	 * @since 1.3
	 */
	public static function register_styles() {
		wp_register_style( __CLASS__ . '_admin', plugins_url( 'admin.css', __FILE__ ) );
	}

	/**
	 * Enqueues stylesheets if the user has admin expert mode activated.
	 *
	 * @since 1.3
	 */
	public static function enqueue_admin_css() {
		wp_enqueue_style( __CLASS__ . '_admin' );
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

		// If appearing on the dashboard, we need to wrap in <strong> as is done elsewhere.
		$screen = get_current_screen();
		$is_dashboard = $screen && 'dashboard' == $screen->id;
		$html = $is_dashboard ? '<strong>' : '';
		$html .= "
			<div class='post-com-count-wrapper post-and-author-com-count-wrapper'>
			<a class='author-com-count post-com-count$pclass' href='$url' title='" . esc_attr( $msg ) . "'>
			<span class='comment-count'>$comment_count</span>
			</a></div>$author_name";
		$html .= $is_dashboard ? '</strong>' : '';

		return $html;
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
