<?php 
echo "<div class=\"pull-right\">\n    <div class=\"panel panel-default panel-market-account";
echo ($account["linked"] ? " account-linked" : "");
echo "\" id=\"panelAccount\">\n        <div class=\"panel-heading\">\n            <small><a href=\"#\" class=\"pull-right account-refresh\" style=\"margin-top:2px;\">Refresh</a></small>\n            <strong>Your Account</strong>\n        </div>\n        <div class=\"panel-body text-center account-linked\">\n            <div class=\"text-center\" style=\"margin-bottom:1px;\">\n                Your Balance:\n                <span class=\"points-balance\">\n                    <span class=\"points-container";
if( !$account["balance"] || $balanceNeedsUpdate ) 
{
    echo " hidden";
}

echo "\">\n                        <span class=\"balance\">";
echo $account["balance"]["balance"];
echo "</span> Points\n                    </span>\n                    <span class=\"retrieving-container";
if( !$balanceNeedsUpdate ) 
{
    echo " hidden";
}

echo "\">\n                        <span style=\"color:#aaa;font-weight:300;\"><i class=\"fa fa-spinner fa-spin\"></i> Retrieving...</span>\n                    </span>\n                </span>\n            </div>\n            <div class=\"info-line\">Last Updated: <span class=\"balance-last-updated\">";
echo $balanceLastUpdated;
echo "</span></div>\n            <div class=\"linked-to info-line\">Linked to <u class=\"account-email\">";
echo $account["email"];
echo "</u></div>\n            <div class=\"connection-error\">Unable to communicate with Marketplace.</div>\n            <div class=\"auth-error\">Could not authenticate. <a href=\"#\" id=\"btnResolveAuthError\">Resolve now</a></div>\n            <form method=\"post\" action=\"\" target=\"_blank\">\n                <input type=\"hidden\" name=\"action\" value=\"sso\">\n                <button type=\"submit\" name=\"destination\" value=\"deposit\" class=\"btn btn-default btn-sm\">Deposit Funds</button>\n                <button type=\"button\" class=\"btn btn-default btn-sm\" id=\"btnDisconnect\">Disconnect</button>\n            </form>\n        </div>\n        <div class=\"panel-body text-center account-not-linked\">\n            <button type=\"button\" data-toggle=\"modal\" data-target=\"#loginModal\" class=\"btn btn-default btn-sm btn-login-create-account\">Login/Create Account</button>\n        </div>\n    </div>\n</div>\n\n<img src=\"../assets/img/marketconnect/logo.png\" class=\"marketconnect-logo\" id=\"marketconnectLogo\">\n<br><br>\n<p class=\"lead\">Connecting you with Value Added Services, Upsells and Additional Revenue Streams...</p>\n\n<div class=\"clearfix\"></div>\n\n<div class=\"modal modal-marketconnect-login fade\" tabindex=\"-1\" role=\"dialog\" id=\"loginModal\">\n  <div class=\"modal-dialog\" role=\"document\">\n    <div class=\"modal-content\">\n      <div class=\"modal-header\">\n        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n        <h4 class=\"modal-title\" id=\"myModalLabel\">Login/Create Account</h4>\n      </div>\n      <div class=\"modal-body\">\n        <div class=\"modal-mc-logo\">\n            <img src=\"../assets/img/marketconnect/logo.png\" class=\"marketconnect-logo\" id=\"marketconnectLogo\">\n        </div>\n        <div id=\"loginFormContainer\">\n        <p class=\"login-msg\">You can login using either your <a href=\"https://marketplace.whmcs.com/\" target=\"_blank\">WHMCS Marketplace</a> or <a href=\"https://www.whmcs.com/members/\" target=\"_blank\">WHMCS Members Area</a> login details.</p>\n        <div class=\"alert alert-danger hidden\" id=\"alertLoginFailed\"></div>\n        <form class=\"form-horizontal\" id=\"frmLogin\">\n          <div class=\"form-group\">\n            <label for=\"inputLoginEmail\" class=\"col-sm-3 control-label\">Email</label>\n            <div class=\"col-sm-8\">\n              <input type=\"email\" name=\"email\" class=\"form-control\" id=\"inputLoginEmail\" placeholder=\"Email\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <label for=\"inputLoginPassword\" class=\"col-sm-3 control-label\">Password</label>\n            <div class=\"col-sm-8\">\n              <input type=\"password\" name=\"password\" class=\"form-control\" id=\"inputLoginPassword\" placeholder=\"Password\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <div class=\"col-sm-12 text-center\">\n              <label class=\"checkbox-inline\">\n                <input type=\"checkbox\" name=\"agreetos\" value=\"1\">\n                I agree to the <a href=\"https://marketplace.whmcs.com/policies/terms\" target=\"_blank\">WHMCS Marketplace Terms of Service</a>\n              </label>\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <div class=\"col-sm-12 text-center\">\n              <button type=\"submit\" class=\"btn btn-default\">Login</button>\n            </div>\n          </div>\n        </form>\n        <p class=\"create-acct-link\">Not registered? <a href=\"#\" id=\"btnShowRegisterForm\">Create a free WHMCS Marketplace account</a></p>\n        </div>\n        <div id=\"registerFormContainer\" class=\"hidden\">\n        <p class=\"text-center\">Create a WHMCS Marketplace account and get started instantly.</p>\n        <div class=\"alert alert-danger hidden\" id=\"alertRegisterFailed\"></div>\n        <form class=\"form-horizontal\" id=\"frmRegister\">\n          <div class=\"form-group\">\n            <label for=\"inputRegisterFirstName\" class=\"col-sm-4 control-label\">First Name</label>\n            <div class=\"col-sm-7\">\n              <input type=\"text\" name=\"firstname\" class=\"form-control\" id=\"inputRegisterFirstName\" placeholder=\"First Name\" value=\"";
echo $registerInfo["admin"]->firstName;
echo "\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <label for=\"inputRegisterLastName\" class=\"col-sm-4 control-label\">Last Name</label>\n            <div class=\"col-sm-7\">\n              <input type=\"text\" name=\"lastname\" class=\"form-control\" id=\"inputRegisterLastName\" placeholder=\"Last Name\" value=\"";
echo $registerInfo["admin"]->lastName;
echo "\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <label for=\"inputRegisterCompanyName\" class=\"col-sm-4 control-label\">Company Name</label>\n            <div class=\"col-sm-7\">\n              <input type=\"text\" name=\"companyname\" class=\"form-control\" id=\"inputRegisterCompanyName\" placeholder=\"Company Name\" value=\"";
echo $registerInfo["companyName"];
echo "\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <label for=\"inputRegisterEmail\" class=\"col-sm-4 control-label\">Email</label>\n            <div class=\"col-sm-7\">\n              <input type=\"email\" name=\"email\" class=\"form-control\" id=\"inputRegisterEmail\" placeholder=\"Email\" value=\"";
echo $registerInfo["admin"]->email;
echo "\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <label for=\"inputPassword3\" class=\"col-sm-4 control-label\">Password</label>\n            <div class=\"col-sm-7\">\n              <input type=\"password\" name=\"password\" class=\"form-control\" id=\"inputPassword3\" placeholder=\"Password\">\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <div class=\"col-sm-12 text-center\">\n              <label class=\"checkbox-inline\">\n                <input type=\"checkbox\" name=\"agreetos\" value=\"1\">\n                I agree to the <a href=\"https://marketplace.whmcs.com/policies/terms\" target=\"_blank\">WHMCS Marketplace Terms of Service</a>\n              </label>\n            </div>\n          </div>\n          <div class=\"form-group\">\n            <div class=\"col-sm-12 text-center\">\n              <button type=\"submit\" class=\"btn btn-default\">Create Account</button>\n            </div>\n          </div>\n        </form>\n        <br>\n        <p>Already registered? <a href=\"#\" id=\"btnShowLoginForm\">Login here</a></p>\n        </div>\n      </div>\n    </div>\n  </div>\n</div>\n\n<script>\n\$(document).ready(function() {\n    \$('body').addClass('market-connect');\n    \$('.account-refresh').click(function(e) {\n        e.preventDefault();\n        \$('.points-container').hide();\n        \$('.retrieving-container').removeClass('hidden').show();\n        \$.post('', 'action=getBalance&token=' + csrfToken, function(data) {\n            if (data.error) {\n                \$('.linked-to').hide();\n                \$('.connection-error').show();\n            } else if (data.authError) {\n                \$('.linked-to').hide();\n                \$('.auth-error').show();\n            } else {\n                \$('.points-container .balance').html(data.balance);\n                \$('.balance-last-updated').html(data.updatedDiff);\n            }\n            \$('.retrieving-container').hide();\n            \$('.points-container').removeClass('hidden').show();\n        }, 'json');\n    });\n    ";
if( $balanceNeedsUpdate ) 
{
    echo "        \$('.account-refresh').click();\n    ";
}

echo "    \$('#frmLogin').submit(function(e) {\n        e.preventDefault();\n        \$('#frmLogin button[type=\"submit\"]').prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> Login');\n        \$.post('', 'action=link&token=' + csrfToken + '&' + \$(this).serialize(), function(data) {\n            if (data.error) {\n                \$('#alertLoginFailed').html(data.error).hide().removeClass('hidden').fadeIn();\n            } else {\n                swal(\"Success!\", \"You have successfully linked your WHMCS installation to the WHMCS Marketplace!\", \"success\");\n                \$('#loginModal').modal('hide');\n                \$('.account-email').html(data.email);\n                \$('.points-container .balance').html(data.balance);\n                \$('.balance-last-updated').html(data.updatedDiff);\n                \$('.retrieving-container, .auth-error, .connection-error').hide();\n                \$('.linked-to').show();\n                \$('.retrieving-container').hide();\n                \$('.points-container').removeClass('hidden').show();\n                \$('.panel-market-account').addClass('account-linked');\n            }\n            \$('#frmLogin button[type=\"submit\"]').removeProp('disabled').html('Login');\n        });\n    });\n    \$('#frmRegister').submit(function(e) {\n        e.preventDefault();\n        \$('#frmRegister button[type=\"submit\"]').prop('disabled', true).html('<i class=\"fa fa-spinner fa-spin\"></i> Register');\n        \$.post('', 'action=register&token=' + csrfToken + '&' + \$(this).serialize(), function(data) {\n            if (data.error) {\n                \$('#alertRegisterFailed').html(data.error).hide().removeClass('hidden').fadeIn();\n            } else {\n                swal(\"Success!\", \"Your account has been created! Please check your email to verify your account.\", \"success\");\n                \$('#loginModal').modal('hide');\n                \$('.account-email').html(data.email);\n                \$('.points-container .balance').html(data.balance);\n                \$('.balance-last-updated').html(data.updatedDiff);\n                \$('.retrieving-container, .auth-error, .connection-error').hide();\n                \$('.linked-to').show();\n                \$('.points-container').removeClass('hidden').show();\n                \$('.panel-market-account').addClass('account-linked');\n            }\n            \$('#frmRegister button[type=\"submit\"]').removeProp('disabled').html('Register');\n        });\n    });\n    \$('#btnDisconnect').click(function(e) {\n        e.preventDefault();\n        swal({\n          title: \"Are you sure?\",\n          text: \"Disconnecting your WHMCS installation from the current WHMCS Marketplace account will mean you can no longer manage any services provisioned while using this account until it is re-connected.\",\n          type: \"warning\",\n          showCancelButton: true,\n          confirmButtonColor: \"#DD6B55\",\n          confirmButtonText: \"Yes, disconnect my account\",\n          closeOnConfirm: false\n        },\n        function(){\n          swal(\"Disconnected\", \"Your WHMCS installation has now been disconnected.\", \"success\");\n          \$.post('', 'action=disconnect&token=' + csrfToken);\n          \$('.panel-market-account').removeClass('account-linked');\n        });\n    });\n    \$('#btnShowRegisterForm').click(function(e) {\n        e.preventDefault();\n        \$('#loginFormContainer').hide();\n        \$('#registerFormContainer').hide().removeClass('hidden').fadeIn();\n    });\n    \$('#btnShowLoginForm').click(function(e) {\n        e.preventDefault();\n        \$('#registerFormContainer').hide();\n        \$('#loginFormContainer').hide().removeClass('hidden').fadeIn();\n    });\n    \$('#btnResolveAuthError').click(function(e) {\n        e.preventDefault();\n        \$('#registerFormContainer').hide();\n        \$('#loginFormContainer').removeClass('hidden').show();\n        \$('#loginModal').modal('show');\n    });\n\n    \$('body').on('click', '.modal-mc-service .btn-login', function(e) {\n        e.preventDefault();\n        \$('#registerFormContainer').hide();\n        \$('#loginFormContainer').removeClass('hidden').show();\n        \$('#loginModal').modal('show');\n        \$('#modalAjax').modal('hide');\n    });\n\n    \$('body').on('click', '.modal-mc-service .btn-register', function(e) {\n        e.preventDefault();\n        \$('#registerFormContainer').removeClass('hidden').show();\n        \$('#loginFormContainer').hide();\n        \$('#loginModal').modal('show');\n        \$('#modalAjax').modal('hide');\n    });\n\n    \$('body').on('click', '.modal-mc-service .do-advanced-setup-mode', function(e) {\n        e.preventDefault();\n        \$('#activate-simple').hide();\n        \$('#activate-advanced').removeClass('hidden').show();\n    });\n\n    \$('body').on('click', '.modal-mc-service .do-simple-setup-mode', function(e) {\n        e.preventDefault();\n        \$('#activate-advanced').hide();\n        \$('#activate-simple').removeClass('hidden').show();\n    });\n\n    \$('body').on('click', '.modal-mc-service .toggle-pricing, .modal-mc-service .btn-manage-pricing', function(e) {\n        e.preventDefault();\n        var product = \$(this).closest('.product');\n        var pricing = product.find('.pricing');\n        \$(this).parent('div').parent('div')\n            .find('input.product-status')\n            .parent('div').parent('div')\n            .tooltip('hide')\n            .end();\n        if (pricing.is(':visible')) {\n            pricing.slideUp();\n            product.find('.toggle-pricing i').removeClass('fa-chevron-down').addClass('fa-chevron-right');\n        } else {\n            pricing.hide().removeClass('hidden').slideDown();\n            product.find('.toggle-pricing i').addClass('fa-chevron-down').removeClass('fa-chevron-right');\n        }\n    });\n\n    \$('body').on('click', '.modal-mc-service .btn-save-pricing', function(e) {\n        e.preventDefault();\n        var btn = \$(this);\n        btn.parents('li').find('input.product-status').parent('div').parent('div').tooltip('hide').end();\n        btn.html('<i class=\"fa fa-spinner fa-spin\"></i> Saving...');\n        \$.post('', 'action=setPricing&token=' + csrfToken + '&' + \$(this).closest('div.pricing').find('form').serialize(), function(data) {\n            if (data.error) {\n                swal(\"Uh oh!\", data.error, \"error\");\n            }\n            btn.html('Save Changes');\n        }, 'json');\n    });\n\n    \$('body').on('click', '.modal-mc-service .btn-restore-default-pricing', function(e) {\n        e.preventDefault();\n        \$(this).closest('div.pricing').find('.price-field').each(function(){\n            \$(this).val(\$(this).data('default-price'));\n        });\n    });\n\n    \$('body').on('click', '.modal-mc-service .btn-sso-service', function(e) {\n        e.preventDefault();\n        var btn = \$(this);\n        var originalBtnLabel = btn.html();\n        \$(this).html('<i class=\"fa fa-spinner fa-spin\"></i> Connecting to service...');\n        \$.post('', 'action=ssoForService&service=' + \$(this).data('service') + '&token=' + csrfToken, function(data) {\n            if (data.error) {\n                swal(\"Sign-In Failed\", data.error, \"error\");\n            } else {\n                window.open(data.redirectUrl);\n            }\n            btn.html(originalBtnLabel);\n        }, 'json');\n    })\n        .on('click', '.modal-mc-service .btn-sso-client-service', function(e) {\n            e.preventDefault();\n            var btn = \$(this),\n                originalBtnLabel = btn.html();\n            btn.html('<i class=\"fa fa-spinner fa-spin\"></i>')\n                .addClass('disabled')\n                .prop('disabled', 'disabled');\n            \$.post(\n                '',\n                'action=ssoForServiceId&serviceId=' + \$('#serviceId')[0].selectize.getValue() + '&token=' + csrfToken,\n                function(data) {\n                    if (data.error) {\n                        swal(\"Sign-In Failed\", data.error, \"error\");\n                    } else {\n                        window.open(data.redirectUrl);\n                    }\n                    btn.html(originalBtnLabel).removeProp('disabled').removeClass('disabled');\n                },\n                'json'\n            );\n        });\n\n    \$('body').on('switchChange.bootstrapSwitch', '.modal-mc-service .product-status', function(event, state) {\n        if (!\$(this).closest('ul').hasClass('activation')) {\n            var productKey = \$(this).data('productkey');\n            if (state) {\n                \$.post('', 'action=enableProduct&productkey=' + productKey + '&token=' + csrfToken);\n            } else {\n                \$.post('', 'action=disableProduct&productkey=' + productKey + '&token=' + csrfToken);\n            }\n        }\n    });\n\n    \$('body').on('switchChange.bootstrapSwitch', '.modal-mc-service .promo-switch', function(event, state) {\n        if (\$(this).closest('div.tab-pane').prop('id') == 'settings') {\n            var promo = \$(this).data('promo');\n            var service = \$(this).data('service');\n            \$.post('', 'action=setPromoStatus&promo=' + promo + '&service=' + service + '&state=' + state + '&token=' + csrfToken\n            );\n        }\n    });\n\n    \$('body').on('switchChange.bootstrapSwitch', '.modal-mc-service .setting-switch', function(event, state) {\n        if (\$(this).closest('div.tab-pane').prop('id') == 'other') {\n            var name = \$(this).data('name');\n            var service = \$(this).data('service');\n            \$.post('', 'action=setGeneralSetting&name=' + name + '&service=' + service + '&state=' + state + '&token=' + csrfToken\n            );\n        }\n    });\n\n    \$('body').on('click', '.modal-mc-service .advanced-mode .steps a:not(\".mode-label\")', function(e) {\n        \$('.modal-mc-service .advanced-mode .steps a').removeClass('active');\n        \$(this).addClass('active');\n    });\n\n    \$('body').on('shown.bs.tab', '.modal-mc-service .advanced-mode .steps a[data-toggle=\"tab\"]', function(e) {\n        if (\$(e.target).attr('href') == '#activate-advanced-finish') {\n            \$('.modal-mc-service .advanced-mode .btn-next').fadeOut();\n        } else {\n            \$('.modal-mc-service .advanced-mode .btn-next').fadeIn();\n        }\n    });\n\n    \$('body').on('click', '.modal-mc-service .advanced-mode .btn-next', function(e) {\n        var currentTab = \$('.modal-mc-service .advanced-mode .steps a.active').attr('href');\n        if (currentTab == '#activate-advanced-products') {\n            \$('.modal-mc-service .advanced-mode .steps a[href=\"#activate-advanced-promos\"]').click();\n        } else {\n            \$('.modal-mc-service .advanced-mode .steps a[href=\"#activate-advanced-finish\"]').click();\n        }\n    });\n\n    \$('body').on('click', '.btn-mc-service-control,.btn-login-create-account', function(e) {\n        if (tour) {\n            tour.end();\n        }\n    })\n        .on('change', 'input:checkbox[name^=\"enabled[\"]', function() {\n            var productKey = \$(this).data('product-key'),\n                checked = \$('input:checkbox:checked[data-product-key=\"' + productKey + '\"]');\n            if (checked.length === 0) {\n                \$(this).prop('checked', 'checked');\n                \$('input.product-status[data-productkey=\"' + productKey + '\"')\n                    .parent('div').parent('div')\n                    .data('toggle', 'tooltip')\n                    .data('trigger', 'manual')\n                    .data('placement', 'bottom auto')\n                    .attr(\n                        'title',\n                        'At least one term must be enabled. Slide this toggle to remove the product from sale.'\n                    )\n                    .tooltip('show');\n            } else {\n                \$('input.product-status[data-productkey=\"' + productKey + '\"')\n                    .parent('div').parent('div')\n                    .tooltip('hide');\n            }\n        })\n        .on('switchChange.bootstrapSwitch', 'input.product-status', function(event, state) {\n            \$(this).parent('div').parent('div').tooltip('hide');\n        });\n});\n</script>\n";

