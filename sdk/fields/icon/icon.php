<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'ADMTH_Field_icon' ) ) {
  class ADMTH_Field_icon extends ADMTH_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'admth' ),
        'remove_title' => esc_html__( 'Remove Icon', 'admth' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'admth_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="admth-icon-select">';
      echo '<span class="admth-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary admth-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button admth-warning-primary admth-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="admth-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'ADMTH_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'ADMTH_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="admth-modal-icon" class="admth-modal admth-modal-icon hidden">
        <div class="admth-modal-table">
          <div class="admth-modal-table-cell">
            <div class="admth-modal-overlay"></div>
            <div class="admth-modal-inner">
              <div class="admth-modal-title">
                <?php esc_html_e( 'Add Icon', 'admth' ); ?>
                <div class="admth-modal-close admth-icon-close"></div>
              </div>
              <div class="admth-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'admth' ); ?>" class="admth-icon-search" />
              </div>
              <div class="admth-modal-content">
                <div class="admth-modal-loading"><div class="admth-loading"></div></div>
                <div class="admth-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
