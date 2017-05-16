# powerbodybridge

Min requirements: PHP 5.6, magento 1.8.1

Launching the API<br>
In the "test" folder we have uploaded the files of scripts that should be later removed. Use these files only for test purpose. 
Configuration of Cron in magento for these actions is present in app/code/local/Powerbody/Bridge/etc/config.xml but make sure, that cron is running in magento system. 

Test files:<br> 
orderscreate.php - creating order (recommended every 5 minutes)<br> 
ordersupdate.php - order status update (recommended every 15 minutes)<br> 
prices.php - stock levels + prices (every 15-30 minutes)<br> 
product.php - product data (twice a day)<br> 

# bridge
<b>System -> Configuration -> Bridge</b><br>
- Basic settings<br>
Select "Yes" to enable. Additional tab "Bridge" in the main menu should appear.
- Web Service Settings<br> 
Enter your webservice login credentials (to obtain credentials -> Contact Us).
Default WSDL Url: http://www.powerbody.co.uk/index.php/api/soap/?wsdl
- Dropshipping Settings<br>
Enter webservice credentials for order synchronization.
Select the statuses for the following:<br>
"Available order statuses to powerbody create order" - sending orders with the marked statuses to powerbody system<br>
"Active order statuses to powerbody update order" - requesting updates from powerbody for orders with the following statuses
- Ingredients<br>
In order to download ingredients image for product, watermark image must be uploaded to server. To upload image, visit System->Configuration->Bridge settings->Ingredients->Watermark image. <b>Warning:</b> watermark must be added to all store views. Once a day, a cron job will download changed or new ingredients images. To test: execute script under test/additional.php

<b>Bridge -> Import requested</b> (main menu)<br>
Pick the brands and categories for import. Then wait for cron or execute main product import action (test -> products.php file) (important: this action add's products with retail prices, the prices will be updated for individual customer after cron price action is executed (test -> prices.php))

#scp
<b>System -> Configuration -> SCP</b><br>
SCP Plugin allows to display products as simple at the checkout (instead of configurable products). 
This plugin might need to be adjusted if custom magento store template is applied.

#manufacturers
<b>Catalog -> Manage Manufacturers -> Manufacturers</b><br>
Place where margin can be set (for the entire brand).

To preserve description, name changes etc., visit product page and change product attribute "Is Updated While Import" to "No".
