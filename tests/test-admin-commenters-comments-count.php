<?php

class Admin_Commenters_Comments_Count_Test extends WP_UnitTestCase {

	/**
	 *
	 * HELPER FUNCTIONS
	 *
	 */


	private function create_comments( $post_id = null, $count = 1, $name = 'alpha', $comment_info = array() ) {
		$default_comment_info = array(
			'comment_approved'     => '1',
			'comment_author'       => ucfirst( $name ) . ' User',
			'comment_author_email' => $name . '@example.org',
			'comment_author_url'   => 'http://example.org/' . $name . '/',
		);
		$comment_info = wp_parse_args( $comment_info, $default_comment_info );

		if ( ! $post_id ) {
			$post_id = $this->factory->post->create();
		}

		if ( 1 == $count ) {
			$comments = $this->factory->comment->create( array_merge( array( 'comment_post_ID' => $post_id ), $comment_info ) );
		} else {
			$comments = $this->factory->comment->create_post_comments( $post_id, $count, $comment_info );
		}

		return $comments;
	}

	private function expected_output( $approved_count = 0, $pending_count = 0, $name = '', $email = '' ) {
		$title = sprintf( _n( '%d comment', '%d comments', $approved_count ), $approved_count );
		$class = '';
		if ( $pending_count > 0 ) {
			$title .= "; $pending_count pending";
			$class = ' author-com-pending';
		}
		return "
			<div class='post-com-count-wrapper post-and-author-com-count-wrapper'>
			<a class='author-com-count post-com-count$class' href='edit-comments.php?s=" . esc_attr( urlencode( $email ) ) . "' title='" . esc_attr( $title ) . "'>
			<span class='comment-count'>$approved_count</span>
			</a></div>$name";
	}

	private function get_comment_author_output( $comment_id ) {
		ob_start();
		comment_author( $comment_id );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}


	/**
	 *
	 * TESTS
	 *
	 */

	function test_not_in_admin_area() {
		$this->assertFalse( is_admin() );
	}

	function test_class_not_available_on_frontend() {
		$this->assertFalse( class_exists( 'c2c_AdminCommentersCommentsCount' ) );
	}

	function test_get_comment_author_link_unaffected_on_frontend() {
		$comments = $this->create_comments( null, 3 );
		$GLOBALS['comment'] = get_comment( $comments[0] );

		$this->AssertEquals( "<a href='http://example.org/alpha/' rel='external nofollow' class='url'>Alpha User</a>", get_comment_author_link( $comments[0] ) );
		$this->assertEquals( 'originallink', apply_filters( 'get_comment_author_link', 'originallink' ) );
	}

	/*
	 * TESTS AFTER THIS SHOULD ASSUME THEY ARE IN THE ADMIN AREA
	 */

	// This should be the first of the admin area tests and is
	// necessary to set the environment to be the admin area.
	function test_in_admin_area() {
		define( 'WP_ADMIN', true );
		// Re-require plugin file in admin context so class gets loaded
		require( './admin-commenters-comments-count.php' );
		// Re-fire 'plugins_loaded' action so plugin can set itself up
		do_action( 'plugins_loaded' );

		$this->assertTrue( is_admin() );
	}

	function test_class_is_available_on_backend() {
		$this->assertTrue( class_exists( 'c2c_AdminCommentersCommentsCount' ) );
	}

	function test_get_comment_author_link_affected_on_backend() {
		$post_id = $this->factory->post->create();

		$this->create_comments( $post_id, 5, 'alpha' );
		$bravo_comments = $this->create_comments( $post_id, 2, 'bravo' );
		$comment_id = $this->create_comments( $post_id, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$GLOBALS['comment'] = get_comment( $comment_id );

		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), get_comment_author_link( $comment_id ) );
		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), c2c_AdminCommentersCommentsCount::get_comment_author_link( $comment_id ) );

		$GLOBALS['comment'] = get_comment( $bravo_comments[0] );

		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), get_comment_author_link( $comment_id ) );
		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), c2c_AdminCommentersCommentsCount::get_comment_author_link( $bravo_comments[0] ) );
	}

	function test_comment_author_link_affected_on_backend() {
		$post_id = $this->factory->post->create();

		$this->create_comments( $post_id, 5, 'alpha' );
		$bravo_comments = $this->create_comments( $post_id, 2, 'bravo' );
		$comment_id = $this->create_comments( $post_id, 1, 'alpha', array( 'comment_approved' => '0' ) );

		$GLOBALS['comment'] = get_comment( $comment_id );

		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), $this->get_comment_author_output( $comment_id ) );
		$this->assertEquals( $this->expected_output( 5, 1, 'Alpha User', 'alpha@example.org' ), c2c_AdminCommentersCommentsCount::comment_author( $comment_id ) );

		$GLOBALS['comment'] = get_comment( $bravo_comments[0] );

		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), $this->get_comment_author_output( $bravo_comments[0] ) );
		$this->assertEquals( $this->expected_output( 2, 0, 'Bravo User', 'bravo@example.org' ), c2c_AdminCommentersCommentsCount::comment_author( $bravo_comments[0] ) );
	}

}
