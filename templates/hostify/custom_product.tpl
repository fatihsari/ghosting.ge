{include file="orderforms/standard_cart/common.tpl"}
<script type="text/javascript" src="{$BASE_PATH_JS}/ion.rangeSlider.min.js"></script>
<link href="{$BASE_PATH_CSS}/ion.rangeSlider.css" rel="stylesheet">
<link href="{$BASE_PATH_CSS}/ion.rangeSlider.skinHTML5.css" rel="stylesheet">
<script>
    var ramSliderInstance=null;
    var cpuSliderInstance=null;
    var ramValues = null;
    var osDropdounInstance=null;
    var hddSliderInstance=null;
    var hddValues = null;
    var gpuDropdounInstance=null;
    var gpuSliderInstance=null;
    var gpu_orig_values = null;
    function recalcprice() 
    {
        if (!jQuery("#orderSummaryLoader").is(":visible"))
            jQuery("#orderSummaryLoader").fadeIn('fast');
        var thisRequestId = Math.floor((Math.random() * 1000000) + 1);
        window.lastSliderUpdateRequestId = thisRequestId;
        var post = jQuery.post("{$systemurl}custom_product.php", 'ajax=1&pid={$product.id}&'+jQuery("#custom_frmConfigureProduct").serialize());
        post.done(
            function(data) 
            {
                if (thisRequestId == window.lastSliderUpdateRequestId) {
                    jQuery("#producttotal").html(data);
                }
            }
        );
        post.always(
            function() 
            {
                jQuery("#orderSummaryLoader").delay(500).fadeOut('slow');
            }
        );
    }
    function updateConfigurableOptions(pid, billingCycle) 
    {
        jQuery.post("{$systemurl}custom_product.php", 'a=cyclechange&ajax=1&pid='+pid+'&billingcycle='+billingCycle,
            function(data) {
                jQuery("#_productConfigurableOptions").html(jQuery(data).find('#_productConfigurableOptions').html());
                jQuery('input').iCheck({
                    inheritID: true,
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            }
        );
        recalcprice();
    }
    function trySubmit()
    {
        {if $product.type == "reselleraccount" || $product.type == "other"}
            jQuery("#custom_frmConfigureProduct").submit();
            return true;
        {/if}
        var button = jQuery('#btnCompleteProductConfig');
        var btnOriginalText = jQuery(button).html();
        jQuery(button).find('i').removeClass('fa-arrow-circle-right').addClass('fa-spinner fa-spin');
        var post = jQuery.post("{$systemurl}custom_product.php", 'ajax=1&validate_domain={if $product.type == 'hostingaccount'}true{else}false{/if}&'+jQuery("#custom_frmConfigureProduct").serialize());
        post.done(
            function(data) 
            {
                if (data) {
                    jQuery("#btnCompleteProductConfig").html(btnOriginalText);
                    jQuery("#containerProductValidationErrorsList").html(data);
                    jQuery("#containerProductValidationErrors").removeClass('hidden').show();
                    // scroll to error container if below it
                    if (jQuery(window).scrollTop() > jQuery("#containerProductValidationErrors").offset().top) {
                        jQuery('html, body').scrollTop(jQuery("#containerProductValidationErrors").offset().top - 15);
                    }
                } else {
                    jQuery("#custom_frmConfigureProduct").submit();
                }
            }
        );
    }
    function calcMinMaxMemory(cpu)
	{
		if(cpu <= 2)
			return { min:cpu, max:6.5 * cpu };
		if(cpu == 4)
			return { min:3.75, max:26 };
		return { min:3.75 + (((cpu - 2) - (cpu / 2)) * 1.75), max:26 + (((cpu - 2) - (cpu / 2)) * 13) };
	}
    function setRAMRange()
    {
        if(!cpuSliderInstance)
            return;
        var values = [];
        var min_max = calcMinMaxMemory(parseInt(cpuSliderInstance.val()));
        for(var i=0;i<ramValues.length;i++)
        {
            var i_val = parseInt(ramValues[i]);
            if(i_val >= min_max.min && i_val <= min_max.max)
                values.push(ramValues[i]);
        }
        ramSliderInstance.update( { values:values } );
    }
    function setHDDRange()
    {
        if(!osDropdounInstance)
            return;
        var values = [];
        var min = osDropdounInstance.val() == "1430" ? 10 : 40;
        for(var i=0;i<hddValues.length;i++)
        {
            var i_val = parseInt(hddValues[i]);
            if(i_val >= min)
                values.push(hddValues[i]);
        }
        hddSliderInstance.update( { values:values } );
        recalcprice();
    }
    function setGPURange()
    {
        if(!gpuDropdounInstance)
        { 
            recalcprice();
            return;
        }
        var values = [];
        var gpu_id = gpuDropdounInstance.val();
        var gpu_name = gpuDropdounInstance.children("option:selected").text().trim();
        if(gpu_id == 1432)
        {
            $('#gpu_count').hide();
            gpuSliderInstance.update( { values:values } );       
        }
        else
        {
            for(var i=0;i<gpu_orig_values.length;i++)
            {
                if(gpu_orig_values[i].indexOf(gpu_name) !== -1)
                    values.push(gpu_orig_values[i].replace(' ' + gpu_name, ''));
            }
            $('#gpu_count').show();
           gpuSliderInstance.update( { values:values } );
        }
        recalcprice();
    }
</script>
<form id="custom_frmConfigureProduct" action="/cart.php?a=add&pid={$product.id}&skipconfig=true" method="post">
<div id="order-standard_cart">
<div class="row">
    <div class="col-md-4" id="scrollingPanelContainer">

                        <div id="orderSummary">
                            <div class="order-summary">
                                <div class="loader" id="orderSummaryLoader">
                                    <i class="fa fa-fw fa-refresh fa-spin"></i>
                                </div>
                                <h2>{$product.name}</h2>
                                <div class="summary-container" id="producttotal"></div>
                            </div>
                            <div class="text-center">
                                {if $pricing.type eq "recurring"}
                            <div class="field-container">
                                <div class="form-group">
                                    <label for="inputBillingcycle">{$LANG.cartchoosecycle}</label>
                                    <select name="billingcycle" id="inputBillingcycle" class="form-control select-inline" onchange="updateConfigurableOptions({$product.id}, this.value);">
                                        {if $pricing.monthly}
                                            <option value="monthly"{if $billingcycle eq "monthly"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.monthly|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.monthly}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.quarterly}
                                            <option value="quarterly"{if $billingcycle eq "quarterly"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.quarterly|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.quarterly}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.semiannually}
                                            <option value="semiannually"{if $billingcycle eq "semiannually"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.semiannually|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.semiannually}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.annually}
                                            <option value="annually"{if $billingcycle eq "annually"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.annually|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.annually}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.biennially}
                                            <option value="biennially"{if $billingcycle eq "biennially"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.biennially|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.biennially}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.triennially}
                                            <option value="triennially"{if $billingcycle eq "triennially"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.triennially|regex_replace:"/[0-9.]+/":""|replace:'USD':''|replace:'GEL':''}
                                                {else}
                                                    {$pricing.triennially}
                                                {/if}
                                            </option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                        {/if}
                                <button type="button" id="btnCompleteProductConfig" class="btn btn-primary btn-lg" onclick="trySubmit()">
                                    {$LANG.continue}
                                    <i class="fa fa-arrow-circle-right"></i>
                                </button>
                            </div>
                        </div>

                    </div>
    <div class="col-md-8">
        <div class="alert alert-danger hidden" role="alert" id="containerProductValidationErrors">
                            <p>{$LANG.orderForm.correctErrors}:</p>
                            <ul id="containerProductValidationErrorsList"></ul>
                        </div>
        <div class="form-group"  id="_productConfigurableOptions">
            {foreach $configurableoptions as $num => $configoption}
            {if ($configoption.optionname == translateOptionName('OS') && $product.type != 'hostingaccount') || $configoption.optionname == translateOptionName('DB')}
                                                    <label for="inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                                                    <select name="configoption[{$configoption.id}]" id="inputConfigOption{$configoption.id}" class="form-control" onchange="setHDDRange();">
                                                        {foreach key=num2 item=options from=$configoption.options}
                                                            <option value="{$options.id}"{if $configoption.selectedvalue eq $options.id} selected="selected"{/if}>
                                                                {$options.nameonly}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                    <script>
                        osDropdounInstance=jQuery("#inputConfigOption{$configoption.id}");
                                                    </script>
                                            {elseif $configoption.optiontype eq 3}
                                                <div class="form-group" style="margin-top: 15px; margin-bottom: 25px;">
                                                        <div class="pull-right">
                                                            {$configoption.optionname}
                                                        <input type="checkbox" name="configoption[{$configoption.id}]" id="inputConfigOption{$configoption.id}" value="1" />
                                                       
                                                    </div>
                                            </div>
                                            {else if $configoption.optionname == translateOptionName('GPU') && $product.type != 'hostingaccount'}
                                                    <label for="inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                                                    <select name="configoption[{$configoption.id}]" id="inputConfigOption{$configoption.id}" class="form-control" onchange="setGPURange();">
                                                        {foreach key=num2 item=options from=$configoption.options}
                                                            <option value="{$options.id}"{if $configoption.selectedvalue eq $options.id} selected="selected"{/if}>
                                                                {$options.nameonly}
                                                            </option>
                                                        {/foreach}
                                                    </select>
                                                    <script>
                        gpuDropdounInstance=jQuery("#inputConfigOption{$configoption.id}");
                                                    </script>
                                            {elseif $configoption.optiontype eq 3}
                                                <div class="form-group" style="margin-top: 15px; margin-bottom: 25px;">
                                                        <div class="pull-right">
                                                            {$configoption.optionname}
                                                        <input type="checkbox" name="configoption[{$configoption.id}]" id="inputConfigOption{$configoption.id}" value="1" />
                                                       
                                                    </div>
                                            </div>
                                            {else}
            {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'} <div id="gpu_count" style="display:none"> {/if}
            <label for="_inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                <input type="hidden" name="configoption[{$configoption.id}]" value="{$configoption.options[0].id}" id="inputConfigOption{$configoption.id}" />
                <input type="text" value="{$configoption.options[0].name|regex_replace:"/([^\s]+)\s?.*/":"$1"}" id="_inputConfigOption{$configoption.id}" class="form-control" />
                <script>
                    var sliderTimeoutId{$configoption.id} = null;
                    var values{$configoption.id} = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}"{$options.nameonly}"{/foreach}];
                    {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'} gpu_orig_values = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}"{$options.nameonly}"{/foreach}]; {/if}
                    var ids{$configoption.id} = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}{$options.id}{/foreach}];
                    jQuery("#_inputConfigOption{$configoption.id}").ionRangeSlider({
                        grid: false,
                        grid_snap: false,
                        {if ($configoption.optionname == translateOptionName('RAM') || $configoption.optionname == translateOptionName('GPU Count')) && $product.type != 'hostingaccount'}
                        values: ['0GB','0GB'],
                        {else}
                        values: values{$configoption.id},
                        {/if}
                        onFinish: function(e) {
                            {if $configoption.optionname == translateOptionName('CPU') && $product.type != 'hostingaccount'}
                            setRAMRange();
                            {/if}
                            {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[gpu_orig_values.indexOf(e.from_value + " " + gpuDropdounInstance.children("option:selected").text().trim())]);
                            {else}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[values{$configoption.id}.indexOf(e.from_value)]);
                            {/if}
                            if (sliderTimeoutId{$configoption.id}) {
                                clearTimeout(sliderTimeoutId{$configoption.id});
                            }
                            sliderTimeoutId = setTimeout(function() {
                                sliderTimeoutId{$configoption.id} = null;
                                recalcprice();
                            }, 500);
                        },
                        onUpdate: function(e) {
                            {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[gpu_orig_values.indexOf(e.from_value + " " + gpuDropdounInstance.children("option:selected").text().trim())]);
                            {else}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[values{$configoption.id}.indexOf(e.from_value)]);
                            {/if}
                        }
                    });
                    {if $configoption.optionname == translateOptionName('CPU') && $product.type != 'hostingaccount'}
                        cpuSliderInstance = jQuery("#_inputConfigOption{$configoption.id}");
                    {/if}
                    {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'}
                        gpuSliderInstance = jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        gpuValues = values{$configoption.id};
                    {/if}
                    {if $configoption.optionname == translateOptionName('RAM') && $product.type != 'hostingaccount'}
                        ramSliderInstance = jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        ramValues=values{$configoption.id};
                    {/if}
                    {if $configoption.optionname == translateOptionName('HDD') && $product.type != 'hostingaccount'}
                        hddSliderInstance=jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        hddValues = values{$configoption.id};
                    {/if}
                </script>
                {if $configoption.optionname == translateOptionName('GPU Count') && $product.type != 'hostingaccount'} </div> {/if}
                {/if}
            {/foreach}
        </div>
        {if count($configurableoptions) && $product.showdomainoptions}
                            <div class="field-container">
                                    <div class="form-group">
                                        <label for="domains">{$LANG.domainname}</label>
                                        <input type="text" name="domains[]" id="domains" value="" placeholder="yourdomain.com" class="form-control">
                                    </div>
                            </div>

                        {/if}
        <p>{$product.description}</p>
    </div>
</div>
</div>
</form>
<script>
    jQuery(document).ready(function(){
        jQuery("#_productConfigurableOptions").on('ifChecked', 'input', function() {
        recalcprice();
    });
    jQuery("#_productConfigurableOptions").on('ifUnchecked', 'input', function() {
        recalcprice();
    });
    jQuery("#_productConfigurableOptions").on('change', 'select', function() {
        recalcprice();
    });
    });
    setRAMRange();
    setGPURange();
</script>