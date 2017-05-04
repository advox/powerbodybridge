<?php

$blockArray = [
    'header_block' => [
        'title'   => 'Header Block',
        'content' => '
            <div class="index-container">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="index_block">
                                <div class="banners_row">
                                    <div class="banner_col ban1">
                                        <div class="banner_img"><a href="#"> <img alt="" src="{{skin_url=\'images/banner1.jpg\'}}" /> </a></div>
                                        <div class="banner_sm">
                                            <a href="#">
                                                <h2>Recovery Formulas</h2>
                                                <div class="img_sm"><img alt="" src="{{skin_url=\'images/banner_sm1.jpg\'}}" /></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="banner_col ban2">
                                        <div class="banner_sm">
                                            <a href="#">
                                                <h2>Muscle Formulas</h2>
                                                <div class="img_sm"><img alt="" src="{{skin_url=\'images/banner_sm2.jpg\'}}" /></div>
                                            </a>
                                        </div>
                                        <div class="banner_img"><a href="#"> <img alt="" src="{{skin_url=\'images/banner2.jpg\'}}" /> </a></div>
                                    </div>
                                    <div class="banner_col ban3">
                                        <div class="banner_img"><a href="#"> <img alt="" src="{{skin_url=\'images/banner3.jpg\'}}" /> </a></div>
                                        <div class="banner_sm">
                                            <a href="#">
                                                <h2>Endurance Athletes</h2>
                                                <div class="img_sm"><img alt="" src="{{skin_url=\'images/banner_sm3.jpg\'}}" /></div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="banner_col ban4">
                                        <div class="banner_sm">
                                            <a href="#">
                                                <h2>Weight Loss</h2>
                                                <div class="img_sm"><img alt="" src="{{skin_url=\'images/banner_sm4.jpg\'}}" /></div>
                                            </a>
                                        </div>
                                        <div class="banner_img"><a href="#"> <img alt="" src="{{skin_url=\'images/banner4.jpg\'}}" /> </a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear">&nbsp;</div>
                </div>
            </div>',
    ],
    'banner_wide'  => [
        'title'   => 'Banner Wide',
        'content' => '
            <div class="banner_wide">
                <div class="banner_wide_wrap"><a href="#"> <img alt="" src="{{skin_url=\'images/banner_wide.jpg\'}}" /> </a></div>
            </div>',
    ],
];

foreach ($blockArray as $identifier => $data) {
    $blockModel = Mage::getModel('cms/block')->load($identifier, 'identifier');
    /* @var $blockModel Mage_Cms_Model_Block */
    $blockModel->addData([
        'title'      => $data['title'],
        'identifier' => $identifier,
        'stores'     => 0,
        'is_active'  => 1,
        'content'    => $data['content'],
    ]);
    $blockModel->save();
}

$pageModel = Mage::getModel('cms/page')->load('home', 'identifier');
/* @var $pageModel Mage_Cms_Model_Page */
$pageModel->addData([
    'root_template'     => 'one_column',
    'content'           => '<div class="clear">&nbsp;</div>',
    'layout_update_xml' => '
        <reference name="content">
            <block type="catalog/product_new" name="home.catalog.product.new" alias="product_new" template="catalog/product/new.phtml" after="cms_page">
                <action method="setColumnCount"><count>4</count></action>
                <action method="addPriceBlockType">
                    <type>bundle</type>
                    <block>bundle/catalog_product_price</block>
                    <template>bundle/catalog/product/price.phtml</template>
                </action>
            </block>
            <block type="reports/product_viewed" name="home.reports.product.viewed" alias="product_viewed" template="reports/home_product_viewed.phtml" after="product_new">
                <action method="setColumnCount"><count>4</count></action>
                <action method="addPriceBlockType">
                    <type>bundle</type>
                    <block>bundle/catalog_product_price</block>
                    <template>bundle/catalog/product/price.phtml</template>
                </action>
            </block>
            <block type="reports/product_compared" name="home.reports.product.compared" template="reports/home_product_compared.phtml" after="product_viewed">
                <action method="setColumnCount"><count>4</count></action>
                <action method="addPriceBlockType">
                    <type>bundle</type>
                    <block>bundle/catalog_product_price</block>
                    <template>bundle/catalog/product/price.phtml</template>
                </action>
            </block>
        </reference>
        <reference name="right">
            <action method="unsetChild"><alias>right.reports.product.viewed</alias></action>
            <action method="unsetChild"><alias>right.reports.product.compared</alias></action>
        </reference>',
]);
$pageModel->save();
