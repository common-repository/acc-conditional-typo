<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: dimensions
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'WPGP_Field_dimensions' ) ) {
  class WPGP_Field_dimensions extends WPGP_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'width_icon'         => '<i class="fa fa-arrows-h"></i>',
        'height_icon'        => '<i class="fa fa-arrows-v"></i>',
        'width_placeholder'  => esc_html__( 'width', 'wpgp' ),
        'height_placeholder' => esc_html__( 'height', 'wpgp' ),
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
      $is_unit = ( ! empty( $unit ) ) ? ' wpgp--is-unit' : '';

      echo $this->field_before();

      echo '<div class="wpgp--inputs">';

      if( ! empty( $args['width'] ) ) {
        $placeholder = ( ! empty( $args['width_placeholder'] ) ) ? ' placeholder="'. $args['width_placeholder'] .'"' : '';
        echo '<div class="wpgp--input">';
        echo ( ! empty( $args['width_icon'] ) ) ? '<span class="wpgp--label wpgp--icon">'. $args['width_icon'] .'</span>' : '';
        echo '<input type="number" name="'. $this->field_name('[width]') .'" value="'. $value['width'] .'"'. $placeholder .' class="wpgp-input-number'. $is_unit .'" />';
        echo ( ! empty( $unit ) ) ? '<span class="wpgp--label wpgp--unit">'. $args['units'][0] .'</span>' : '';
        echo '</div>';
      }

      if( ! empty( $args['height'] ) ) {
        $placeholder = ( ! empty( $args['height_placeholder'] ) ) ? ' placeholder="'. $args['height_placeholder'] .'"' : '';
        echo '<div class="wpgp--input">';
        echo ( ! empty( $args['height_icon'] ) ) ? '<span class="wpgp--label wpgp--icon">'. $args['height_icon'] .'</span>' : '';
        echo '<input type="number" name="'. $this->field_name('[height]') .'" value="'. $value['height'] .'"'. $placeholder .' class="wpgp-input-number'. $is_unit .'" />';
        echo ( ! empty( $unit ) ) ? '<span class="wpgp--label wpgp--unit">'. $args['units'][0] .'</span>' : '';
        echo '</div>';
      }

      if( ! empty( $args['unit'] ) && ! empty( $args['show_units'] ) && count( $args['units'] ) > 1 ) {
        echo '<div class="wpgp--input">';
        echo '<select name="'. $this->field_name('[unit]') .'">';
        foreach( $args['units'] as $unit ) {
          $selected = ( $value['unit'] === $unit ) ? ' selected' : '';
          echo '<option value="'. $unit .'"'. $selected .'>'. $unit .'</option>';
        }
        echo '</select>';
        echo '</div>';
      }

      echo '</div>';

      echo $this->field_after();

    }

    public function output() {

      $output    = '';
      $element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];
      $wpgp_prefix    = ( ! empty( $this->field['output_prefix'] ) ) ? $this->field['output_prefix'] .'-' : '';
      $important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
      $unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';
      $width     = ( isset( $this->value['width'] ) && $this->value['width'] !== '' ) ? $wpgp_prefix .'width:'. $this->value['width'] . $unit . $important .';' : '';
      $height    = ( isset( $this->value['height'] ) && $this->value['width'] !== '' ) ? $wpgp_prefix .'height:'. $this->value['height'] . $unit . $important .';' : '';

      if( $width !== '' || $height !== '' ) {
        $output = $element .'{'. $width . $height .'}';
      }

      $this->parent->output_css .= $output;

      return $output;

    }

  }
}
