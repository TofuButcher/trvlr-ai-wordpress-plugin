<div class="trvlr-instructions">
   <h2>Setup Instructions</h2>

   <h3>1. Configure Base Domain</h3>
   <p>Enter your trvlr.ai base domain in the settings above (e.g., <code>https://yourdomain.trvlr.ai</code>). This is required for the booking system to function.</p>

   <h3>2. Disable Frontend Elements</h3>
   <p>The "Disable Frontend Elements" checkbox controls whether the plugin loads its booking modals and JavaScript. Check this if you want to build custom integrations.</p>

   <h3>3. Configure Tour Post Types (Optional)</h3>
   <p>If you have custom post types for tours or activities, enter their slugs (comma-separated) in the Tour Post Types field. This will add an "Attraction ID" field to those post types.</p>
   <p><strong>Example:</strong> <code>tour, experience, activity</code></p>

   <h3>4. Create Payment Confirmation Page</h3>
   <p>Use the "Create Payment Confirmation Page" button above to automatically create a page at <code>/payments</code> with the payment confirmation shortcode.</p>

   <h3>5. Adding Book Now Buttons</h3>
   <p>To add a booking button to any page, add these attributes to a button or link:</p>
   <ul class="trvlr-list">
      <li><strong>Class:</strong> <code>book-now</code></li>
      <li><strong>Attribute:</strong> <code>attraction-id="YOUR_ATTRACTION_ID"</code></li>
   </ul>
   <p><strong>Example:</strong></p>
   <pre class="trvlr-code">&lt;button class="book-now" attraction-id="123"&gt;Book Now&lt;/button&gt;</pre>

   <h3>6. Available Shortcodes</h3>

   <h4>Payment Confirmation</h4>
   <pre class="trvlr-code-simple">[trvlr_payment_confirmation]</pre>
   <p>Displays the payment confirmation page. Use this on your payment confirmation page (automatically included if you use the button above).</p>

   <h4>Booking Calendar</h4>
   <pre class="trvlr-code-simple">[trvlr_booking_calendar]</pre>
   <p>If inserted on a post/page with an Attraction ID field, the calendar will automatically use that ID. You can also specify an ID manually.</p>
   <p><strong>Parameters:</strong></p>
   <ul class="trvlr-list">
      <li><code>attraction_id</code> (optional): Your attraction ID from trvlr.ai. If not provided, uses the Attraction ID field from the current post.</li>
      <li><code>width</code> (optional): Calendar width (default: 450px)</li>
      <li><code>height</code> (optional): Calendar height (default: 600px)</li>
   </ul>
   <p><strong>Example with custom attraction ID and size:</strong></p>
   <pre class="trvlr-code-simple">[trvlr_booking_calendar attraction_id="123" width="500px" height="700px"]</pre>

   <h3>7. Automatic Attraction ID Detection</h3>
   <p>If you've configured tour post types with attraction ID fields, the <code>[trvlr_booking_calendar]</code> shortcode will automatically detect and use the attraction ID from the current post. No need to manually specify the <code>attraction_id</code> attribute!</p>
   <p><strong>Simple usage on tour posts:</strong></p>
   <pre class="trvlr-code-simple">[trvlr_booking_calendar]</pre>
   <p><strong>Using in PHP templates:</strong></p>
   <pre class="trvlr-code">&lt;?php
$attraction_id = trvlr_get_attraction_id(get_the_ID());
if ($attraction_id) {
echo do_shortcode('[trvlr_booking_calendar]');
}
?&gt;</pre>
</div>