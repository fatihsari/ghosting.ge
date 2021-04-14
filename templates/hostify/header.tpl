<!DOCTYPE html>
<html lang="{if $language=='georgian'}ka{elseif $language=='russian'}ru{else}en{/if}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{if $seotitle eq ""}{if $kbarticle.title}{$kbarticle.title} - {/if}{$pagetitle} - {$companyname}{else}{$seotitle}{/if}</title>
    <meta name="keywords" content="{$seokeyword}">
    <meta name="description" content="{$seodecription}">
    <meta property="og:url" content="{$fburl}" />
    <meta property="og:type" content="{$fbtype}" />
    <meta property="og:title" content="{$fbtitle}" />
    <meta property="og:description" content="{$fbdesc}" />
    <meta property="og:image" content="{$fbimage}" />
    {include file="$template/includes/head.tpl"}
    {$headoutput}
</head>
<body data-phone-cc-input="{$phoneNumberInputStyle}" {if $loginpage eq 1 or $templatefile eq "clientregister"}class="fullpage"{/if}>
{if $loginpage eq 0 and $templatefile ne "clientregister"}

<div id="header-holder" class="{if $templatefile != 'homepage'}inner-header{/if}">
    <div class="bg-animation"></div>
    
    {$headeroutput}

    <section id="header" class="container-fluid" style="min-height:60px">
        <div class="container">
            <ul class="top-nav">
                {if $languagechangeenabled && count($locales) > 1}
                    <li>
                        <a href="#" class="choose-language" data-toggle="popover" id="languageChooser">
                            {if $activeLocale.localisedName == 'Georgian'}ქართული{else}
                                            {$activeLocale.localisedName}
                                            {/if}
                            <b class="caret"></b>
                        </a>
                        <div id="languageChooserContent" class="hidden">
                            <ul>
                                {foreach $locales as $locale}
                                    <li>
                                        <a href="{$currentpagelinkback}language={$locale.language}">
                                            {if $locale.localisedName == 'Georgian'}ქართული{else}
                                            {$locale.localisedName}
                                            {/if}
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    </li>
                {/if}
                {if $loggedin}
                    <li>
                        <a href="#" data-toggle="popover" id="accountNotifications" data-placement="bottom">
                            {$LANG.notifications}
                            {if count($clientAlerts) > 0}<span class="label label-info">NEW</span>{/if}
                            <b class="caret"></b>
                        </a>
                        <div id="accountNotificationsContent" class="hidden">
                            <ul class="client-alerts">
                            {foreach $clientAlerts as $alert}
                                <li>
                                    <a href="{$alert->getLink()}">
                                        <i class="fa fa-fw fa-{if $alert->getSeverity() == 'danger'}exclamation-circle{elseif $alert->getSeverity() == 'warning'}warning{elseif $alert->getSeverity() == 'info'}info-circle{else}check-circle{/if}"></i>
                                        <div class="message">{$alert->getMessage()}</div>
                                    </a>
                                </li>
                            {foreachelse}
                                <li class="none">
                                    {$LANG.notificationsnone}
                                </li>
                            {/foreach}
                            </ul>
                        </div>
                    </li>
                {/if}
                {include file="$template/includes/navbar.tpl" x_navbar=$secondaryNavbar}
                <li class="primary-action">
                        <a href="{$WEB_ROOT}/cart.php?a=view" class="btn">
                            {$LANG.viewcart}
                        </a>
                    </li>
                {if $adminMasqueradingAsClient || $adminLoggedIn}
                    <li>
                        <a href="{$WEB_ROOT}/logout.php?returntoadmin=1" class="btn btn-logged-in-admin" data-toggle="tooltip" data-placement="bottom" title="{if $adminMasqueradingAsClient}{$LANG.adminmasqueradingasclient} {$LANG.logoutandreturntoadminarea}{else}{$LANG.adminloggedin} {$LANG.returntoadminarea}{/if}">
                            <i class="fa fa-sign-out"></i>
                        </a>
                    </li>
                {/if}
            </ul>
            <a href="{$WEB_ROOT}/index.php" target="_self" class="header_logo" title="{$companyname}">gHosting</a>
        </div>
    </section>
    <section id="main-menu">
        <nav id="nav" class="container-fluid navbar navbar-default navbar-main" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#primary-nav">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="primary-nav">

                    <ul class="nav navbar-nav">

                        {include file="$template/includes/navbar.tpl" navbar=$primaryNavbar}

                    </ul>

                </div><!-- /.navbar-collapse -->
            </div>
        </nav>
    </section>
    {if $templatefile == 'homepage'}
    <div id="top-content" class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="main-slider">
                        <div class="slide info-slide1">
                            <div class="icon-holder">
                                <div class="icon-bg"></div>
                                <i class="fa fa-google" style="margin-top: 8px;"></i>
                            </div>
                            <div class="big-title">{$LANG.ghosting.slider.1}</div>
                        </div>
                        <div class="slide info-slide2">
                            <div class="icon-holder">
                                <div class="icon-bg"></div>
                                <i class="htfy htfy-trophy"></i>
                            </div>
                            <div class="big-title">{$LANG.ghosting.slider.2}</div>
                        </div>
                        <div class="slide info-slide3">
                            <div class="icon-holder">
                                <div class="icon-bg"></div>
                                <i class="htfy htfy-speedometer"></i>
                            </div>
                            <div class="big-title">{$LANG.ghosting.slider.3}</div>
                        </div>
                        <div class="slide info-slide4">
                            <div class="icon-holder">
                                <div class="icon-bg"></div>
                                <i class="htfy htfy-padlock"></i>
                            </div>
                            <div class="big-title">{$LANG.ghosting.slider.4}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="arrow-button-holder">
                        <a href="#services" onclick="event.preventDefault(); $('html, body').animate({ scrollTop: $('#services').offset().top }, 800);">
                            <div class="button-text">{$LANG.ghosting.text.choose_services}</div>
                            <div class="arrow-icon">
                                <i class="htfy htfy-arrow-down"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
</div>
{if $templatefile == 'homepage'}
<div id="services" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row-title">{$LANG.ghosting.text.our_services}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img width="137" height="125" src="{$WEB_ROOT}/templates/{$template}/images/service-icon1.png" alt="">
                    </div>
                    <div class="service-title"><a href="{$seo_web_hosting_url}">{$LANG.ghosting.text.web_hosting}</a></div>
                    <div class="service-details">
                        <p>{$LANG.ghosting.text.web_hosting_desc}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img width="137" height="125" src="{$WEB_ROOT}/templates/{$template}/images/service-icon2.png" alt="">
                    </div>
                    <div class="service-title"><a href="{$seo_vps_servers_url}">{$LANG.ghosting.text.vps_hosting}</a></div>
                    <div class="service-details">
                        <p>{$LANG.ghosting.text.vps_hosting_desc}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img width="137" height="125" src="{$WEB_ROOT}/templates/{$template}/images/service-icon3.png" alt="">
                    </div>
                    <div class="service-title"><a href="{$seo_databases_url}">{$LANG.ghosting.text.db}</a></div>
                    <div class="service-details">
                        <p>{$LANG.ghosting.text.db_desc}</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div class="service-box">
                    <div class="service-icon">
                        <img width="137" height="125" src="{$WEB_ROOT}/templates/{$template}/images/service-icon4.png" alt="">
                    </div>
                    <div class="service-title"><a href="{$seo_profesional_service_url}">{$LANG.ghosting.text.pro_hosting}</a></div>
                    <div class="service-details">
                        <p>{$LANG.ghosting.text.pro_hosting_desc}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="features" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="row-title">{$LANG.ghosting.advantages.0}</div>
            </div>
        </div>
        <div class="row rtl-cols">
            <div class="col-sm-12 col-md-6">
                <div id="features-links-holder">
                    <div class="icons-axis">
                        <img width="98" height="80" src="{$WEB_ROOT}/templates/{$template}/images/features-icon.png" alt="">
                    </div>
                    <div class="feature-icon-holder feature-icon-holder1 opened" data-id="1">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-worldwide"></i></div>
                        <div class="feature-title">{$LANG.ghosting.advantages.1}</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder2" data-id="2">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-cogwheel"></i></div>
                        <div class="feature-title">{$LANG.ghosting.advantages.2}</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder3" data-id="3">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-speedometer"></i></div>
                        <div class="feature-title">{$LANG.ghosting.advantages.3}</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder4" data-id="4">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-padlock"></i></div>
                        <div class="feature-title">{$LANG.ghosting.advantages.4}</div>
                    </div>
                    <div class="feature-icon-holder feature-icon-holder5" data-id="5">
                        <div class="animation-holder"><div class="special-gradiant"></div></div>
                        <div class="feature-icon"><i class="htfy htfy-like"></i></div>
                        <div class="feature-title">{$LANG.ghosting.advantages.5}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6">
                <div id="features-holder">
                    <div class="feature-box feature-d1 show-details">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-worldwide"></i></span>
                            <span class="feature-title">{$LANG.ghosting.advantages.1}</span>
                        </div>
                        <div class="feature-details">
                            <p>
                                {$LANG.ghosting.advantages.1_desc}
                            </p>

                        </div>
                    </div>
                    <div class="feature-box feature-d2">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-cogwheel"></i></span>
                            <span class="feature-title">{$LANG.ghosting.advantages.2}</span>
                        </div>
                        <div class="feature-details">
                            <p>{$LANG.ghosting.advantages.2_desc}</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d3">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-speedometer"></i></span>
                            <span class="feature-title">{$LANG.ghosting.advantages.3}</span>
                        </div>
                        <div class="feature-details">
                            <p>{$LANG.ghosting.advantages.3_desc}</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d4">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-padlock"></i></span>
                            <span class="feature-title">{$LANG.ghosting.advantages.4}</span>
                        </div>
                        <div class="feature-details">
                             <p>{$LANG.ghosting.advantages.4_desc}</p>
                        </div>
                    </div>
                    <div class="feature-box feature-d5">
                        <div class="feature-title-holder">
                            <span class="feature-icon"><i class="htfy htfy-like"></i></span>
                            <span class="feature-title">{$LANG.ghosting.advantages.5}</span>
                        </div>
                        <div class="feature-details">
                            <p>{$LANG.ghosting.advantages.5_desc}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
{/if}
{include file="$template/includes/verifyemail.tpl"}
<div id="main-body-holder" class="container-fluid">
<section id="main-body">
    <div class="container{if $skipMainBodyContainer}-fluid without-padding{/if}">
        <div class="row">

        {if !$inShoppingCart && ($primarySidebar->hasChildren() || $secondarySidebar->hasChildren())}
            {if $primarySidebar->hasChildren() && !$skipMainBodyContainer}
                <div class="col-md-9 pull-md-right">
                    {include file="$template/includes/pageheader.tpl" title=$displayTitle desc=$tagline showbreadcrumb=true}
                </div>
            {/if}
            <div class="col-md-3 pull-md-left sidebar">
                {include file="$template/includes/sidebar.tpl" sidebar=$primarySidebar}
            </div>
        {/if}
        <!-- Container for main page display content -->
        <div class="{if !$inShoppingCart && ($primarySidebar->hasChildren() || $secondarySidebar->hasChildren())}col-md-9 pull-md-right{else}col-xs-12{/if} main-content">
            {if !$primarySidebar->hasChildren() && !$showingLoginPage && !$inShoppingCart && $templatefile != 'homepage' && !$skipMainBodyContainer}
                {include file="$template/includes/pageheader.tpl" title=$displayTitle desc=$tagline showbreadcrumb=true}
            {/if}
{/if}