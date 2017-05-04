# powerbodybridge

Min requirements: PHP 5.6, magento 1.8.1

Launching the API
In the main folder we have uploaded the cron test files. They need adding to crone job in order to make the API work.

Test files (should add to cron):
orderscreate.php - creating order (recommended every 5 minutes)
ordersupdate.php - order status update (recommended every 5 minutes)
prices.php - stock levels + prices (every 15-30 minutes)
product.php - product data (twice a day)

Configuration:

System -> Configuration -> Bridge
You need to enter your webservice login credentials (Web service settings - product create, Dropshipping - order create).

Dropshipping settings -> You need to select the statuses for the following:
- "Available order statuses to powerbody create order" - sending orders with the marked statuses to powerbody system
- "Active order statuses to powerbody update order" - requesting updates from powerbody for orders with the following statuses

System -> Configuration -> SCP
SCP Plugin allows to display products as simple products at the checkout (instead of configurable products). This plugin might need adjusting with custom Magento templates

Bridge
Bridge -> Import requested
You need to pick the brands (or categories) you want to import to your system and execute product.php file (important: this file contains retail prices, the prices will need updating with prices.php)

Manufacturers
Catalog->Manage Manufacturers->Manufacturers
This is where a Margin can be set (for the entire brand).

Catalog->Manage Products
If you want to edit prices, product descriptions or other product details you have to do that on the product page and click No on Is Updated While Import * field. This will disable ALL future updates for the product - we will only refresh prices if the basic price changes.
