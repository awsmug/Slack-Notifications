<?php
/**
 * Comment notifications.
 *
 * @package     SlackNotifications\Notifications
 * @subpackage  Comment
 * @author      Dor Zuberi <webmaster@dorzki.co.il>
 * @link        https://www.dorzki.co.il
 * @since       2.0.0
 * @version     2.0.0
 */

namespace SlackNotifications\Notifications;

// Block direct access to the file via url.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Comment
 *
 * @package SlackNotifications\Notifications
 */
class Comment extends Notification_Type {

	/**
	 * Comment constructor.
	 */
	public function __construct() {

		$this->object_type    = 'comment';
		$this->object_label   = esc_html__( 'Comments', 'dorzki-notifications-to-slack' );
		$this->object_options = [
			'new_comment' => [
				'label'    => esc_html__( 'New Comment', 'dorzki-notifications-to-slack' ),
				'hooks'    => [
					'comment_post' => 'new_comment',
				],
				'priority' => 10,
				'params'   => 1,
			],
		];

		parent::__construct();

	}


	/**
	 * Post notification when a new comment has posted.
	 *
	 * @param $comment_id
	 *
	 * @return bool
	 */
	public function new_comment( $comment_id ) {

		// Get comment
		$comment = get_comment( $comment_id );

		if ( is_wp_error( $comment ) ) {
			return false;
		}

		// Build notification
		$message = __( ':speech_balloon: A new comment has been posted on *<%s|%s>*!', 'dorzki-notifications-to-slack' );
		$message = sprintf( $message, get_permalink( $comment->comment_post_ID ), get_the_title( $comment->comment_post_ID ) );

		$attachments = [
			[
				'title' => esc_html__( 'Comment Author', 'dorzki-notifications-to-slack' ),
				'value' => $comment->comment_author,
				'short' => true,
			],
			[
				'title' => esc_html__( 'Author Email', 'dorzki-notifications-to-slack' ),
				'value' => $comment->comment_author_email,
				'short' => true,
			],
			[
				'title' => esc_html__( 'Comment Content', 'dorzki-notifications-to-slack' ),
				'value' => $comment->comment_content,
				'short' => false,
			],
		];

		$channel = $this->get_notification_channel( __FUNCTION__ );

		return $this->slack_bot->send_message( $message, $attachments, [
			'color'   => '#e67e22',
			'channel' => $channel,
		] );

	}

}