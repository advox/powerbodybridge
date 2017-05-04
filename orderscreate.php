<?php
require_once 'app/Mage.php';
Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('admin');
Mage::register('isSecureArea', 1);
$rootPath = Mage::getBaseDir();
set_time_limit(0);
$sync = new Powerbody_Bridge_Model_Cron();
$sync->createDropshippingOrders();
exit;
