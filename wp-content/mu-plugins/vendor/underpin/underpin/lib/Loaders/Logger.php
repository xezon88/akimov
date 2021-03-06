<?php
/**
 *
 *
 * @since
 * @package
 */


namespace Underpin\Loaders;

use Exception;
use Underpin\Abstracts\Registries\Object_Registry;
use Underpin\Abstracts\Underpin;
use Underpin\Interfaces\Singleton;
use Underpin\Abstracts\Event_Type;
use Underpin\Factories\Log_Item;
use Underpin\Factories\Observers\Trigger_Exception;
use Underpin\Factories\Observers\Trigger_Notice;
use Underpin\Traits\With_Subject;
use WP_Error;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Logger
 * Houses methods to manage event logging
 *
 * @since   1.0.0
 * @package Underpin\Loaders
 */
final class Logger extends Object_Registry implements Singleton {

	use With_Subject;

	/**
	 * @inheritDoc
	 */
	protected $abstraction_class = 'Underpin\Abstracts\Event_Type';

	protected $default_factory = 'Underpin\Factories\Event_Type_Instance';

	/**
	 * Determines if the logger should log events, or not. Can be changed with mute, and unmute.
	 *
	 * @var bool
	 */
	protected static $is_muted = false;

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @inheritDoc
	 */
	public function add( $key, $value ) {
		$valid = parent::add( $key, $value );

		// If valid, set up actions.
		if ( true === $valid ) {
			$this->get( $key )->do_actions();
		}

		return $valid;
	}

	public function __construct() {
		self::mute();
		parent::__construct();
		$this->notify( 'init' );
		$this->set_default_items();
		self::unmute();
	}

	/**
	 * @return self
	 */
	public static function instance(): Singleton {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Retrieves all events that have happened for this request.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $type The event type to retrieve. If false, this will get all events.
	 *
	 * @return array|WP_Error list of all events, or a WP_Error if something went wrong.
	 */
	public static function get_request_events( $type = false ) {
		if ( false !== $type ) {
			$events = self::instance()->get( $type );

			// Return the error.
			if ( is_wp_error( $events ) ) {
				return $events;
			} else {
				return (array) $events;
			}

		} else {
			$result = [];
			foreach ( self::instance() as $type => $events ) {
				$result[ $type ] = (array) self::instance()->get( $type );
			}

			return $result;
		}
	}

	/**
	 * Enqueues an event to be logged in the system
	 *
	 * @since 1.0.0
	 *
	 * @param string $type    Event log type
	 * @param string $code    The event code to use.
	 * @param string $message The message to log.
	 * @param array  $data    Arbitrary data associated with this event message.
	 *
	 * @return Log_Item|WP_Error Log item, with error message. WP_Error if something went wrong.
	 */
	public static function log( $type, $code, $message, $data = array() ) {
		if ( self::is_muted() ) {
			return new WP_Error(
				'logger_is_muted',
				'Could not log event because this was ran in a muted action',
				[
					'type'    => $type,
					'code'    => $code,
					'message' => $message,
					'data'    => $data,
				]
			);
		}

		$event_type = self::instance()->get( $type );

		if ( is_wp_error( $event_type ) ) {
			return $event_type;
		}

		$item = $event_type->log( $code, $message, $data );
		self::instance()->notify( 'event:logged', ['event' => $item, 'event_type' => $event_type]);

		return $item;
	}

	/**
	 * Mutes the logger.
	 *
	 * @since 1.0.0
	 */
	private static function mute() {
		self::$is_muted = true;
	}

	/**
	 * Un-Mutes the logger.
	 *
	 * @since 1.0.0
	 */
	private static function unmute() {
		self::$is_muted = false;
	}

	/**
	 * Does an action, muting all events that would otherwise happen.
	 *
	 * @since 1.2.4
	 *
	 * @param callable $action The muted action to call.
	 *
	 * @return mixed|WP_Error The action returned result, or WP_Error if something went wrong.
	 */
	public static function do_muted_action( $action ) {
		if ( is_callable( $action ) ) {
			self::mute();
			$result = $action();
			self::unmute();
		} else {
			$result = new WP_Error(
				'muted_action_not_callable',
				'The provided muted action is not callable',
				[ 'ref' => $action, 'type' => 'function' ]
			);
		}

		return $result;
	}

	/**
	 * Fetches the mute status of the logger.
	 *
	 * @since 1.0.0
	 */
	public static function is_muted() {
		return self::$is_muted;
	}

	/**
	 * Enqueues an event to be logged in the system, and then returns a WP_Error object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type    Event log type
	 * @param string $code    The event code to use.
	 * @param string $message The message to log.
	 * @param array  $data    Arbitrary data associated with this event message.
	 *
	 * @return WP_Error Log item, with error message. WP_Error if something went wrong.
	 */
	public static function log_as_error( $type, $code, $message, $data = array() ) {
		$item = self::instance()->log( $type, $code, $message, $data );

		if ( ! is_wp_error( $item ) ) {
			$item = $item->error();
		}

		return $item;
	}

	/**
	 * Logs an error using a WP Error object.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $type     Error log type
	 * @param WP_Error $wp_error Instance of WP_Error to use for log
	 * @param array    $data     Additional data to log
	 *
	 * @return Log_Item|WP_Error The logged item, if successful, otherwise WP_Error.
	 */
	public static function log_wp_error( $type, WP_Error $wp_error, $data = [] ) {
		$item = self::instance()->get( $type );

		if ( is_wp_error( $item ) ) {
			return $item;
		}

		$error = $item->log_wp_error( $wp_error, $data );

		return $error;
	}

	/**
	 * Logs an error from within an exception.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $type      Error log type
	 * @param Exception $exception Exception instance to log.
	 * @param array     $data      array Data associated with this error message
	 *
	 * @return Log_Item|WP_Error Log Item, with error message if successful, otherwise WP_Error.
	 */
	public static function log_exception( $type, Exception $exception, $data = array() ) {
		$item = self::instance()->get( $type );

		if ( is_wp_error( $type ) ) {
			return $item;
		}

		$error = $item->log_exception( $exception, $data );

		return $error;
	}

	/**
	 * @param string $key
	 *
	 * @return Event_Type|WP_Error Event type, if it exists. WP_Error, otherwise.
	 */
	public function get( $key ) {
		$result = parent::get( $key );

		// If the logged event could not be found, search event type keys.
		if ( is_wp_error( $result ) ) {
			$event_type = $this->find( [
				'type' => $key,
			] );

			// If that also comes up empty, return the error.
			if ( is_wp_error( $event_type ) ) {
				return $result;
			}

			// Return the discovered event.
			$result = $event_type;
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	protected function set_default_items() {
		$defaults = [
			'group' => 'core',
		];

		$this->add( 'emergency', array_merge( $defaults, [
			'type'        => 'emergency',
			'description' => 'Intended to be used only for the most-severe events.',
			'name'        => "Emergency",
			'psr_level'   => 'emergency',
		] ) );

		$this->add( 'alert', array_merge( $defaults, [
			'type'        => 'alert',
			'description' => 'Intended to be used when someone should be notified about this problem.',
			'name'        => "Alert",
			'psr_level'   => 'alert',
		] ) );

		$this->add( 'critical', array_merge( $defaults, [
			'type'        => 'critical',
			'description' => 'Intended to log events when an error occurs that is potentially damaging.',
			'name'        => "Critical Error",
			'psr_level'   => 'critical',
		] ) );

		$this->add( 'error', array_merge( $defaults, [
			'type'        => 'error',
			'description' => 'Intended to log events when something goes wrong.',
			'name'        => "Error",
			'psr_level'   => 'error',
		] ) );

		if ( Underpin::is_debug_mode_enabled() ) {

			$this->add( 'warning', array_merge( [
				'type'        => 'warning',
				'description' => 'Intended to log events when something seems wrong.',
				'name'        => 'Warning',
				'psr_level'   => 'warning',
			] ) );

			$this->add( 'notice', array_merge( [
				'type'        => 'notice',
				'description' => 'Posts informative notices when something is neither good nor bad.',
				'name'        => 'Notice',
				'psr_level'   => 'notice',
			] ) );

			$this->add( 'info', array_merge( [
				'type'        => 'info',
				'description' => 'Posts informative messages that something is most-likely going as-expected.',
				'name'        => 'Info',
				'psr_level'   => 'info',
			] ) );

			$this->add( 'debug', array_merge( [
				'type'        => 'debug',
				'description' => 'A place to put information that is only useful in debugging context.',
				'name'        => 'Debug',
				'psr_level'   => 'debug',
			] ) );
		}
	}

	/**
	 * Gathers errors from a set of variables.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed ...$items
	 *
	 * @return WP_Error
	 */
	public static function gather_errors( ...$items ) {
		$errors = new WP_Error();
		$items  = func_get_args();
		foreach ( $items as $item ) {
			self::extract( $errors, $item );
		}

		return $errors;
	}

	/**
	 * Appends errors to a WP_Error object.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Error          $error    The error to append to. Passed by reference.
	 * @param Log_Item|WP_Error $log_item The log item to append. If this has multiple errors, it will append all of them.
	 *
	 * @return void
	 */
	public static function extract( WP_Error &$error, $log_item ) {

		// Transform the log item into a WP_Error, if it is a Log_item
		if ( $log_item instanceof Log_Item ) {
			$log_item = $log_item->error();
		}

		// Append the error, if it is an error.
		if ( $log_item instanceof WP_Error ) {
			foreach ( $log_item->get_error_codes() as $code ) {
				$error->add( $code, $log_item->get_error_message( $code ), $log_item->get_error_data( $code ) );
			}
		}
	}

}