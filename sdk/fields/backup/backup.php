<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'ADMTH_Field_backup' ) ) {
  class ADMTH_Field_backup extends ADMTH_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'admth_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'admth-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      

      echo '<textarea name="admth_import_data" class="admth-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary admth-confirm admth-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'admth' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="admth-export-data">'. esc_attr( wp_json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary admth-export" target="_blank">'. esc_html__( 'Export & Download', 'admth' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="admth_transient[reset]" value="reset" class="button admth-warning-primary admth-confirm admth-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'admth' ) .'</button>';

      

    }

  }
}
