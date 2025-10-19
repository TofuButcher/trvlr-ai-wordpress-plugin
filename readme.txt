=== Trvlr AI Booking System ===
Contributors: pariswelch
Tags: booking, reservations, tours, trvlr, booking system
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 0.0.3
Requires PHP: 7.0
License: MIT
License URI: https://opensource.org/licenses/MIT

WordPress plugin for integrating the trvlr.ai booking system into any WordPress site.

== Description ==

Trvlr AI Booking System provides a seamless integration with the trvlr.ai booking platform, allowing you to add booking capabilities to your WordPress site with minimal setup.

= Features =

* Modal-based booking flow
* Floating cart button
* Payment confirmation page
* Booking calendar integration
* Automatic cart synchronization
* Responsive design
* Easy configuration through admin settings
* Disable frontend elements

= Usage =

After installation and configuration:

1. Configure your trvlr.ai base domain in the plugin settings
2. Add booking buttons to your pages with `class="book-now"` and `attraction-id="YOUR_ID"` attributes
3. Use shortcodes for payment confirmation and booking calendars
4. Customize which post types display attraction ID fields

== Installation ==

1. Upload the `trvlr` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Trvlr Settings' in the WordPress admin menu
4. Enter your trvlr base domain (e.g., `https://yourdomain.trvlr.ai`)
5. Save the settings

== Frequently Asked Questions ==

= How do I get a trvlr.ai domain? =

Contact trvlr.ai to set up your booking system domain.

= Can I disable the plugin's frontend elements? =

Yes, there is a "Disable Frontend Elements" checkbox in the settings that allows you to disable the booking modals and JavaScript while keeping the plugin active for custom implementations.

= What shortcodes are available? =

* `[trvlr_payment_confirmation]` - Displays the payment confirmation page
* `[trvlr_booking_calendar]` - Displays a booking calendar. Automatically uses the Attraction ID field from the current post, or you can specify one with `attraction_id="123"`

= How do I add a booking calendar? =

Simply add `[trvlr_booking_calendar]` to any post or page. If the post has an Attraction ID field (configured via Tour Post Types setting), it will automatically use that ID.

You can also manually specify an attraction ID: `[trvlr_booking_calendar attraction_id="123"]`

= How do I add book now buttons? =

Add the following attributes to any button or element:
* Class: `book-now`
* Attribute: `attraction-id` with your attraction ID value

Example: `<button class="book-now" attraction-id="123">Book Now</button>`

== Changelog ==

= 0.0.3 =
* Changed "Enable Frontend" to "Disable Frontend Elements" checkbox with inverted logic (defaults to false/unchecked)
* Booking calendar shortcode now automatically detects attraction ID from current post - no need to specify attraction_id attribute
* Added tour post types configuration to register attraction_id fields
* Added automatic payment confirmation page creation button
* Improved admin UI with custom fonts and styling
* Added comprehensive setup instructions in admin settings
* Refactored JavaScript to class-based architecture for better maintainability
* Created proper meta field registration system supporting both ACF and native WordPress custom fields
* Updated all documentation to reflect automatic attraction ID detection

= 0.0.2 =
* Added frontend enable/disable control in admin settings
* Conditional loading of JS/CSS based on configuration
* Improved admin settings interface
* Booking modals and scripts now respect enable setting

= 0.0.1 =
* Initial release
* Basic booking system integration
* Modal-based booking flow
* Payment confirmation page
* Booking calendar shortcode
* Admin configuration panel

== Upgrade Notice ==

= 0.0.3 =
Major improvements to admin interface and booking calendar shortcode. The booking calendar now automatically detects attraction IDs from posts, making setup much easier.

= 0.0.2 =
This version adds the ability to disable frontend elements, useful for custom implementations or during development.

