Attractions API Documentation
1. Overview
The Attractions API endpoint retrieves detailed information about booking items from a centralized database. This RESTful endpoint returns JSON-formatted data with comprehensive attraction details.
2. Base URL

To fetch Multiple attractions list:
curl --location --request POST 'https://lc84mznen7.execute-api.ap-southeast-2.amazonaws.com/production/process/webapi_handler/attractions' \--header 'Content-Type: application/json' \--header 'accept: /' \--header 'origin: https://your-frontend-app.com' \--header 'referer: https://your-frontend-app.com/' \--header 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
To fetch  single attraction:

curl --location 'https://sl.portal.trvlr.ai/api/process/webapi_handler/attraction_with_id' \
--header 'Content-Type: application/json' \
--header 'accept: /' \
--header 'origin: https://your-frontend-app.com' \
--header 'referer: https://your-frontend-app.com/' \
--header 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36' \
--data '{"attraction_id":5220}'

3. Request Method
POST
4. Authentication
[Specify if authentication is required]
5. Response Structure
The API returns a JSON object with the following top-level structure:
{
  "results": [...],
  "count": 197,
  "error": "",
  "filters": {},
  "columns": [],
  "response_status": "success"
}
6. Response Fields
6.1 Top-Level Fields
Field	Type	Description
results	array	Array of attraction/booking item objects.
count	integer	Total number of results returned.
error	string	Error message if request fails (empty string if no error).
filters	object	Filters applied to the results (if any).
columns	array	List of available columns/fields that can be returned or displayed.
response_status	string	Status of the response. "success" or "error".

6.2 Attraction Object Structure
Each attraction in the results array contains:
{
  "pk": 4485,
  "parent_attraction_ids": null,
  "id": 4485,
  "created_at": "Jun 05 2025, 00:25:34",
  "modified_at": "Jul 16 2025, 09:00:39",
  "is_deleted": false,
  "is_active": true,
  "extra_data": "{}",
  "address": null,
  "attraction_type": [],
  "cost_by_attraction": null,
  "cost_by_days": null,
  "currency": "AUD",
  "description": "Explore the oldest rainforest...",
  "destination_id": null,
  "distance_from_city_center": null,
  "equipment": [],
  "location": "{\"address\": \"\", \"location\": {\"coordinates\": [-16.4746141, 145.3507164]}}",
  "organisation_id": 26,
  "product_type": "Attraction",
  "recommended_time": "{}",
  "seasonal": false,
  "status_id": 11,
  "title": "Mossman Gorge Daintree Experience Day Tour",
  "validity": null,
  "contact_email": "",
  "contact_number": "",
  "have_different_address": false,
  "information_url": "",
  "stay_time": "0-9-0",
  "images": "https://img.rezdy.com/PRODUCT_IMAGE/233629/Crocodile_on_the_banks_daintree_river.jpg",
  "end_address": "{\"address\": \"\", \"location\": {\"coordinates\": []}}",
  "is_parent": false,
  "parent_attraction_id": null,
  "booking_url": "",
  "created_by_id": 2,
  "is_featured": false,
  "wix_image": "",
  "extra_details": "",
  "sourceproduct_id": 7329
}
6.3 Attraction Object Fields
Field	Type	Description
pk	integer	Primary key identifier of the attraction.
parent_attraction_ids	object	IDs of parent attractions if applicable.
id	integer	Unique identifier of the attraction.
created_at	string	Timestamp of creation (e.g., 'Jun 05 2025, 00:25:34').
modified_at	string	Timestamp of last modification.
is_deleted	boolean	Indicates whether the attraction is deleted.
is_active	boolean	Indicates whether the attraction is active.
extra_data	object	Additional metadata in JSON string format.
address	object	Address of the attraction (if available).
attraction_type	array	Categories/tags for the attraction.
cost_by_attraction	number/null	Cost if defined per attraction.
cost_by_days	number/null	Cost if defined per day.
currency	string	Currency code (e.g., 'AUD').
description	string	Detailed description of the attraction.
destination_id	integer/	Related destination identifier.
distance_from_city_center	number/null	Distance from city center in km.
equipment	array	List of required/provided equipment.
location	object/string	Geographic coordinates & address in JSON format.
organisation_id	integer	Organisation/tenant identifier.
product_type	string	Always 'Attraction'.
recommended_time	object/string	Suggested visiting time (JSON).
seasonal	boolean	Indicates if the attraction is seasonal.
status_id	integer	Status reference ID.
title	string	Name/title of the attraction.
validity	string/null	Validity period if applicable.
contact_email	string	Contact email address.
contact_number	string	Contact phone number.
have_different_address	boolean	Indicates if a separate address exists.
information_url	string	External link for more information.
stay_time	string	Recommended stay duration (HH-MM-SS format).
images	string	URL to featured image.
end_address	object/string	End address & coordinates in JSON format.
is_parent	boolean	Whether this is a parent attraction.
parent_attraction_id	integer/null	Parent attraction ID if applicable.
booking_url	string	Booking link (if available).
created_by_id	integer	ID of the user who created the entry.
is_featured	boolean	Indicates if attraction is featured.
wix_image	string	Wix-hosted image URL (if any).
extra_details	string	Additional textual details.
sourceproduct_id	integer	Source product reference ID.
7. Nested Objects
7.1 Location Object
{
  "address": "",
  "location": {
    "coordinates": [-16.4746141, 145.3507164]
  }
}
7.2 End Address Object
{
  "address": "",
  "location": {
    "coordinates": []
  }
}
8. Status Codes
Code	Description
200	Success – Attraction data returned successfully.
400	Bad Request – Invalid parameters provided.
401	Unauthorized – Authentication required.
404	Not Found – Attraction doesn’t exist.
500	Internal Server Error – Something went wrong.
9. Pagination
The API supports pagination through the following fields:
- count → Total number of items available.
- results → Contains the current page of results.
- filters → Indicates which filters were applied (if any).
- columns → Can be used to define the structure of paginated responses.
10. CURL Example
curl 'https://lc84mznen7.execute-api.ap-southeast-2.amazonaws.com/production/process/webapi_handler/attractions'   -H 'accept: */*'   -H 'content-type: application/json'   -H 'origin: https://your-frontend-app.com'   -H 'referer: https://your-frontend-app.com/'   -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'   --data-raw '{"organisation": 40, "attraction_id": <ATTRACTION_ID>}'
