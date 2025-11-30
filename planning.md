# High Level Overview
This wordpress plugin is for integrating the TRVLR AI booking system, a business to business solution for booking companies to easily import tours and experiences to sell through their site while using the trvlr system for payments and a source of truth for all the tour data. The aim of this is to be a drop in integration that just requires installing the plugin, connecting an API key and identifier for your tour business in the trvlr system, then syncing all the tours that you have enabled in the trvlr backend. This will populate a custom post type which you can then edit details of individual tours and the plugin will track changes to update fields that have not been changed manually. These tours will then be displayed on the wordpress site, using either the built in default templates, or since all the data for them resides within standard wordpress posts a developer can build whatever frontend they want to display the data. There will be an admin page for controlling the connection to the TRVLR AI system, syncing between trvlr and wordpress ( and logs of syncs, errors, etc.), some theme customization that will control the style of the built in UI elements, along with detailed instructions setup and some documentation about development features for

## Current State
This plugin started as a custom built integration for the trvlr booking system for a site that already had all the tours on the website. So the beginning of this was just to embed all the required modals with the trvlr iframes for bookings. Along with some shortcodes to embed iframe booking calendars and some logic for listening to clicks on booking buttons with attraction_id attributes

The final API has not been setup yet so there will be changes to authentication and available endpoints and data returned.
Currently, the API was only set up for a single business and doesn't have any authentication. I have made a request to both currently available endpoints and saved the result in files for easy access while testing.
The two endpoints are:
- get a list of all the tours for that business in the trvlr system.
- get more detailed data for a single tour

As the API and auth will be changing the development needs to be done with what's currently available but with the intent of changing the connection method and specific data returned.

## Connection to TRVLR AI system
- API connection ( After trvlr api fully setup it will probably some identifier for the business and an api key )
- Request Logging and retry logic

## Custom data within 
- Custom post type "attractions" ( I often refer to as tours but there may be multiple types of experiences sold through the trvlr system that may not suit the tour name so attraction is a more general term for the post type )
- Retained native features for attractions post type ( tags, categories, custom taxonomies, custom fields etc.)

## Syncing
- Data transformation layer that will map API data to the fields of an "attraction"
- Handling of new tours, updates, and deletions.
- Smart differential syncing that updates only what needs to be with awareness of what's been changed manually within wordpress.
- Fields like images that are only returned a url from the API should download and add the images in wordpress media library so image sizes can be generated while avoiding downloading multiple copies of the same image ( from the same url ).
- Some fields from the api that should be treated as taxonomies need to be processed as such.


## Admin pages / controls
- Toggles that are on by default but disable default frontend elements so users can build there own.
- Manual buttons to sync all tours from TVLR system.
- Toggles that are on by default but disable default frontend elements so users can build there own.
- Syncing button for sync all now + settings for settings up scheduled syncs ( hourly or daily).
- Manual sync screen should have progress indicator and show logs for ongoing sync.
- Button on editing page for individual tours to sync now. This will sync latest data for just that tour while keeping in mind manually changed data within wordpress.
- Basic theme customization options that effect the style of UI elements that come with the plugin.

## Frontend UI
Prebuilt page templates for the following:
- Payment confirmation page
- Single tour template
- Each main element on single tour template available through shortcodes & php functions for composability and customization for developers.
- Tour cards shortcode that displays list of premade cards. All tours -> or main query if available -> Or query arguments passed to shortcode