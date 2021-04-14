<div id="form-section" class="container-fluid signin">
    <div class="website-logo">
        <a href="{$WEB_ROOT}/index.php" target="_self" class="header_logo" title="{$companyname}">gHosting</a>
    </div>
    <div class="row">
        <div class="info-slider-holder" style="padding: 140px 0px 0px;">
            <div class="bg-animation"></div>
            <div id="top-content" class="container-fluid" style="margin:0px">
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
        </div>
        <div class="form-holder">
            <div class="menu-holder">
                <ul class="main-links">
                    <li><a class="normal-link" href="{$WEB_ROOT}/register.php">{$LANG.ghosting.text.not_registered}</a></li>
                    <li><a class="sign-button" href="{$WEB_ROOT}/register.php">{$LANG.ghosting.text.register}</a></li>
                </ul>
            </div>
            <div class="signin-signup-form">
                <div class="form-items{if $linkableProviders} with-social{/if}">
                    <div class="form-title">{include file="$template/includes/pageheader.tpl" title=$LANG.login desc="{$LANG.restrictedpage}"}</div>
                    {if $incorrect}
                        {include file="$template/includes/alert.tpl" type="error" msg=$LANG.loginincorrect textcenter=true}
                    {elseif $verificationId && empty($transientDataName)}
                        {include file="$template/includes/alert.tpl" type="error" msg=$LANG.verificationKeyExpired textcenter=true}
                    {elseif $ssoredirect}
                        {include file="$template/includes/alert.tpl" type="info" msg=$LANG.sso.redirectafterlogin textcenter=true}
                    {/if}
                    <div class="providerLinkingFeedback"></div>

                    <div class="row">
                        <div class="col-sm-12">
                            <form id="signinform" method="post" action="{$systemurl}dologin.php" role="form">
                                <div class="form-text">
                                    <input id="inputEmail" type="email" name="username" name="username" placeholder="{$LANG.enteremail}">
                                </div>
                                <div class="form-text">
                                    <input id="inputPassword" type="password" name="password" placeholder="{$LANG.clientareapassword}" autocomplete="off">
                                </div>
                                <div class="form-text text-holder">
                                    <input id="chkbox" type="checkbox" class="hno-checkbox" name="rememberme" /> <label for="chkbox">{$LANG.loginrememberme}</label>
                                </div>
                                <div class="form-button">
                                    <button id="login" type="submit" class="ybtn ybtn-accent-color">{$LANG.loginbutton}</button>
                                    <a href="pwreset.php" class="btn btn-link">{$LANG.forgotpw}</a>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-12">
                            {include file="$template/includes/linkedaccounts.tpl" linkContext="login" customFeedback=true}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>