<?php

/**
 * Define all custom meta fields for attractions
 * 
 * @package Trvlr
 */

// Include Helper
require_once plugin_dir_path( __FILE__ ) . 'class-trvlr-meta-repeater.php';

// Initialize Repeater Objects Globally so we can access them in save hooks
function trvlr_get_repeater_instances() {
    return array(
        'pricing' => new Trvlr_Meta_Repeater( 'trvlr_attraction', 'trvlr_pricing', 'Attraction Pricing', array(
            array( 'id' => 'type', 'label' => 'Price Type' ), 
            array( 'id' => 'price', 'label' => 'Price' ),
            array( 'id' => 'sale_price', 'label' => 'Sale Price' ), 
        )),
        'locations' => new Trvlr_Meta_Repeater( 'trvlr_attraction', 'trvlr_locations', 'Locations', array(
            array( 'id' => 'type', 'label' => 'Type (Start/End)' ),
            array( 'id' => 'address', 'label' => 'Address' ),
            array( 'id' => 'lat', 'label' => 'Latitude' ),
            array( 'id' => 'lng', 'label' => 'Longitude' ),
        )),
    );
}

function trvlr_register_meta_boxes() {
    
    // 1. Consolidated Details Box (Normal context with Core priority to appear right after title)
    add_meta_box(
        'trvlr_attraction_details',
        'Attraction Details',
        'trvlr_render_details_meta_box',
        'trvlr_attraction',
        'normal',
        'core'
    );
    
    // Hide Native Custom Fields
    remove_meta_box( 'postcustom', 'trvlr_attraction', 'normal' );
}
add_action( 'add_meta_boxes', 'trvlr_register_meta_boxes' );

// Move content editor below our meta box
function trvlr_move_editor_below_metabox() {
    global $post, $wp_meta_boxes;
    
    if ( get_post_type() === 'trvlr_attraction' ) {
        // Remove the editor from its default position
        remove_post_type_support( 'trvlr_attraction', 'editor' );
    }
}
add_action( 'admin_head', 'trvlr_move_editor_below_metabox' );

// Add the editor back after our meta box
function trvlr_add_editor_after_metabox() {
    global $post;
    
    if ( get_post_type() === 'trvlr_attraction' ) {
        add_meta_box(
            'trvlr_content_editor',
            'Content',
            'trvlr_render_editor_meta_box',
            'trvlr_attraction',
            'normal',
            'low'
        );
    }
}
add_action( 'add_meta_boxes', 'trvlr_add_editor_after_metabox', 20 );

// Render the editor in our custom meta box
function trvlr_render_editor_meta_box( $post ) {
    $content = $post->post_content;
    wp_editor( $content, 'content', array(
        'textarea_rows' => 15,
        'teeny' => false,
        'media_buttons' => true
    ));
}


// Render ALL Details in One Box
function trvlr_render_details_meta_box( $post ) {
    wp_nonce_field( 'trvlr_save_details', 'trvlr_details_nonce' );
    
    $trvlr_id = get_post_meta( $post->ID, 'trvlr_id', true );
    $description = get_post_meta( $post->ID, 'trvlr_description', true );
    $short_desc = get_post_meta( $post->ID, 'trvlr_short_description', true );
    $duration = get_post_meta( $post->ID, 'trvlr_duration', true );
    $start_time = get_post_meta( $post->ID, 'trvlr_start_time', true );
    $end_time = get_post_meta( $post->ID, 'trvlr_end_time', true );
    $is_on_sale = get_post_meta( $post->ID, 'trvlr_is_on_sale', true );
    $sale_description = get_post_meta( $post->ID, 'trvlr_sale_description', true );
    $media_ids = get_post_meta( $post->ID, 'trvlr_media', true );
    $inclusions = get_post_meta( $post->ID, 'trvlr_inclusions', true );
    $highlights = get_post_meta( $post->ID, 'trvlr_highlights', true );
    $additional_info = get_post_meta( $post->ID, 'trvlr_additional_info', true );
    
    ?>
    <style>
        .trvlr-row { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .trvlr-row label { font-weight: bold; display: block; margin-bottom: 5px; }
        .trvlr-row .description { margin-top: 5px; font-style: italic; color: #666; }
        .trvlr-repeater-section { margin-top: 20px; border-top: 2px solid #ddd; padding-top: 10px; }
        .trvlr-repeater-section h4 { margin: 0 0 10px 0; font-size: 1.1em; }
        .wp-editor-wrap { width: 100%; }
        .trvlr-media-gallery { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .trvlr-media-gallery .media-item { position: relative; }
        .trvlr-media-gallery .media-item img { width: 100px; height: 100px; object-fit: cover; }
        .trvlr-media-gallery .media-item .remove { position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; }
    </style>

    <div class="trvlr-row">
        <label>TRVLR ID</label>
        <input type="text" class="regular-text" value="<?php echo esc_attr($trvlr_id); ?>" readonly disabled>
        <span class="description">System ID (Read Only)</span>
    </div>

    <div class="trvlr-row">
        <label>Description (Full)</label>
        <?php wp_editor( $description, 'trvlr_description', array( 'media_buttons' => false, 'textarea_rows' => 10 ) ); ?>
    </div>

    <div class="trvlr-row">
        <label>Short Description</label>
        <?php wp_editor( $short_desc, 'trvlr_short_description', array( 'media_buttons' => false, 'textarea_rows' => 3, 'teeny' => true ) ); ?>
    </div>

    <div class="trvlr-row">
        <label>Duration</label>
        <input type="text" name="trvlr_duration" value="<?php echo esc_attr($duration); ?>" class="regular-text">
        <span class="description">e.g., "2 hours", "Full day"</span>
    </div>

    <div class="trvlr-row">
        <div style="display:flex; gap:20px; flex-wrap:wrap;">
            <div style="flex:1; min-width:200px;">
                <label>Start Time</label>
                <input type="time" name="trvlr_start_time" value="<?php echo esc_attr($start_time); ?>" class="regular-text">
            </div>
            <div style="flex:1; min-width:200px;">
                <label>End Time</label>
                <input type="time" name="trvlr_end_time" value="<?php echo esc_attr($end_time); ?>" class="regular-text">
            </div>
        </div>
    </div>

    <div class="trvlr-row">
        <label>
            <input type="checkbox" name="trvlr_is_on_sale" value="1" <?php checked( $is_on_sale, '1' ); ?>>
            Is On Sale?
        </label>
    </div>

    <div class="trvlr-row">
        <label>Sale Description</label>
        <input type="text" name="trvlr_sale_description" value="<?php echo esc_attr($sale_description); ?>" class="widefat">
    </div>

    <!-- Pricing Repeater -->
    <?php 
    $repeaters = trvlr_get_repeater_instances();
    $repeaters['pricing']->render( $post->ID );
    ?>

    <div class="trvlr-row" style="margin-top:20px;">
        <label>Media Gallery (Images/Videos)</label>
        <div class="trvlr-media-gallery" id="trvlr-media-gallery">
            <?php
            if ( ! empty( $media_ids ) && is_array( $media_ids ) ) {
                foreach ( $media_ids as $attachment_id ) {
                    $img_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                    if ( $img_url ) {
                        echo '<div class="media-item" data-id="' . esc_attr( $attachment_id ) . '">';
                        echo '<img src="' . esc_url( $img_url ) . '" />';
                        echo '<button type="button" class="remove" onclick="trvlrRemoveMedia(this)">×</button>';
                        echo '<input type="hidden" name="trvlr_media[]" value="' . esc_attr( $attachment_id ) . '">';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
        <button type="button" class="button" onclick="trvlrOpenMediaUploader(event)">Add Media</button>
    </div>

    <!-- Locations Repeater -->
    <?php $repeaters['locations']->render( $post->ID ); ?>

    <div class="trvlr-row" style="margin-top:20px;">
        <label>Inclusions</label>
        <?php wp_editor( $inclusions, 'trvlr_inclusions', array( 'media_buttons' => false, 'textarea_rows' => 5 ) ); ?>
    </div>

    <div class="trvlr-row">
        <label>Highlights</label>
        <?php wp_editor( $highlights, 'trvlr_highlights', array( 'media_buttons' => false, 'textarea_rows' => 5 ) ); ?>
    </div>

    <div class="trvlr-row">
        <label>Additional Info</label>
        <?php wp_editor( $additional_info, 'trvlr_additional_info', array( 'media_buttons' => false, 'textarea_rows' => 5 ) ); ?>
    </div>

    <script>
    function trvlrOpenMediaUploader(e) {
        e.preventDefault();
        var frame = wp.media({
            title: 'Select or Upload Media',
            button: { text: 'Add to Gallery' },
            multiple: true
        });
        
        frame.on('select', function() {
            var selection = frame.state().get('selection');
            var gallery = document.getElementById('trvlr-media-gallery');
            
            selection.forEach(function(attachment) {
                attachment = attachment.toJSON();
                var itemHtml = '<div class="media-item" data-id="' + attachment.id + '">' +
                    '<img src="' + attachment.sizes.thumbnail.url + '" />' +
                    '<button type="button" class="remove" onclick="trvlrRemoveMedia(this)">×</button>' +
                    '<input type="hidden" name="trvlr_media[]" value="' + attachment.id + '">' +
                    '</div>';
                gallery.insertAdjacentHTML('beforeend', itemHtml);
            });
        });
        
        frame.open();
    }
    
    function trvlrRemoveMedia(btn) {
        if (confirm('Remove this media item?')) {
            btn.closest('.media-item').remove();
        }
    }
    </script>

    <?php
}

// Save All Fields
function trvlr_save_details_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Save Standard Details
    if ( isset( $_POST['trvlr_details_nonce'] ) && wp_verify_nonce( $_POST['trvlr_details_nonce'], 'trvlr_save_details' ) ) {
        
        $text_fields = array('trvlr_duration', 'trvlr_start_time', 'trvlr_end_time', 'trvlr_sale_description');
        foreach ($text_fields as $field) {
            if ( isset( $_POST[$field] ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
            }
        }

        $editor_fields = array('trvlr_description', 'trvlr_short_description', 'trvlr_inclusions', 'trvlr_highlights', 'trvlr_additional_info');
        foreach ($editor_fields as $field) {
            if ( isset( $_POST[$field] ) ) {
                update_post_meta( $post_id, $field, wp_kses_post( $_POST[$field] ) );
            }
        }
        
        // Checkbox
        update_post_meta( $post_id, 'trvlr_is_on_sale', isset( $_POST['trvlr_is_on_sale'] ) ? '1' : '0' );
        
        // Media Gallery
        // Only update if explicitly provided in POST (don't delete if field not submitted)
        if ( isset( $_POST['trvlr_media'] ) ) {
            if ( is_array( $_POST['trvlr_media'] ) && !empty( $_POST['trvlr_media'] ) ) {
                $media_ids = array_map( 'intval', $_POST['trvlr_media'] );
                update_post_meta( $post_id, 'trvlr_media', $media_ids );
            } else {
                // Empty array explicitly submitted - clear the gallery
                delete_post_meta( $post_id, 'trvlr_media' );
            }
        }
        // If not in POST, leave unchanged (user didn't interact with gallery field)
    }

    // Save Repeaters
    $repeaters = trvlr_get_repeater_instances();
    foreach ( $repeaters as $r ) {
        $r->save( $post_id );
    }
}
add_action( 'save_post', 'trvlr_save_details_meta' );
