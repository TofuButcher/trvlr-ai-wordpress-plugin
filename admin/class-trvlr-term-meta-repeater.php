<?php

/**
 * Repeatable term meta rows (same UI as Trvlr_Meta_Repeater).
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

class Trvlr_Term_Meta_Repeater {

	/** @var string */
	private $meta_key;

	/** @var string */
	private $label;

	/** @var array<int, array{id: string, label: string, type?: string}> */
	private $fields;

	/**
	 * @param string $meta_key
	 * @param string $label
	 * @param array  $fields Column definitions: each has `id`, `label`, optional `type` (`text`|`textarea`).
	 */
	public function __construct( $meta_key, $label, $fields = array() ) {
		$this->meta_key = $meta_key;
		$this->label    = $label;
		$this->fields   = $fields;
	}

	/**
	 * @param int $term_id
	 * @return void
	 */
	public function render( $term_id ) {
		wp_nonce_field( 'trvlr_save_' . $this->meta_key, 'trvlr_' . $this->meta_key . '_nonce' );

		$values = get_term_meta( $term_id, $this->meta_key, true );
		if ( empty( $values ) || ! is_array( $values ) ) {
			$values = array();
		}

		echo '<div class="trvlr-repeater-section">';
		echo '<h4>' . esc_html( $this->label ) . '</h4>';

		echo '<div class="trvlr-repeater-wrapper" id="trvlr-repeater-' . esc_attr( $this->meta_key ) . '">';
		echo '<table class="trvlr-repeater-table widefat">';
		echo '<thead><tr>';

		foreach ( $this->fields as $field ) {
			echo '<th>' . esc_html( $field['label'] ) . '</th>';
		}

		echo '<th class="trvlr-repeater-actions"><span class="screen-reader-text">Actions</span></th>';
		echo '</tr></thead>';
		echo '<tbody class="trvlr-repeater-rows">';

		if ( ! empty( $values ) ) {
			foreach ( $values as $index => $row_data ) {
				$this->render_row( $index, $row_data );
			}
		}

		echo '</tbody>';
		echo '</table>';

		echo '<div class="trvlr-repeater-footer">';
		echo '<button type="button" class="button trvlr-add-row" data-key="' . esc_attr( $this->meta_key ) . '">Add Row</button>';
		echo '</div>';

		echo '<script type="text/template" id="tmpl-trvlr-repeater-' . esc_attr( $this->meta_key ) . '">';
		$this->render_row( '{{index}}', array() );
		echo '</script>';

		echo '</div>';
		echo '</div>';
	}

	/**
	 * @param int|string $index
	 * @param array      $data
	 * @return void
	 */
	private function render_row( $index, $data ) {
		echo '<tr class="trvlr-repeater-row">';

		foreach ( $this->fields as $field ) {
			$field_id   = $field['id'];
			$field_type = isset( $field['type'] ) ? $field['type'] : 'text';
			$input_name = $this->meta_key . '[' . $index . '][' . $field_id . ']';
			$value      = isset( $data[ $field_id ] ) ? $data[ $field_id ] : '';

			echo '<td data-label="' . esc_attr( $field['label'] ) . '">';

			if ( $field_type === 'textarea' ) {
				echo '<textarea name="' . esc_attr( $input_name ) . '" class="widefat" rows="2">' . esc_textarea( $value ) . '</textarea>';
			} else {
				echo '<input type="text" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $value ) . '" class="widefat">';
			}

			echo '</td>';
		}

		echo '<td class="trvlr-repeater-actions">';
		echo '<a href="#" class="trvlr-remove-row" title="Remove row" aria-label="Remove row">−</a>';
		echo '</td>';
		echo '</tr>';
	}

	/**
	 * @param int $term_id
	 * @return void
	 */
	public function save( $term_id ) {
		if ( ! isset( $_POST[ 'trvlr_' . $this->meta_key . '_nonce' ] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST[ 'trvlr_' . $this->meta_key . '_nonce' ], 'trvlr_save_' . $this->meta_key ) ) {
			return;
		}

		if ( isset( $_POST[ $this->meta_key ] ) && is_array( $_POST[ $this->meta_key ] ) ) {
			$new_values = array();
			foreach ( $_POST[ $this->meta_key ] as $row ) {
				$empty         = true;
				$sanitized_row = array();
				foreach ( $this->fields as $field ) {
					$fid = $field['id'];
					if ( isset( $row[ $fid ] ) ) {
						if ( isset( $field['type'] ) && $field['type'] === 'textarea' ) {
							$sanitized_row[ $fid ] = wp_kses_post( $row[ $fid ] );
						} else {
							$sanitized_row[ $fid ] = sanitize_text_field( $row[ $fid ] );
						}
						if ( ! empty( $sanitized_row[ $fid ] ) ) {
							$empty = false;
						}
					}
				}
				if ( ! $empty ) {
					$new_values[] = $sanitized_row;
				}
			}
			update_term_meta( $term_id, $this->meta_key, $new_values );
		} else {
			delete_term_meta( $term_id, $this->meta_key );
		}
	}

}
