import React from '@wordpress/element';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
   Card,
   CardHeader,
   CardBody,
   CardDivider,
   __experimentalHeading as Heading,
   __experimentalText as Text,
   Panel,
   PanelBody,
   PanelRow,
   Notice,
} from '@wordpress/components';

export const PluginInstructions = () => {
   return (
      <Card>
         <CardHeader>
            <Heading level={2}>{__('Getting Started with TRVLR', 'trvlr')}</Heading>
         </CardHeader>
         <CardBody>
            <Text>
               {__('Follow these steps to connect your WordPress site to TRVLR and start accepting bookings.', 'trvlr')}
            </Text>
         </CardBody>
         <CardDivider />
         <CardBody>
            <Panel>
               <PanelBody
                  title={__('1. Connect to TRVLR', 'trvlr')}
                  initialOpen={true}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Enter your Organization ID and API Key in the Connection Settings above.', 'trvlr')}
                        </Text>
                        <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <li>{__('Organization ID: Required for all features', 'trvlr')}</li>
                           <li>{__('API Key: Required for syncing attractions', 'trvlr')}</li>
                        </ul>
                        <Notice status="info" isDismissible={false}>
                           {__('Find your credentials in your TRVLR dashboard under Settings → API Keys.', 'trvlr')}
                        </Notice>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('2. Verify System Status', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Check the System Status section above to ensure:', 'trvlr')}
                        </Text>
                        <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <li>{__('API Connection: Shows "Active" when credentials are valid', 'trvlr')}</li>
                           <li>{__('Payment Confirmation Page: Shows "Active" when page exists', 'trvlr')}</li>
                        </ul>
                        <Text>
                           {__('The plugin automatically creates a payment confirmation page at /payments. If it doesn\'t exist, it will be created on first sync.', 'trvlr')}
                        </Text>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('3. Sync Your Attractions', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Navigate to the "Sync" tab to import your attractions from TRVLR.', 'trvlr')}
                        </Text>
                        <div style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <strong>{__('Manual Sync:', 'trvlr')}</strong>
                           <Text> {__('Click "Run Manual Sync" to import attractions on demand.', 'trvlr')}</Text>
                        </div>
                        <div style={{ marginBottom: '12px' }}>
                           <strong>{__('Automatic Sync:', 'trvlr')}</strong>
                           <Text> {__('Enable scheduled syncing to keep attractions up-to-date automatically.', 'trvlr')}</Text>
                        </div>
                        <Notice status="warning" isDismissible={false}>
                           {__('Custom edits made in WordPress are preserved during sync. You can review and manage them in the "Custom Edits" section.', 'trvlr')}
                        </Notice>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('4. Add Booking Buttons', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Add booking functionality to any button or link on your site by adding these attributes:', 'trvlr')}
                        </Text>
                        <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <li><code>class="trvlr-book-now"</code></li>
                           <li><code>attraction-id="YOUR_ATTRACTION_ID"</code></li>
                        </ul>
                        <Panel>
                           <PanelBody
                              title={__('View Code Example', 'trvlr')}
                              initialOpen={false}
                           >
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
                           </PanelBody>
                        </Panel>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('5. Display Attractions', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Use these shortcodes to display your attractions:', 'trvlr')}
                        </Text>

                        <div style={{ marginTop: '16px', marginBottom: '16px' }}>
                           <strong>{__('Attraction Cards', 'trvlr')}</strong>
                           <div style={{ marginTop: '8px' }}>
                              <code>[trvlr_attraction_cards]</code>
                           </div>
                           <Text style={{ marginTop: '8px', display: 'block' }}>
                              {__('Displays all attractions in a card grid layout.', 'trvlr')}
                           </Text>
                           <Panel>
                              <PanelBody
                                 title={__('View Parameters', 'trvlr')}
                                 initialOpen={false}
                              >
                                 <ul>
                                    <li><code>posts_per_page</code> - Number of attractions to show (-1 for all)</li>
                                    <li><code>orderby</code> - Sort by: date, title, etc.</li>
                                    <li><code>order</code> - ASC or DESC</li>
                                    <li><code>ids</code> - Comma-separated list of specific attraction IDs</li>
                                 </ul>
                                 <pre style={{
                                    background: '#f6f7f7',
                                    padding: '12px',
                                    borderRadius: '4px',
                                    marginTop: '12px',
                                    fontSize: '13px'
                                 }}>
                                    {`[trvlr_attraction_cards posts_per_page="6" orderby="title"]`}
                                 </pre>
                              </PanelBody>
                           </Panel>
                        </div>

                        <div style={{ marginTop: '16px', marginBottom: '16px' }}>
                           <strong>{__('Booking Calendar', 'trvlr')}</strong>
                           <div style={{ marginTop: '8px' }}>
                              <code>[trvlr_booking_calendar]</code>
                           </div>
                           <Text style={{ marginTop: '8px', display: 'block' }}>
                              {__('Displays an interactive booking calendar. Automatically uses the current attraction\'s ID when placed on an attraction page.', 'trvlr')}
                           </Text>
                           <Panel>
                              <PanelBody
                                 title={__('View Parameters', 'trvlr')}
                                 initialOpen={false}
                              >
                                 <ul>
                                    <li><code>attraction_id</code> - Specific attraction ID (optional if on attraction page)</li>
                                    <li><code>width</code> - Calendar width (default: 100%)</li>
                                    <li><code>height</code> - Calendar height (default: 600px)</li>
                                 </ul>
                                 <pre style={{
                                    background: '#f6f7f7',
                                    padding: '12px',
                                    borderRadius: '4px',
                                    marginTop: '12px',
                                    fontSize: '13px'
                                 }}>
                                    {`[trvlr_booking_calendar attraction_id="123" width="500px" height="700px"]`}
                                 </pre>
                              </PanelBody>
                           </Panel>
                        </div>

                        <div style={{ marginTop: '16px', marginBottom: '16px' }}>
                           <strong>{__('Single Attraction Card', 'trvlr')}</strong>
                           <div style={{ marginTop: '8px' }}>
                              <code>[trvlr_attraction_card id="123"]</code>
                           </div>
                           <Text style={{ marginTop: '8px', display: 'block' }}>
                              {__('Displays a single attraction card.', 'trvlr')}
                           </Text>
                        </div>

                        <div style={{ marginTop: '16px', marginBottom: '16px' }}>
                           <strong>{__('Attraction Gallery', 'trvlr')}</strong>
                           <div style={{ marginTop: '8px' }}>
                              <code>[trvlr_attraction_gallery]</code>
                           </div>
                           <Text style={{ marginTop: '8px', display: 'block' }}>
                              {__('Displays the attraction\'s image gallery with thumbnail navigation.', 'trvlr')}
                           </Text>
                        </div>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('6. Single Attraction Pages', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('The plugin automatically creates detailed pages for each attraction. These pages include:', 'trvlr')}
                        </Text>
                        <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <li>{__('Image gallery', 'trvlr')}</li>
                           <li>{__('Description and details', 'trvlr')}</li>
                           <li>{__('Inclusions and locations', 'trvlr')}</li>
                           <li>{__('Booking calendar', 'trvlr')}</li>
                        </ul>
                        <Panel>
                           <PanelBody
                              title={__('Customize Single Attraction Templates', 'trvlr')}
                              initialOpen={false}
                           >
                              <Text>
                                 {__('To customize the single attraction template, create this file in your theme:', 'trvlr')}
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
                                 {__('Use these template functions in your custom template:', 'trvlr')}
                              </Text>
                              <ul style={{ marginTop: '8px' }}>
                                 <li><code>trvlr_attraction_gallery()</code></li>
                                 <li><code>trvlr_get_attraction_accordion()</code></li>
                                 <li><code>trvlr_render_booking_calendar()</code></li>
                                 <li><code>get_trvlr_attraction_description()</code></li>
                                 <li><code>get_trvlr_attraction_pricing()</code></li>
                              </ul>
                           </PanelBody>
                        </Panel>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('7. Customize Appearance', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('Navigate to the "Theme" tab to customize the appearance of attraction cards and other elements.', 'trvlr')}
                        </Text>
                        <ul style={{ marginTop: '12px', marginBottom: '12px' }}>
                           <li>{__('Colors: Primary, secondary, and accent colors', 'trvlr')}</li>
                           <li>{__('Typography: Font sizes and styles', 'trvlr')}</li>
                           <li>{__('Cards: Border radius, spacing, and shadows', 'trvlr')}</li>
                        </ul>
                        <Text>
                           {__('All customizations use CSS variables, making it easy to match your site\'s design.', 'trvlr')}
                        </Text>
                     </div>
                  </PanelRow>
               </PanelBody>

               <PanelBody
                  title={__('Advanced: PHP Template Functions', 'trvlr')}
                  initialOpen={false}
               >
                  <PanelRow>
                     <div style={{ width: '100%' }}>
                        <Text>
                           {__('For developers building custom templates, these PHP functions are available:', 'trvlr')}
                        </Text>
                        <Panel>
                           <PanelBody
                              title={__('Display Functions', 'trvlr')}
                              initialOpen={false}
                           >
                              <pre style={{
                                 background: '#f6f7f7',
                                 padding: '12px',
                                 borderRadius: '4px',
                                 fontSize: '13px',
                                 whiteSpace: 'pre-wrap'
                              }}>
                                 {`// Display single attraction card
trvlr_attraction_card($post_id);

// Display multiple attraction cards
trvlr_attraction_cards($args);

// Display attraction gallery
trvlr_attraction_gallery($post_id);

// Display booking calendar
echo trvlr_render_booking_calendar();`}
                              </pre>
                           </PanelBody>
                           <PanelBody
                              title={__('Data Getter Functions', 'trvlr')}
                              initialOpen={false}
                           >
                              <pre style={{
                                 background: '#f6f7f7',
                                 padding: '12px',
                                 borderRadius: '4px',
                                 fontSize: '13px',
                                 whiteSpace: 'pre-wrap'
                              }}>
                                 {`// Get attraction data
get_trvlr_attraction_id($post_id);
get_trvlr_attraction_description($post_id);
get_trvlr_attraction_short_description($post_id);
get_trvlr_attraction_duration($post_id);
get_trvlr_attraction_start_time($post_id);
get_trvlr_attraction_locations($post_id);
get_trvlr_attraction_inclusions($post_id);
get_trvlr_attraction_pricing($post_id);
get_trvlr_attraction_media($post_id);
get_trvlr_attraction_is_on_sale($post_id);`}
                              </pre>
                           </PanelBody>
                           <PanelBody
                              title={__('Example: Custom Loop', 'trvlr')}
                              initialOpen={false}
                           >
                              <pre style={{
                                 background: '#f6f7f7',
                                 padding: '12px',
                                 borderRadius: '4px',
                                 fontSize: '13px',
                                 whiteSpace: 'pre-wrap'
                              }}>
                                 {`<?php
$args = array(
    'post_type' => 'trvlr_attraction',
    'posts_per_page' => 6,
    'orderby' => 'date'
);
$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        trvlr_attraction_card(get_the_ID());
    }
    wp_reset_postdata();
}
?>`}
                              </pre>
                           </PanelBody>
                        </Panel>
                     </div>
                  </PanelRow>
               </PanelBody>
            </Panel>
         </CardBody>
      </Card>
   );
};
