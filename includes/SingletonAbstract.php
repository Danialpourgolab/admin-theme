<?php


namespace AdminTheme\Includes;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

if ( ! class_exists( SingletonAbstract::class ) ) {
	abstract class SingletonAbstract {

		/**
		 * Singleton instance.
		 */
		protected static $instance = [];

		/**
		 * Instantiate the singleton.
		 */
		public static function instance( $id = '', ...$params ) {
			$class = static::class;

			if ( empty( $id ) ) {
				$id = $class;
			}

			if ( ! isset( self::$instance[ $id ] ) ) {
				/**
				 * Help phpstorm autocomplete
				 *
				 * @var static $class
				 */
				self::$instance[ $id ] = new $class( ...$params );
			}

			return self::$instance[ $id ];
		}

		/**
		 * Construct
		 */
		public function __construct() {
			$this->init();
		}

		abstract public function init(): void;

	}
}