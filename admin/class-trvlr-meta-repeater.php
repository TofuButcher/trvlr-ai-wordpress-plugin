<?php

/**
 * A helper class to create repeatable meta boxes.
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

class Trvlr_Meta_Repeater {

	private $post_type;
	private $meta_key;
	private $label;
	private $fields;

	public function __construct( $post_type, $meta_key, $label, $fields = array() ) {
		$this->post_type = $post_type;
		$this->meta_key  = $meta_key;
		$this->label     = $label;
		$this->fields    = $fields;
	}

    /**
     * Render the repeater field HTML.
     * Designed to be called inside a meta box callback.
     */
	public function render( $post_id ) {
		wp_nonce_field( 'trvlr_save_' . $this->meta_key, 'trvlr_' . $this->meta_key . '_nonce' );
		
		// Get existing data
		$values = get_post_meta( $post_id, $this->meta_key, true );
		if ( empty( $values ) || ! is_array( $values ) ) {
			$values = array();
		}

        echo '<div class="trvlr-repeater-section">';
        echo '<h4>' . esc_html( $this->label ) . '</h4>';
        
		echo '<div class="trvlr-repeater-wrapper" id="trvlr-repeater-' . esc_attr( $this->meta_key ) . '">';
		echo '<div class="trvlr-repeater-rows">';

		if ( ! empty( $values ) ) {
			foreach ( $values as $index => $row_data ) {
				$this->render_row( $index, $row_data );
			}
		}

		echo '</div>'; // .trvlr-repeater-rows

		echo '<button type="button" class="button trvlr-add-row" data-key="' . esc_attr( $this->meta_key ) . '">Add ' . esc_html( $this->label ) . ' Row</button>';
        
        // Hidden template for JS
        echo '<script type="text/template" id="tmpl-trvlr-repeater-' . esc_attr( $this->meta_key ) . '">';
        $this->render_row( '{{index}}', array() );
        echo '</script>';

		echo '</div>'; // .trvlr-repeater-wrapper
        echo '</div>'; // .trvlr-repeater-section
	}

	private function render_row( $index, $data ) {
		echo '<div class="trvlr-repeater-row" style="border:1px solid #ccc; padding:10px; margin-bottom:10px; background:#f0f0f1;">';
		echo '<div style="text-align:right;"><button type="button" class="button-link trvlr-remove-row" style="color:#a00;">Remove</button></div>';
		echo '<table class="form-table" style="margin:0;">';

		foreach ( $this->fields as $field ) {
			$field_id    = $field['id'];
			$field_label = $field['label'];
			$field_type  = isset( $field['type'] ) ? $field['type'] : 'text';
			
			$input_name  = $this->meta_key . '[' . $index . '][' . $field_id . ']';
			$value       = isset( $data[ $field_id ] ) ? $data[ $field_id ] : '';

			echo '<tr>';
			echo '<th style="padding:5px 0; width:20%; font-weight:normal;"><label>' . esc_html( $field_label ) . '</label></th>';
			echo '<td style="padding:5px 0;">';

			if ( $field_type === 'textarea' ) {
				echo '<textarea name="' . esc_attr( $input_name ) . '" class="widefat" rows="2">' . esc_textarea( $value ) . '</textarea>';
			} else {
				echo '<input type="text" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $value ) . '" class="widefat">';
			}

			echo '</td>';
			echo '</tr>';
		}

		echo '</table>';
		echo '</div>'; // .trvlr-repeater-row
	}

    /**
     * Save the repeater data.
     */
	public function save( $post_id ) {
		if ( ! isset( $_POST[ 'trvlr_' . $this->meta_key . '_nonce' ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST[ 'trvlr_' . $this->meta_key . '_nonce' ], 'trvlr_save_' . $this->meta_key ) ) {
			return;
		}
		// Other checks handled by caller (DOING_AUTOSAVE, capability) usually, but good to check here too if called directly.

		if ( isset( $_POST[ $this->meta_key ] ) && is_array( $_POST[ $this->meta_key ] ) ) {
			$new_values = array();
			foreach ( $_POST[ $this->meta_key ] as $row ) {
                $empty = true;
				$sanitized_row = array();
				foreach ( $this->fields as $field ) {
                    $fid = $field['id'];
                    if ( isset( $row[ $fid ] ) ) {
                        if ( isset($field['type']) && $field['type'] === 'textarea' ) {
                             $sanitized_row[ $fid ] = wp_kses_post( $row[ $fid ] );
                        } else {
                             $sanitized_row[ $fid ] = sanitize_text_field( $row[ $fid ] );
                        }
                        if ( ! empty( $sanitized_row[ $fid ] ) ) $empty = false;
                    }
				}
                if ( ! $empty ) {
				    $new_values[] = $sanitized_row;
                }
			}
			update_post_meta( $post_id, $this->meta_key, $new_values );
		} else {
			// If field is not present at all (and nonce is verified), user might have cleared all rows
            // But be careful: if disabled/hidden input, it might not send.
            // Since we have the nonce, we can assume we should process it.
			delete_post_meta( $post_id, $this->meta_key );
		}
	}

}
