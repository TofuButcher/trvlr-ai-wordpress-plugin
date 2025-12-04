Post type: Attraction
Slug: attraction

## Native Fields

Title
Slug
Featured Image
Categories


# Attraction Specific Fields

*attraction_id*: number

*attraction_description*: rich text

*attraction_short_description*: rich text

*attraction_pricing*: Repeater field
- *attraction_price_type*: Text / Multiple Select
- *attraction_price*: number
- *attraction_sale_price*: number

*attraction_is_on_sale*: Boolean
*attraction_sale_description*: Text

*attraction_media* ( images, videos )

*attraction_duration*: Text

*attraction_start_time*: Time Picker
*attraction_end_time*: Time Picker

*attraction_location*: Repeater Field
- *attraction_location_type*: Text / Multiple Select
- *attraction_location_address*: Text
- *attraction_location_latitude*: Text
- *attraction_location_longitude*: Text

*attraction_additional_info*: Rich text

*attraction_inclusions*: Rich Text

*attraction_highlights*: Rich Text

# Low Urgency Fields to Consider Later

*attraction_tags*: taxonomy
- tags from trvlr api.

*attraction_availability*: text

*attraction_policies*: rich text