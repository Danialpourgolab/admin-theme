<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: dimensions
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'ADMTH_Field_dimensions' ) ) {
  class ADMTH_Field_dimensions extends ADMTH_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'width_icon'         => '<i class="fas fa-arrows-alt-h"></i>',
        'height_icon'        => '<i class="fas fa-arrows-alt-v"></i>',
        'width_placeholder'  => esc_html__( 'width', 'admth' ),
        'height_placeholder' => esc_html__( 'height', 'admth' ),
        'width'              => true,
        'height'             => true,
        'unit'               => true,
        'show_units'         => true,
        'units'              => array( 'px', '%', 'em' )
      ) );

      $default_values = array(
        'width'  => '',
        'height' => '',
        'unit'   => 'px',
      );

      $value   = wp_parse_args( $this->value, $default_values );
      $unit    = ( count( $args['units'] ) === 1 && ! empty( $args['unit'] ) ) ? $args['units'][0] : '';
      $is_unit = ( ! empty( $unit ) ) ? ' admth--is-unit' : '';

      

      echo '<div class="admth--inputs" data-depend-id="'. esc_attr( $this->field['id'] ) .'">';

      if ( ! empty( $args['width'] ) ) {
        $placeholder = ( ! empty( $args['width_placeholder'] ) ) ? ' placeholder="'. esc_attr( $args['width_placeholder'] ) .'"' : '';
        echo '<div class="admth--input">';
        echo ( ! empty( $args['width_icon'] ) ) ? '<span class="admth--label admth--icon">'. $args['width_icon'] .'</span>' : '';
        echo '<input type="number" name="'. esc_attr( $this->field_name( '[width]' ) ) .'" value="'. esc_attr( $value['width'] ) .'"'. $placeholder .' class="admth-input-number'. esc_attr( $is_unit ) .'" step="any" />';
        echo ( ! empty( $unit ) ) ? '<span class="admth--label admth--unit">'. esc_attr( $args['units'][0] ) .'</span>' : '';
        echo '</div>';
      }

      if ( ! empty( $args['height'] ) ) {
        $placeholder = ( ! empty( $args['height_placeholder'] ) ) ? ' placeholder="'. esc_attr( $args['height_placeholder'] ) .'"' : '';
        echo '<div class="admth--input">';
        echo ( ! empty( $args['height_icon'] ) ) ? '<span class="admth--label admth--icon">'. $args['height_icon'] .'</span>' : '';
        echo '<input type="number" name="'. esc_attr( $this->field_name( '[height]' ) ) .'" value="'. esc_attr( $value['height'] ) .'"'. $placeholder .' class="admth-input-number'. esc_attr( $is_unit ) .'" step="any" />';
        echo ( ! empty( $unit ) ) ? '<span class="admth--label admth--unit">'. esc_attr( $args['units'][0] ) .'</span>' : '';
        echo '</div>';
      }

      if ( ! empty( $args['unit'] ) && ! empty( $args['show_units'] ) && count( $args['units'] ) > 1 ) {
        echo '<div class="admth--input">';
        echo '<select name="'. esc_attr( $this->field_name( '[unit]' ) ) .'">';
        foreach ( $args['units'] as $unit ) {
          $selected = ( $value['unit'] === $unit ) ? ' selected' : '';
          echo '<option value="'. esc_attr( $unit ) .'"'. esc_attr( $selected ) .'>'. esc_attr( $unit ) .'</option>';
        }
        echo '</select>';
        echo '</div>';
      }

      echo '</div>';

      

    }

    public function output() {

      $output    = '';
      $element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];
      $prefix    = ( ! empty( $this->field['output_prefix'] ) ) ? $this->field['output_prefix'] .'-' : '';
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
      $unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';
      $width     = ( isset( $this->value['width'] ) && $this->value['width'] !== '' ) ? $prefix .'width:'. $this->value['width'] . $unit . $important .';' : '';
      $height    = ( isset( $this->value['height'] ) && $this->value['height'] !== '' ) ? $prefix .'height:'. $this->value['height'] . $unit . $important .';' : '';

      if ( $width !== '' || $height !== '' ) {
        $output = $element .'{'. $width . $height .'}';
      }

      $this->parent->output_css .= $output;

      return $output;

    }

  }
}
