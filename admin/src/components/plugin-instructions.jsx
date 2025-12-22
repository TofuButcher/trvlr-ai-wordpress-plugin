import React from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   Panel,
   PanelBody,
   PanelRow,
   __experimentalText as Text,
   Notice,
} from '@wordpress/components';

const getInstructionSteps = () => [
   {
      title: __('1. Connect to TRVLR', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('Navigate to the Connection tab and enter your Organization ID.', 'trvlr')}
            </Text>
            <Notice status="info" isDismissible={false} style={{ display: 'flex', flexDirection: 'column', alignItems: 'flex-start', gap: '12px', marginTop: '12px' }}>
               <p style={{ margin: 0, marginBottom: '4px' }}>{__('Your organization ID is your websites domain without the any prefixes ( subdomain. / www.) or suffixes ( .com / .org ).', 'trvlr')}</p>
               <p style={{ margin: 0 }}>{__('For example if your website is https://www.example.com, your organization ID is "example"', 'trvlr')}</p>
            </Notice>
         </>
      ),
      dropdowns: []
   },
   {
      title: __('2. Sync Your Attractions', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('Navigate to the Sync tab to import your attractions from TRVLR.', 'trvlr')}
            </Text>
            <div style={{ marginTop: '12px', marginBottom: '12px' }}>
               <strong>{__('Manual Sync:', 'trvlr')}</strong>
               <Text> {__('Click "Sync Now" to import attractions on demand. Progress is shown in real-time.', 'trvlr')}</Text>
            </div>
            <div style={{ marginBottom: '12px' }}>
               <strong>{__('Automatic Sync:', 'trvlr')}</strong>
               <Text> {__('Enable scheduled syncing to keep attractions updated automatically.', 'trvlr')}</Text>
            </div>
            <Notice status="warning" isDismissible={false}>
               {__('Manual edits made in WordPress are preserved during sync. Review them in the Custom Edits section or use the "Sync from TRVLR" button on individual attraction pages to override.', 'trvlr')}
            </Notice>
         </>
      ),
      dropdowns: []
   },
   {
      title: __('3. Add Booking Buttons', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('Add booking functionality to any button or link by adding these attributes:', 'trvlr')}
            </Text>
            <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
               <li><code>class="trvlr-book-now"</code></li>
               <li><code>attraction-id="YOUR_TRVLR_ID"</code></li>
            </ul>
         </>
      ),
      dropdowns: [
         {
            title: __('Code Example', 'trvlr'),
            content: (
               <pre style={{
                  background: '#f6f7f7',
                  padding: '12px',
                  borderRadius: '4px',
                  overflow: 'auto',
                  fontSize: '13px'
               }}>
                  {`<button 
   class="trvlr-book-now" 
   attraction-id="123">
   Book Now
</button>`}
               </pre>
            )
         }
      ]
   },
   {
      title: __('4. Display Attractions', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('Use shortcodes to display attractions on any page:', 'trvlr')}
            </Text>
            <div style={{ marginTop: '16px', marginBottom: '16px' }}>
               <strong>{__('All Attractions:', 'trvlr')}</strong>
               <div style={{ marginTop: '8px' }}>
                  <code>[trvlr_attraction_cards]</code>
               </div>
            </div>
            <div style={{ marginTop: '16px', marginBottom: '16px' }}>
               <strong>{__('Single Attraction:', 'trvlr')}</strong>
               <div style={{ marginTop: '8px' }}>
                  <code>[trvlr_attraction_card id="123"]</code>
               </div>
            </div>
            <div style={{ marginTop: '16px', marginBottom: '16px' }}>
               <strong>{__('Booking Calendar:', 'trvlr')}</strong>
               <div style={{ marginTop: '8px' }}>
                  <code>[trvlr_booking_calendar]</code>
               </div>
            </div>
         </>
      ),
      dropdowns: [
         {
            title: __('Shortcode Parameters', 'trvlr'),
            content: (
               <>
                  <strong style={{ display: 'block', marginTop: '12px' }}>[trvlr_attraction_cards]</strong>
                  <ul style={{ marginTop: '8px', marginBottom: '16px' }}>
                     <li><code>posts_per_page</code> - Number of attractions (-1 for all, default: -1)</li>
                     <li><code>orderby</code> - Sort by: date, title, meta_value, etc. (default: date)</li>
                     <li><code>order</code> - ASC or DESC (default: DESC)</li>
                     <li><code>ids</code> - Comma-separated list of post IDs to include</li>
                     <li><code>exclude</code> - Comma-separated list of post IDs to exclude</li>
                     <li><code>tag</code> - Filter by tag name (comma-separated)</li>
                     <li><code>tag_id</code> - Filter by tag ID (comma-separated)</li>
                     <li><code>tag_slug</code> - Filter by tag slug (comma-separated)</li>
                     <li><code>tag_relation</code> - AND or OR for multiple tags (default: AND)</li>
                     <li><code>meta_key</code> - Meta key to filter/sort by</li>
                     <li><code>meta_value</code> - Meta value to match</li>
                     <li><code>meta_compare</code> - Comparison operator (=, !=, &gt;, &lt;, etc.)</li>
                  </ul>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px',
                     marginBottom: '16px'
                  }}>
                     {`// Basic usage
[trvlr_attraction_cards posts_per_page="6" orderby="title" order="ASC"]

// Filter by tags
[trvlr_attraction_cards tag="popular,featured" tag_relation="OR"]

// Filter by meta field
[trvlr_attraction_cards meta_key="trvlr_is_on_sale" meta_value="1"]

// Exclude specific attractions
[trvlr_attraction_cards exclude="10,20,30"]`}
                  </pre>

                  <strong style={{ display: 'block', marginTop: '12px' }}>[trvlr_attraction_card]</strong>
                  <ul style={{ marginTop: '8px', marginBottom: '16px' }}>
                     <li><code>id</code> - Attraction post ID (defaults to current post)</li>
                  </ul>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px',
                     marginBottom: '16px'
                  }}>
                     {`[trvlr_attraction_card id="123"]`}
                  </pre>

                  <strong style={{ display: 'block', marginTop: '12px' }}>[trvlr_booking_calendar]</strong>
                  <ul style={{ marginTop: '8px', marginBottom: '16px' }}>
                     <li><code>id</code> - Attraction post ID (auto-detected on single pages)</li>
                     <li><code>width</code> - Calendar width (default: 450px)</li>
                     <li><code>height</code> - Calendar height (default: 600px)</li>
                  </ul>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px'
                  }}>
                     {`[trvlr_booking_calendar id="123" width="500px" height="700px"]`}
                  </pre>

                  <strong style={{ display: 'block', marginTop: '16px' }}>{__('Other Shortcodes:', 'trvlr')}</strong>
                  <ul style={{ marginTop: '8px' }}>
                     <li><code>[trvlr_attraction_gallery]</code> - Display image gallery with thumbnails</li>
                     <li><code>[trvlr_description]</code> - Full description</li>
                     <li><code>[trvlr_short_description]</code> - Short description</li>
                     <li><code>[trvlr_accordion]</code> - Inclusions, locations, and additional info</li>
                     <li><code>[trvlr_duration]</code> - Duration with icon</li>
                     <li><code>[trvlr_advertised_price]</code> - Price display</li>
                     <li><code>[trvlr_sale_badge]</code> - Sale indicator</li>
                  </ul>
               </>
            )
         }
      ]
   },
   {
      title: __('5. Single Attraction Pages', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('The plugin automatically creates detailed pages for each attraction with gallery, description, booking calendar, and more.', 'trvlr')}
            </Text>
         </>
      ),
      dropdowns: [
         {
            title: __('Customize Templates', 'trvlr'),
            content: (
               <>
                  <Text>
                     {__('Create a custom template in your theme:', 'trvlr')}
                  </Text>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     marginTop: '12px',
                     fontSize: '13px'
                  }}>
                     {`/wp-content/themes/your-theme/single-trvlr_attraction.php`}
                  </pre>
                  <Text style={{ marginTop: '12px', display: 'block' }}>
                     {__('Use these template functions:', 'trvlr')}
                  </Text>
                  <ul style={{ marginTop: '8px' }}>
                     <li><code>trvlr_gallery($post_id)</code> - Image gallery</li>
                     <li><code>trvlr_accordion($post_id)</code> - Collapsible content sections</li>
                     <li><code>trvlr_booking_calendar($post_id)</code> - Booking calendar</li>
                     <li><code>trvlr_description($post_id)</code> - Full description</li>
                     <li><code>trvlr_short_description($post_id)</code> - Short description</li>
                     <li><code>trvlr_advertised_price($post_id)</code> - Price display</li>
                     <li><code>trvlr_duration($post_id)</code> - Duration</li>
                     <li><code>trvlr_locations($post_id)</code> - Start/End locations</li>
                  </ul>
               </>
            )
         }
      ]
   },
   {
      title: __('6. Customize Appearance', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('Navigate to the Theme tab to customize colors, typography, and card styles.', 'trvlr')}
            </Text>
            <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
               <li>{__('Colors: Primary, secondary, accent, and sale badge', 'trvlr')}</li>
               <li>{__('Typography: Font sizes for headings and text', 'trvlr')}</li>
               <li>{__('Cards: Border radius, spacing, shadows, and hover effects', 'trvlr')}</li>
            </ul>
            <Text>
               {__('All styling uses CSS variables for easy customization.', 'trvlr')}
            </Text>
         </>
      ),
      dropdowns: []
   },
   {
      title: __('Advanced: PHP Development', 'trvlr'),
      content: () => (
         <>
            <Text>
               {__('For developers building custom templates and features.', 'trvlr')}
            </Text>
         </>
      ),
      dropdowns: [
         {
            title: __('Display Functions', 'trvlr'),
            content: (
               <pre style={{
                  background: '#f6f7f7',
                  padding: '12px',
                  borderRadius: '4px',
                  fontSize: '13px',
                  whiteSpace: 'pre-wrap'
               }}>
                  {`// Display single card
echo trvlr_card($post_id);

// Display multiple cards (with optional args)
echo trvlr_cards($args);

// Display gallery
echo trvlr_gallery($post_id);

// Display booking calendar
echo trvlr_booking_calendar($post_id);

// Display accordion (inclusions/locations/info)
echo trvlr_accordion($post_id);

// Display description content
echo trvlr_description($post_id);
echo trvlr_short_description($post_id);

// Display price
echo trvlr_advertised_price($post_id);

// Display duration with icon
echo trvlr_duration($post_id);

// Display locations
echo trvlr_locations($post_id);`}
               </pre>
            )
         },
         {
            title: __('trvlr_cards() Arguments', 'trvlr'),
            content: (
               <>
                  <Text style={{ marginBottom: '12px' }}>
                     {__('The trvlr_cards() function accepts extensive query arguments:', 'trvlr')}
                  </Text>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px',
                     whiteSpace: 'pre-wrap'
                  }}>
                     {`// Basic arguments
$args = array(
    'posts_per_page' => 12,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
);

// Filter by specific IDs
$args = array(
    'ids' => '123,456,789', // or array(123, 456, 789)
);

// Exclude specific IDs
$args = array(
    'exclude' => '10,20,30',
);

// Filter by attraction tags
$args = array(
    'tag' => 'popular,featured',
    'tag_relation' => 'OR', // AND or OR
);

$args = array(
    'tag_id' => '5,10',
    'tag_slug' => 'water-activities',
);

// Filter by meta fields
$args = array(
    'meta_key' => 'trvlr_duration',
    'meta_value' => '2 hours',
    'meta_compare' => '=', // =, !=, >, <, >=, <=, LIKE, etc.
);

// Advanced meta query
$args = array(
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'trvlr_is_on_sale',
            'value' => '1',
            'compare' => '='
        ),
        array(
            'key' => 'trvlr_duration',
            'value' => '3 hours',
            'compare' => 'LIKE'
        )
    )
);

// Advanced taxonomy query
$args = array(
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'trvlr_attraction_tag',
            'field' => 'slug',
            'terms' => array('popular', 'featured')
        )
    )
);

// Full custom query override
$args = array(
    'query_args' => array(
        'post_type' => 'trvlr_attraction',
        'posts_per_page' => 6,
        'orderby' => 'meta_value_num',
        'meta_key' => 'trvlr_advertised_price_value',
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'trvlr_attraction_tag',
                'field' => 'slug',
                'terms' => 'popular'
            )
        )
    )
);

echo trvlr_cards($args);`}
                  </pre>
               </>
            )
         },
         {
            title: __('Data Getter Functions', 'trvlr'),
            content: (
               <pre style={{
                  background: '#f6f7f7',
                  padding: '12px',
                  borderRadius: '4px',
                  fontSize: '13px',
                  whiteSpace: 'pre-wrap'
               }}>
                  {`// Get TRVLR ID (for booking)
get_trvlr_id($post_id);
get_trvlr_attraction_id($post_id);

// Get text content
get_trvlr_title($post_id);
get_trvlr_description($post_id);
get_trvlr_short_description($post_id);
get_trvlr_inclusions($post_id);
get_trvlr_additional_info($post_id);

// Get timing
get_trvlr_duration($post_id);
get_trvlr_start_time($post_id);
get_trvlr_end_time($post_id);

// Get pricing data
get_trvlr_pricing($post_id); // Returns array
get_trvlr_advertised_price_value($post_id);
get_trvlr_advertised_price_type($post_id);

// Get sale info
get_trvlr_is_on_sale($post_id); // Returns boolean
get_trvlr_sale_description($post_id);

// Get locations
get_trvlr_locations($post_id); // Returns array

// Get media
get_trvlr_media($post_id); // Returns array of IDs
get_post_thumbnail_id($post_id); // Featured image

// Get tags/categories
get_trvlr_attraction_tags($post_id); // Returns WP_Term array

// Get all data
get_trvlr_attraction_all_data($post_id); // Returns associative array`}
               </pre>
            )
         },
         {
            title: __('Helper Functions', 'trvlr'),
            content: (
               <pre style={{
                  background: '#f6f7f7',
                  padding: '12px',
                  borderRadius: '4px',
                  fontSize: '13px',
                  whiteSpace: 'pre-wrap'
               }}>
                  {`// Check if post is an attraction
is_trvlr_attraction($post_id);

// Get organization settings
get_trvlr_organisation_id();
get_trvlr_base_domain($org_id);

// Get primary location
get_trvlr_attraction_primary_location($post_id);

// Get lowest price
get_trvlr_attraction_lowest_price($post_id);

// Get formatted price
get_trvlr_attraction_formatted_price($post_id);`}
               </pre>
            )
         },
         {
            title: __('Custom Loop Examples', 'trvlr'),
            content: (
               <>
                  <strong style={{ display: 'block', marginBottom: '8px' }}>{__('Basic Loop:', 'trvlr')}</strong>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px',
                     whiteSpace: 'pre-wrap',
                     marginBottom: '16px'
                  }}>
                     {`<?php
$args = array(
    'post_type' => 'trvlr_attraction',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC'
);
$query = new WP_Query($args);

if ($query->have_posts()) {
    echo '<div class="trvlr-cards">';
    while ($query->have_posts()) {
        $query->the_post();
        echo trvlr_card(get_the_ID());
    }
    echo '</div>';
    wp_reset_postdata();
}
?>`}
                  </pre>

                  <strong style={{ display: 'block', marginBottom: '8px' }}>{__('Using trvlr_cards() Function:', 'trvlr')}</strong>
                  <pre style={{
                     background: '#f6f7f7',
                     padding: '12px',
                     borderRadius: '4px',
                     fontSize: '13px',
                     whiteSpace: 'pre-wrap',
                     marginBottom: '16px'
                  }}>
                     {`<?php
// Show popular attractions on sale
echo trvlr_cards(array(
    'posts_per_page' => 6,
    'tag' => 'popular',
    'meta_key' => 'trvlr_is_on_sale',
    'meta_value' => '1'
));

// Show attractions by price (lowest first)
echo trvlr_cards(array(
    'posts_per_page' => 8,
    'orderby' => 'meta_value_num',
    'meta_key' => 'trvlr_advertised_price_value',
    'order' => 'ASC'
));

// Advanced: Multiple filters
echo trvlr_cards(array(
    'query_args' => array(
        'post_type' => 'trvlr_attraction',
        'posts_per_page' => 10,
        'tax_query' => array(
            array(
                'taxonomy' => 'trvlr_attraction_tag',
                'field' => 'slug',
                'terms' => array('popular', 'featured'),
                'operator' => 'IN'
            )
        ),
        'meta_query' => array(
            array(
                'key' => 'trvlr_duration',
                'value' => '3 hours',
                'compare' => 'LIKE'
            )
        )
    )
));
?>`}
                  </pre>
               </>
            )
         },
         {
            title: __('Custom Template Example', 'trvlr'),
            content: (
               <pre style={{
                  background: '#f6f7f7',
                  padding: '12px',
                  borderRadius: '4px',
                  fontSize: '13px',
                  whiteSpace: 'pre-wrap'
               }}>
                  {`<?php
// single-trvlr_attraction.php
get_header();

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $post_id = get_the_ID();
        ?>
        <article class="attraction-single">
            <h1><?php echo get_trvlr_title($post_id); ?></h1>
            
            <?php echo trvlr_gallery($post_id); ?>
            
            <div class="attraction-meta">
                <?php echo trvlr_duration($post_id); ?>
                <?php echo trvlr_advertised_price($post_id); ?>
            </div>
            
            <?php echo trvlr_description($post_id); ?>
            <?php echo trvlr_accordion($post_id); ?>
            <?php echo trvlr_booking_calendar($post_id); ?>
        </article>
        <?php
    }
}

get_footer();
?>`}
               </pre>
            )
         }
      ]
   }
];

export const PluginInstructions = () => {
   const steps = getInstructionSteps();

   return (
      <Panel className="trvlr-plugin-instructions">
         {steps.map((step, index) => (
            <PanelBody
               key={index}
               title={step.title}
               initialOpen={false}
            >
               <PanelRow>
                  <div style={{ width: '100%', display: 'flex', flexDirection: 'column', gap: '12px' }}>
                     {step.content()}

                     {step.dropdowns && step.dropdowns.length > 0 && (
                        <Panel>
                           {step.dropdowns.map((dropdown, dropdownIndex) => (
                              <PanelBody
                                 key={dropdownIndex}
                                 title={dropdown.title}
                                 initialOpen={false}
                              >
                                 {dropdown.content}
                              </PanelBody>
                           ))}
                        </Panel>
                     )}
                  </div>
               </PanelRow>
            </PanelBody>
         ))}
      </Panel>
   );
};
