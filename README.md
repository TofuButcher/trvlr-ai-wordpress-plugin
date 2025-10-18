# Trvlr Booking System Plugin

A generalized WordPress plugin for integrating the trvlr.ai booking system into any WordPress site.

## Installation

1. Upload the `trvlr` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Trvlr Settings' in the WordPress admin menu
4. Enter your trvlr base domain (e.g., `https://yourdomain.trvlr.ai`)
5. Save the settings

## Configuration

### Admin Settings

Navigate to **Trvlr Settings** in the WordPress admin menu to configure:

- **Base Domain**: Enter your trvlr subdomain (e.g., `https://yourdomain.trvlr.ai`)

## Usage

### Shortcodes

#### Payment Confirmation Page

Use this shortcode to display the payment confirmation page:

```
[trvlr_payment_confirmation]
```

#### Booking Calendar

Display a booking calendar for a specific attraction:

```
[trvlr_booking_calendar attraction_id="YOUR_ATTRACTION_ID"]
```

Optional parameters:
- `width` - Calendar width (default: "450px")
- `height` - Calendar height (default: "600px")

Example:
```
[trvlr_booking_calendar attraction_id="123" width="500px" height="700px"]
```

### Book Now Buttons

Add a "Book Now" button to any element by adding the following attributes:

```html
<button class="book-now" attraction-id="YOUR_ATTRACTION_ID">Book Now</button>
```

The button must have:
- Class: `book-now`
- Attribute: `attraction-id` with your attraction ID value

## Features

- Modal-based booking flow
- Floating cart button
- Payment confirmation page
- Booking calendar integration
- Automatic cart synchronization
- Responsive design

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Version

1.0.0

