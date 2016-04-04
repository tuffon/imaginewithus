<style>
.section{
    margin-left: -20px;
    margin-right: -20px;
    font-family: "Raleway",san-serif;
}
.section h1{
    text-align: center;
    text-transform: uppercase;
    color: #808a97;
    font-size: 35px;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    width: 100%;
    margin: 50px 0 0;
}
.section ul{
    list-style-type: disc;
    padding-left: 15px;
}
.section:nth-child(even){
    background-color: #fff;
}
.section:nth-child(odd){
    background-color: #f1f1f1;
}
.section .section-title img{
    display: table-cell;
    vertical-align: middle;
    width: auto;
    margin-right: 15px;
}
.section h2,
.section h3 {
    display: inline-block;
    vertical-align: middle;
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    color: #808a97;
    text-transform: uppercase;
}

.section .section-title h2{
    display: table-cell;
    vertical-align: middle;
    line-height: 25px;
}

.section-title{
    display: table;
}

.section h3 {
    font-size: 14px;
    line-height: 28px;
    margin-bottom: 0;
    display: block;
}

.section p{
    font-size: 13px;
    margin: 25px 0;
}
.section ul li{
    margin-bottom: 4px;
}
.landing-container{
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    padding: 50px 0 30px;
}
.landing-container:after{
    display: block;
    clear: both;
    content: '';
}
.landing-container .col-1,
.landing-container .col-2{
    float: left;
    box-sizing: border-box;
    padding: 0 15px;
}
.landing-container .col-1 img{
    width: 100%;
}
.landing-container .col-1{
    width: 55%;
}
.landing-container .col-2{
    width: 45%;
}
.premium-cta{
    background-color: #808a97;
    color: #fff;
    border-radius: 6px;
    padding: 20px 15px;
}
.premium-cta:after{
    content: '';
    display: block;
    clear: both;
}
.premium-cta p{
    margin: 7px 0;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YWCNP_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
    border-color: #ff643f;
    box-shadow: none;
    outline: none;
    color: #fff;
    position: relative;
    padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
    color: #fff;
    background: url(<?php echo YWCNP_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
    border-color: #971d00;
    box-shadow: none;
    outline: none;
}
.premium-cta a.button:focus{
    top: 1px;
}
.premium-cta a.button span{
    line-height: 13px;
}
.premium-cta a.button .highlight{
    display: block;
    font-size: 20px;
    font-weight: 700;
    line-height: 20px;
}
.premium-cta .highlight{
    text-transform: uppercase;
    background: none;
    font-weight: 800;
    color: #fff;
}

.section.one{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/02-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.three{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/04-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.five{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YWCNP_ASSETS_URL ?>/images/06-bg.png) no-repeat #fff; background-position: 85% 75%
}



@media (max-width: 768px) {
    .section{margin: 0}
    .premium-cta p{
        width: 100%;
    }
    .premium-cta{
        text-align: center;
    }
    .premium-cta a.button{
        float: none;
    }
}

@media (max-width: 480px){
    .wrap{
        margin-right: 0;
    }
    .section{
        margin: 0;
    }
    .landing-container .col-1,
    .landing-container .col-2{
        width: 100%;
        padding: 0 15px;
    }
    .section-odd .col-1 {
        float: left;
        margin-right: -100%;
    }
    .section-odd .col-2 {
        float: right;
        margin-top: 65%;
    }
}

@media (max-width: 320px){
    .premium-cta a.button{
        padding: 9px 20px 9px 70px;
    }

    .section .section-title img{
        display: none;
    }
}
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Name Your Price%2$s to benefit from all features!','yith-woocommerce-name-your-price'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-name-your-price');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-name-your-price');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-name-your-price');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/01.png" alt="Feature 01" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL ?>/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('For all products of the shop ','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('You will be free to offer an open price for many more products. With the premium version of the plugin, you can offer this option on %1$sgrouped products%2$s and for each %1$ssingle variation%2$s of the products of the shop.', 'yith-woocommerce-name-your-price'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL ?>/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e('Suggested price','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Many studies about marketing strategies prove that %1$susers often choose a price similar to the one suggested by the vendor%2$s, even if they are free to choose. %3$sIf you think this suits you, you will just have to suggest a price for a product, and it will be automatically showed in the detail page.', 'yith-woocommerce-name-your-price'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/02.png" alt="feature 02" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/03.png" alt="Feature 03" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL ?>/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Minimum and maximum price','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Prevent users to purchase products with any amount or even freely. Set a %1$sminimum%2$s and a %1$smaximum price%2$s users have to follow to add products to cart, avoiding unpleasant inconveniences.', 'yith-woocommerce-name-your-price'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL ?>/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('Product categories','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Thanks to the bulk action the plugin offers, you can configure easily the rules about open prices for all products that belong to the same category, %1$swithout acting on every single product%2$s.', 'yith-woocommerce-name-your-price'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/04.png" alt="Feature 04" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/05.png" alt="Feature 05" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL?>/images/05-icon.png" alt="icon 05" />
                    <h2><?php _e('Customized labels','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'You can customize all %1$slabels%2$s and %1$serror messages%2$s showed by the plugin. In this way, you will be able to add the text you want for your users.','yith-woocommerce-name-your-price' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YWCNP_ASSETS_URL ?>/images/06-icon.png" alt="icon 06" />
                    <h2><?php _e('Compatibility with YITH plugins','yith-woocommerce-name-your-price');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Integrate perfectly the features of the plugin with those of %1$sYITYH WooCommerce Multi Vendor%2$s. Allows your users to set the open price for the products of their shops.','yith-woocommerce-name-your-price'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YWCNP_ASSETS_URL ?>/images/06.png" alt="Feature 06" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Name Your Price%2$s to benefit from all features!','yith-woocommerce-name-your-price'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-name-your-price');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-name-your-price');?></span>
                </a>
            </div>
        </div>
    </div>
</div>