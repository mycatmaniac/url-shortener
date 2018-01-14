Shortener
========================

Welcome to url shortener!
This application short your long links

Features
--------------

Application can:

  * Short your link;
  * Provides an opportunity to create your desired links;
  * Redirect to origin url when you go to short url;
  * Count the number of amount of visits;
  * Create links using api;
  * Get json data of link;
  * View data of link.

API
-------------

To create a link via api, you need to send the get request <code>/api/create/</code> with the parameters "originUrl" and "shortUrl" ("shortUrl" optional). You will receive a json response for further use.

To get the link data, you need to go to <code>api/get/{short_link} where {short_link}</code> is your shortcut. Get a json.

To view the data in a convenient format, go to <code>api/{short_link}/view</code>


Enjoy!
