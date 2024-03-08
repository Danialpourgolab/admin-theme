<?php

namespace AdminTheme;

use AdminTheme\Includes\ServerInfo;
use AdminTheme\Includes\SingletonAbstract;
use AdminTheme\Includes\WPConfigTransformer;
use ADMTH;
use Exception;

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( AdminTheme::class ) ) {
	class AdminTheme extends SingletonAbstract {

		/**
		 * @throws Exception
		 */
		public function init(): void {

			self::load_text_domain( 'admin-dashboard-theme', plugin_dir_path( __DIR__ ) . 'languages' );

			register_activation_hook( __FILE__, [ __CLASS__, 'activation' ] );
			add_action( 'after_setup_theme', [ __CLASS__, 'load_codestar' ], - 20 );

			add_action( "init", [ __CLASS__, 'admin_init' ] );
			add_action( 'admin_dashboard_theme_create_section', [ __CLASS__, 'create_menu_theme_select' ] );
			add_action( 'admin_dashboard_theme_create_section', [ __CLASS__, 'create_menu_module_select' ] );
			add_action( 'admin_dashboard_theme_create_section', [ __CLASS__, 'create_menu_information' ] );

			add_action( 'wp_before_admin_bar_render', [ __CLASS__, 'admth_admin_bar_logo' ] );
			add_action( 'login_head', [ __CLASS__, 'admth_login_logo' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'register_assets' ], 999 );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admth_theme_styles' ], 999 );
			self::load_text_domain( 'admth', plugin_dir_path( __DIR__ ) . 'languages' );
			self::admth_wordpress_debug();
			self::admth_wp_memory_limit();
		}

		public static function load_text_domain( $domain, $folder_path ) {

			$locale  = determine_locale();
			$mo_file = "$domain-$locale.mo";
			$sss     = rtrim( $folder_path, '/\\' ) . '/' . ltrim( $mo_file, '/\\' );
			load_textdomain( $domain, $sss );
		}


		public static function get_prefix(): string {
			return 'admin-theme';
		}

		public static function admin_init(): void {
			if ( ! is_admin() ) {
				return;
			}

			$prefix = self::get_prefix();
			$arg    = [
				'menu_icon'          => 'dashicons-art',
				'menu_title'         => esc_html__( 'Admin Theme', 'admin-dashboard-theme' ),
				'framework_title'    => '<div class="admin-theme-menu-title"><img src="' . esc_url( ADMTH_ASSETS_URL . '/images/admin-setting-icon.png' ) . '" alt="Icon" class="admin-theme-menu-icon" /><span class="admin-theme-menu-title-text">' . esc_html__( 'Admin Theme', 'admin-dashboard-theme' ) . '</span></div>',
				'menu_slug'          => $prefix,
				'show_reset_all'     => false,
				'show_bar_menu'      => false,
				'show_sub_menu'      => false,
				'show_in_customizer' => false,
				'show_reset_section' => false,
				'show_footer'        => false,
				'show_search'        => false,
				'sticky_header'      => false,
				'theme'              => 'light',
				'footer_credit'      => esc_html__( 'Designed with ❤️ by ', 'admin-dashboard-theme' ) . '<a href="https://pourgolab.ir">' . esc_html__( "Danial Pourgolab", "admin-dashboard-theme" ) . '</a>',
			];
			ADMTH::createOptions( $prefix, $arg );

			do_action( "admin_dashboard_theme_create_section", $prefix );
		}

		public static function activation(): void {
			do_action( 'admin_dashboard_theme_activation' );
		}

		public static function remove_menu() {
			remove_submenu_page( 'tools.php', 'admth-welcome' );
		}

		public static function load_codestar() {
			if ( ! class_exists( "ADMTH" ) ) {
				do_action( "load_codestar_before" );
				add_action( 'admin_menu', [ __CLASS__, 'remove_menu' ], 100 );
				require_once ADMTH_SDK_DIR . '/admth-framework.php';
				do_action( "load_codestar_after" );
			}
		}

		public static function get_page(): string {
			return isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		}

		public static function create_menu_theme_select() {
			ADMTH::createSection( self::get_prefix(), [
				'title'       => esc_html__( 'Themes', 'admin-dashboard-theme' ),
				'icon'        => 'admth-icon-themes',
				'description' => '<div class="admth-description-div"><span class="admth-description-span">' . esc_html__( 'Select your dashboard theme', 'admin-dashboard-theme' ) . '</span></div>',
				'fields'      => array(
					array(
						'id'      => 'admth-theme-select',
						'type'    => 'image_select',
						'title'   => esc_html__( 'Select Your Theme', 'admin-dashboard-theme' ),
						'options' => array(
							'minimal-light' => ADMTH_ASSETS_URL . '/images/dashboard-theme/minimal-light.png',
							'minimal-dark'  => ADMTH_ASSETS_URL . '/images/dashboard-theme/minimal-dark.png'
						),
					),
				),
			] );
		}

		public static function create_menu_module_select() {
			ADMTH::createSection( self::get_prefix(), [
				'title'       => esc_html__( 'Modules', 'admin-dashboard-theme' ),
				'icon'        => 'admth-icon-modules',
				'description' => '<div class="admth-description-div"><span class="admth-description-span">' . esc_html__( 'You can select some extra modules here', 'admin-dashboard-theme' ) . '</span></div>',
				'fields'      => array(
					array(
						'type'    => 'content',
						'class'   => 'alert-simple',
						'content' => esc_html__( 'Important Note: As long as the plugin is active, enabling or disabling debugging is done from this section.', 'admin-dashboard-theme' ),
					),
					array(
						'id'    => 'wordpress-debug',
						'type'  => 'switcher',
						'title' => esc_html__( 'Wordpress Debug', 'admin-dashboard-theme' ),
						'label' => esc_html__( 'You can active wordpress debugging.', 'admin-dashboard-theme' )
					),
					array(
						'id'         => 'wordpress-debug-download',
						'type'       => 'callback',
						'function'   => [ __CLASS__, 'download_debug_log_file' ],
						'title'      => esc_html__( 'Download Wordpress Debug Log', 'admin-dashboard-theme' ),
						'label'      => esc_html__( 'You can active wordpress debugging.', 'admin-dashboard-theme' ),
						'dependency' => [ 'wordpress-debug', '==', 'true' ],
					),
					array(
						'id'    => 'host-resource',
						'type'  => 'switcher',
						'title' => esc_html__( 'Host Resource', 'admin-dashboard-theme' ),
						'label' => esc_html__( 'Do you want to show your host resource in admin dashboard?', 'admin-dashboard-theme' )
					),
					array(
						'type'    => 'content',
						'class'   => 'alert-simple',
						'content' => esc_html__( 'Enter just 64 or 128 or 256 or 512 or 1024 and enter 0 for default.', 'admin-dashboard-theme' ),
					),
					array(
						'id'       => 'wp-memory-limit',
						'type'     => 'text',
						'title'    => esc_html__( 'Wp Memory Limit', 'admin-dashboard-theme' ),
						'validate' => 'admth_validate_numeric',
					),
					array(
						'id'      => 'wordpress-logo-replace',
						'type'    => 'media',
						'title'   => esc_html__( 'Wordpress Logo Replace', 'admin-dashboard-theme' ),
						'library' => 'image',
					),
				)
			] );
		}

		public static function create_menu_information() {
			ADMTH::createSection( self::get_prefix(), [
				'title'       => esc_html__( 'System Info', 'admin-dashboard-theme' ),
				'icon'        => 'admth-icon-info',
				'description' => '<div class="admth-description-div"><span class="admth-description-span">' . esc_html__( 'System information', 'admin-dashboard-theme' ) . '</span></div>',
				'fields'      => array(
					array(
						'id'       => 'system-info-display',
						'type'     => 'callback',
						'function' => [ __CLASS__, 'system_information_display' ],
					),
				)
			] );
		}

		public static function register_assets() {
			if ( self::get_page() === 'admin-theme' ) {
				wp_enqueue_style( 'admin-theme-setting', ADMTH_ASSETS_URL . '/css/style.css' );
			}
		}

		public static function get_options( $key = '' ) {
			$options = get_option( self::get_prefix() ) ?? [];
			if ( ! empty( $key ) ) {
				return $options[ $key ] ?? [];
			}

			return $options;
		}

		public static function system_information_display() {
			if ( self::get_options( 'host-resource' ) == 1 ) {
				$server_info          = new ServerInfo();
				$wp_memory_percentage = $server_info->wp_memory_usage_percentage();
				$wp_memory_data       = $server_info->get_wp_memory_usage();
				$server_memory_data   = $server_info->get_server_memory_usage();
				$disk_info            = $server_info->get_server_disk_size();
				$ram_info             = $server_info->get_server_ram_details();
				$cpu_load             = $server_info->get_cpu_load_average();
				$php_version          = $server_info->get_php_version();

				echo '<div class="server-info-container">';

				echo '<h2>' . esc_html__( 'Server Information', 'admin-dashboard-theme' ) . '</h2>';

				echo '<div class="admth-wrap-table">';
				echo '<table class="server-info-table">';
				echo '<tr class="table-header">';
				echo '<td><strong>Item</strong></td>';
				echo '<td><strong>Value</strong></td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >WordPress Version</td>';
				echo '<td >' . $server_info->get_wp_version() . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >PHP Version</td>';
				echo '<td >' . $php_version . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >WP Memory Usage Percentage</td>';
				echo '<td >' . $wp_memory_percentage . '%</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >WP Memory Limit</td>';
				echo '<td >' . $wp_memory_data['MemLimitFormat'] . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >Server Memory Limit</td>';
				echo '<td >' . $server_memory_data['MemLimitFormat'] . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >Server CPU Load Average</td>';
				echo '<td >' . $cpu_load . '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td >Server Disk Size</td>';
				echo '<td >' . ( isset( $disk_info['size'] ) ? $disk_info['size'] . ' GB' : 'N/A' ) . '</td>';
				echo '</tr>';

				echo '</table>';
				echo '</div>';
				echo '<h2>' . esc_html__( 'Server RAM Details', 'admin-dashboard-theme' ) . '</h2>';

				if ( is_array( $ram_info ) && ! empty( $ram_info ) ) {
					echo '<div class="admth-wrap-table">';
					echo '<table class="server-ram-table">';
					echo '<tr class="table-header">';
					echo '<td><strong>Item</strong></td>';
					echo '<td><strong>Value</strong></td>';
					echo '</tr>';

					foreach ( $ram_info as $key => $value ) {
						echo '<tr>';
						echo '<td >' . $key . '</td>';
						echo '<td >' . $value . '</td>';
						echo '</tr>';
					}

					echo '</table>';
					echo '</div>';
				} else {
					echo '<p>' . esc_html__( 'No RAM details available.', 'admin-dashboard-theme' ) . '</p>';
				}

				echo '</div>';
			} else {
				esc_html_e( 'You must activate system info in the module section first', 'admin-dashboard-theme' );
			}
		}

		/**
		 * @throws Exception
		 */
		public static function admth_wordpress_debug() {
			$wp_config_transformer = new WPConfigTransformer( ABSPATH . 'wp-config.php' );
			$enable_debug          = self::get_options( 'wordpress-debug' );
			$wp_config_transformer->update( 'constant', 'WP_DEBUG', $enable_debug, [ 'raw' => true ] );
			$wp_config_transformer->update( 'constant', 'WP_DEBUG_LOG', $enable_debug, [ 'raw' => true ] );
		}

		/**
		 * @throws \WpOrg\Requests\Exception
		 */
		public static function admth_wp_memory_limit() {
			$wp_config_transformer  = new WPConfigTransformer( ABSPATH . 'wp-config.php' );
			$wp_memory_limit_change = self::get_options( 'wp-memory-limit' );
			if ( in_array( $wp_memory_limit_change, [ 64, 128, 256, 512, 1024 ] ) ) {
				$wp_config_transformer->add( 'constant', 'WP_MEMORY_LIMIT', $wp_memory_limit_change . 'M' );
			} elseif ( $wp_memory_limit_change == 0 ) {
				$wp_config_transformer->remove( 'constant', 'WP_MEMORY_LIMIT' );
			}

		}

		public static function admth_admin_bar_logo() {

			$admth_wordpress_logo = self::get_options( 'wordpress-logo-replace' )['url'];
			if ( $admth_wordpress_logo ) {
				echo '<style type="text/css">
            #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
                background-image: url("' . $admth_wordpress_logo . '") !important;
                background-position: 0 0;
                background-size: cover;
                color: rgba(0, 0, 0, 0);
            }
        </style>';
			}
		}

		public static function admth_login_logo() {
			$admth_wordpress_logo = self::get_options( 'wordpress-logo-replace' )['url'];
			if ( $admth_wordpress_logo ) {
				echo '<style type="text/css">
                    h1 a { background-image: url("' . $admth_wordpress_logo . '") !important; }
                </style>';
			}
		}

		public static function download_debug_log_file() {
			$debug_log_path = WP_CONTENT_DIR . '/debug.log';

			if ( file_exists( $debug_log_path ) ) {
				$download_url = home_url( '/wp-content/debug.log' );
				?>
                <a href="<?php echo esc_url( $download_url ); ?>" class="admth-dl-btn"
                   download="debug-log"><?php echo esc_html__( 'Download Debug Log', 'admin-dashboard-theme' ) ?></a>
				<?php
			} else {
				echo 'Debug log file not found.';
			}
		}

		public static function admth_theme_styles() {
			$admth_theme_select = self::get_options( 'admth-theme-select' );
			if ( self::get_page() !== 'admin-theme' ) {
				if ( in_array( $admth_theme_select, [ 'minimal-light', 'minimal-dark' ] ) ) {
					wp_enqueue_style( 'admin-theme-styles', ADMTH_ASSETS_URL . '/css/themes-style/admth-' . $admth_theme_select . '.css' );
				}
			}
		}
	}
}