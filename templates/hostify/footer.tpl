{if $loginpage eq 0 and $templatefile ne "clientregister"}
                        </div><!-- /.main-content -->
                {if !$inShoppingCart && $secondarySidebar->hasChildren()}
                    <div class="col-md-3 pull-md-left sidebar">
                        {include file="$template/includes/sidebar.tpl" sidebar=$secondarySidebar}
                    </div>
                {/if}
            
            <div class="clearfix"></div>
        </div>
    </div>
</section>
</div>
<div id="footer" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-6">
                <div class="footer-menu-holder">
                    <h4>{$LANG.ghosting.text.about}</h4>
                    <p>{$LANG.ghosting.text.about_desc}</p>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="address-holder">
                    <div class="phone"><i class="fa fa-phone"></i> +995 511159012</div>
                    <div class="phone"><i class="fa fa-envelope"></i> support@ghosting.ge</div>
                    <div class="phone"><i class="fa fa-map-marker"></i> {$LANG.ghosting.text.company_address}</div>
                    <!--question-circle-->
                    <div class="phone" style="margin-top: 35px;"><i class="fa fa-question-circle"></i> <a href="{$seo_faq_root}" style="color:#FFF; text-decoration:underline">{$LANG.navsupport}</a></div>
                    <div class="phone"><i class="fa fa-legal"></i> <a href="{$seo_terms_and_conditions_url}" style="color:#FFF; text-decoration:underline">{$LANG.ghosting.text.terms}</a></div>
                    <div class="phone"><i class="fa fa-lock"></i> <a href="{$seo_privacy_policy_url}" style="color:#FFF; text-decoration:underline">{$LANG.ghosting.text.policy}</a></div>
                </div>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-3">
                <div class="footer-menu-holder">
                    <h4>{$LANG.ghosting.nav.nav}</h4>
                    <ul class="footer-menu">
                        {if !$loggedin }
                            {include file="$template/includes/navbar.tpl" navbar=$primaryNavbar}
                        {else}
                            {include file="$template/includes/customnavbar.tpl" navbar=$secondaryNavbar}
                        {/if}
                    </ul>
                </div>
            </div>
        </div>
                        <!-- TOP.GE ASYNC COUNTER CODE -->
            <div id="top-ge-counter-container" data-site-id="114946"></div>
            <script async src="//counter.top.ge/counter.js"></script>
            <!-- / END OF TOP.GE COUNTER CODE -->

    </div>
</div>

<div class="modal system-modal fade" id="modalAjax" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content panel panel-primary">
            <div class="modal-header panel-heading">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">Title</h4>
            </div>
            <div class="modal-body panel-body">
                Loading...
            </div>
            <div class="modal-footer panel-footer">
                <div class="pull-left loader">
                    <i class="fa fa-circle-o-notch fa-spin"></i> Loading...
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary modal-submit">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
{/if}
{if $templatefile eq "clientregister"}{/if}
<script src="{$WEB_ROOT}/templates/{$template}/js/bootstrap-slider.min.js"></script>
<script src="{$WEB_ROOT}/templates/{$template}/js/slick.min.js"></script>
<script src="{$WEB_ROOT}/templates/{$template}/js/main.min.js"></script>
{$footeroutput}

</body>
</html>