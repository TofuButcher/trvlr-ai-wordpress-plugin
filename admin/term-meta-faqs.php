<?php

/**
 * FAQ repeater on location and category term screens.
 *
 * @package    Trvlr
 * @subpackage Trvlr/admin
 */

require_once plugin_dir_path( __FILE__ ) . 'class-trvlr-term-meta-repeater.php';

/**
 * @return Trvlr_Term_Meta_Repeater
 */
function trvlr_get_term_faqs_repeater() {
	static $instance = null;

	if ( $instance instanceof Trvlr_Term_Meta_Repeater ) {
		return $instance;
	}

	$instance = new Trvlr_Term_Meta_Repeater(
		'trvlr_faqs',
		__( 'Frequently asked questions', 'trvlr' ),
		array(
			array(
				'id'    => 'question',
				'label' => __( 'Question', 'trvlr' ),
			),
			array(
				'id'    => 'answer',
				'label' => __( 'Answer', 'trvlr' ),
				'type'  => 'textarea',
			),
		)
	);

	return $instance;
}

/**
 * @param WP_Term $term
 * @return void
 */
function trvlr_render_term_faqs_fields( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	echo '<div class="trvlr-meta-fields-grid" style="margin: 24px 0 0; max-width: 100%;">';
	echo '<div class="trvlr-meta-span-12">';
	trvlr_get_term_faqs_repeater()->render( (int) $term->term_id );
	echo '</div>';
	echo '</div>';
}

/**
 * @param int $term_id
 * @return void
 */
function trvlr_save_term_faqs( $term_id ) {
	$term_id = (int) $term_id;
	if ( $term_id <= 0 ) {
		return;
	}

	trvlr_get_term_faqs_repeater()->save( $term_id );
}

foreach ( array( 'location', 'category' ) as $trvlr_faq_taxonomy ) {
	add_action( $trvlr_faq_taxonomy . '_edit_form', 'trvlr_render_term_faqs_fields', 20 );
	add_action( 'edited_' . $trvlr_faq_taxonomy, 'trvlr_save_term_faqs' );
	add_action( 'created_' . $trvlr_faq_taxonomy, 'trvlr_save_term_faqs' );
}
