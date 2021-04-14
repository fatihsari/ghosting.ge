{include file="orderforms/standard_cart/common.tpl"}
<script type="text/javascript" src="{$BASE_PATH_JS}/ion.rangeSlider.min.js"></script>
                                                            <link href="{$BASE_PATH_CSS}/ion.rangeSlider.css" rel="stylesheet">
                                                            <link href="{$BASE_PATH_CSS}/ion.rangeSlider.skinHTML5.css" rel="stylesheet">
<script>
var _localLang = {
    'addToCart': '{$LANG.orderForm.addToCart|escape}',
    'addedToCartRemove': '{$LANG.orderForm.addedToCartRemove|escape}'
}
var ramSliderInstance=null;
    var cpuSliderInstance=null;
    var ramValues = null;
    var ramSelectedValue = null;
    var osDropdounInstance=null;
    var hddSliderInstance=null;
    var hddValues = null;
    var hddSelectedValue=null;
    var gpuDropdounInstance=null;
    var gpuSliderInstance=null;
    var gpu_orig_values = null;
    var gpuSelectedValue=null;
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
        ramSliderInstance.update( { values:values, from:values.indexOf(ramSelectedValue) } );
    }
    function setHDDRange(recalc = true)
    {
        if(!osDropdounInstance)
            return;
        var values = [];
        var min = osDropdounInstance.val() == "1430" ? 10 : 40;
        console.log(min);
        for(var i=0;i<hddValues.length;i++)
        {
            var i_val = parseInt(hddValues[i]);
            if(i_val >= min)
                values.push(hddValues[i]);
        }
        hddSliderInstance.update( { values:values, from:values.indexOf(hddSelectedValue) } );
        if(recalc)
            recalctotals();
    }
    function setGPURange(recalc = true)
    {
        if(!gpuDropdounInstance)
        {
            if(recalc)
                recalctotals();
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
            var selectedIndex = 0;
            for(var i=0;i<gpu_orig_values.length;i++)
            {
                if(gpu_orig_values[i].indexOf(gpu_name) !== -1)
                {
                    if(gpu_orig_values[i].indexOf(gpuSelectedValue) !== -1)
                        selectedIndex = values.length;
                    values.push(gpu_orig_values[i].replace(' ' + gpu_name, ''));
                }
            }
            $('#gpu_count').show();
           gpuSliderInstance.update( { values:values, from:selectedIndex } );
        }
        if(recalc)
            recalctotals();
    }
</script>

            <form id="frmConfigureProduct">
<div id="order-standard_cart">

    <div class="row">
                    <div class="col-md-4" id="scrollingPanelContainer">

                        <div id="orderSummary">
                            <div class="order-summary">
                                <div class="loader" id="orderSummaryLoader">
                                    <i class="fa fa-fw fa-refresh fa-spin"></i>
                                </div>
                                <h2>{$productinfo.name}</h2>
                                <div class="summary-container" id="producttotal"></div>
                            </div>
                            <div class="text-center">
                                {if $pricing.type eq "recurring"}
                            <div class="field-container">
                                <div class="form-group">
                                    <label for="inputBillingcycle">{$LANG.cartchoosecycle}</label>
                                    <select name="billingcycle" id="inputBillingcycle" class="form-control select-inline" onchange="{if $configurableoptions}updateConfigurableOptions({$i}, this.value);{else}recalctotals();{/if}">
                                        {if $pricing.monthly}
                                            <option value="monthly"{if $billingcycle eq "monthly"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.monthly|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.monthly}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.quarterly}
                                            <option value="quarterly"{if $billingcycle eq "quarterly"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.quarterly|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.quarterly}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.semiannually}
                                            <option value="semiannually"{if $billingcycle eq "semiannually"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.semiannually|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.semiannually}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.annually}
                                            <option value="annually"{if $billingcycle eq "annually"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.annually|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.annually}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.biennially}
                                            <option value="biennially"{if $billingcycle eq "biennially"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.biennially|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.biennially}
                                                {/if}
                                            </option>
                                        {/if}
                                        {if $pricing.triennially}
                                            <option value="triennially"{if $billingcycle eq "triennially"} selected{/if}>
                                                {if !empty($configurableoptions)}
                                                    {$pricing.triennially|regex_replace:"/[^\s]+\s[^\s]+\s/":""}
                                                {else}
                                                    {$pricing.triennially}
                                                {/if}
                                            </option>
                                        {/if}
                                    </select>
                                </div>
                            </div>
                        {/if}
                                <button type="submit" id="btnCompleteProductConfig" class="btn btn-primary btn-lg">
                                    {$LANG.continue}
                                    <i class="fa fa-arrow-circle-right"></i>
                                </button>
                            </div>
                        </div>

                    </div>
        <div class="col-md-8 pull-md-right">
                <input type="hidden" name="configure" value="true" />
                <input type="hidden" name="i" value="{$i}" />
                        <p>{$LANG.orderForm.configureDesiredOptions}</p>
                        <div class="alert alert-danger hidden" role="alert" id="containerProductValidationErrors">
                            <p>{$LANG.orderForm.correctErrors}:</p>
                            <ul id="containerProductValidationErrorsList"></ul>
                        </div>
                        {if $productinfo.type eq "server" && false}
                            <div class="sub-heading">
                                <span>{$LANG.cartconfigserver}</span>
                            </div>
                            <div class="field-container">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputHostname">{$LANG.serverhostname}</label>
                                            <input type="text" name="hostname" class="form-control" id="inputHostname" value="{$server.hostname}" placeholder="servername.yourdomain.com">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputRootpw">{$LANG.serverrootpw}</label>
                                            <input type="password" name="rootpw" class="form-control" id="inputRootpw" value="{$server.rootpw}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputNs1prefix">{$LANG.serverns1prefix}</label>
                                            <input type="text" name="ns1prefix" class="form-control" id="inputNs1prefix" value="{$server.ns1prefix}" placeholder="ns1">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="inputNs2prefix">{$LANG.serverns2prefix}</label>
                                            <input type="text" name="ns2prefix" class="form-control" id="inputNs2prefix" value="{$server.ns2prefix}" placeholder="ns2">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {else $productinfo.type eq "server"}
                            
                            <input type="hidden" name="hostname" value="{substr(md5(mt_rand()), 0, 20)}">
<input type="hidden" name="rootpw" value="{substr(md5(mt_rand()), 0, 20)}">
<input type="hidden" name="ns1prefix" value="{substr(md5(mt_rand()), 0, 20)}">
<input type="hidden" name="ns2prefix" value="{substr(md5(mt_rand()), 0, 20)}">
                        {/if}

                        {if $configurableoptions}
                            <div class="product-configurable-options" id="productConfigurableOptions">
                                <div class="row">
                                    {foreach $configurableoptions as $num => $configoption}
                                        {if $configoption.optiontype eq 1}
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    {if $configoption.optionname == "OS"}
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
                                            {else if $configoption.optionname == "GPU"}
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
            {if $configoption.optionname == "GPU Count"} <div id="gpu_count" style="display:none"> {/if}
            <label for="_inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                <input type="hidden" name="configoption[{$configoption.id}]" value="{$configoption.selectedvalue}" id="inputConfigOption{$configoption.id}" />
                <input type="text" value="{$configoption.selectedname}" id="_inputConfigOption{$configoption.id}" class="form-control" />
                <script>
                    var sliderTimeoutId{$configoption.id} = null;
                    var values{$configoption.id} = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}"{$options.nameonly}"{/foreach}];
                    {if $configoption.optionname == "GPU Count"} gpu_orig_values = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}"{$options.nameonly}"{/foreach}]; {/if}
                    var ids{$configoption.id} = [{foreach key=num2 item=options from=$configoption.options}{if !$options@first},{/if}{$options.id}{/foreach}];
                    jQuery("#_inputConfigOption{$configoption.id}").ionRangeSlider({
                        grid: false,
                        grid_snap: false,
                        {if $configoption.optionname == "RAM" || $configoption.optionname == "GPU Count"}
                        {else}
                        values: values{$configoption.id},
                        {/if}
                        onChange: function(e) {
                            {if $configoption.optionname == "CPU"}
                            setRAMRange();
                            {/if}
                            {if $configoption.optionname == "GPU Count"}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[gpu_orig_values.indexOf(e.from_value + " " + gpuDropdounInstance.children("option:selected").text().trim())]);
                            {else}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[values{$configoption.id}.indexOf(e.from_value)]);
                            {/if}
                            if (sliderTimeoutId{$configoption.id}) {
                                clearTimeout(sliderTimeoutId{$configoption.id});
                            }
                            sliderTimeoutId = setTimeout(function() {
                                sliderTimeoutId{$configoption.id} = null;
                                recalctotals();
                            }, 500);
                        },
                        onUpdate: function(e) {
                            {if $configoption.optionname == "GPU Count"}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[gpu_orig_values.indexOf(e.from_value + " " + gpuDropdounInstance.children("option:selected").text().trim())]);
                            {else}
                            $(inputConfigOption{$configoption.id}).val(ids{$configoption.id}[values{$configoption.id}.indexOf(e.from_value)]);
                            {/if}
                        }
                    });
                    {if $configoption.optionname == "CPU"}
                        cpuSliderInstance = jQuery("#_inputConfigOption{$configoption.id}");
                    {/if}
                    {if $configoption.optionname == "GPU Count"}
                        gpuSliderInstance = jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        gpuValues = values{$configoption.id};
                        gpuSelectedValue = "{$configoption.selectedname}";
                    {/if}
                    {if $configoption.optionname == "RAM"}
                        ramSliderInstance = jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        ramValues=values{$configoption.id};
                        ramSelectedValue = "{$configoption.selectedname}";
                    {/if}
                    {if $configoption.optionname == "HDD"}
                        hddSliderInstance=jQuery("#_inputConfigOption{$configoption.id}").data("ionRangeSlider");
                        hddValues = values{$configoption.id};
                        hddSelectedValue = "{$configoption.selectedname}";
                    {/if}
                </script>
                {if $configoption.optionname == "GPU Count"}</div>{/if}
                {/if}
                                                </div>
                                            </div>
                                        {elseif $configoption.optiontype eq 2}
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                                                    {foreach key=num2 item=options from=$configoption.options}
                                                        <br />
                                                        <label>
                                                            <input type="radio" name="configoption[{$configoption.id}]" value="{$options.id}"{if $configoption.selectedvalue eq $options.id} checked="checked"{/if} />
                                                            {if $options.name}
                                                                {$options.nameonly}
                                                            {else}
                                                                {$LANG.enable}
                                                            {/if}
                                                        </label>
                                                    {/foreach}
                                                </div>
                                            </div>
                                        {elseif $configoption.optiontype eq 3}
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                        <div class="pull-right">
                                                            {$configoption.optionname}
                                                        <input type="checkbox" name="configoption[{$configoption.id}]" id="inputConfigOption{$configoption.id}" value="1"{if $configoption.selectedqty} checked{/if} />
                                                       
                                                    </div>
                                                    </div>
                                            </div>
                                        {elseif $configoption.optiontype eq 4}
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="inputConfigOption{$configoption.id}">{$configoption.optionname}</label>
                                                    {if $configoption.qtymaximum}
                                                        {if !$rangesliderincluded}
                                                            {assign var='rangesliderincluded' value=true}
                                                        {/if}
                                                        <input type="text" name="configoption[{$configoption.id}]" value="{if $configoption.selectedqty}{$configoption.selectedqty}{else}{$configoption.qtyminimum}{/if}" id="inputConfigOption{$configoption.id}" class="form-control" />
                                                        <script>
                                                            var sliderTimeoutId = null;
                                                            var sliderRangeDifference = {$configoption.qtymaximum} - {$configoption.qtyminimum};
                                                            // The largest size that looks nice on most screens.
                                                            var sliderStepThreshold = 25;
                                                            // Check if there are too many to display individually.
                                                            var setLargerMarkers = sliderRangeDifference > sliderStepThreshold;

                                                            jQuery("#inputConfigOption{$configoption.id}").ionRangeSlider({
                                                                min: {$configoption.qtyminimum},
                                                                max: {$configoption.qtymaximum},
                                                                grid: true,
                                                                grid_snap: setLargerMarkers ? false : true,
                                                                onChange: function() {
                                                                    if (sliderTimeoutId) {
                                                                        clearTimeout(sliderTimeoutId);
                                                                    }

                                                                    sliderTimeoutId = setTimeout(function() {
                                                                        sliderTimeoutId = null;
                                                                        recalctotals();
                                                                    }, 250);
                                                                }
                                                            });
                                                        </script>
                                                    {else}
                                                        <div>
                                                            <input type="number" name="configoption[{$configoption.id}]" value="{if $configoption.selectedqty}{$configoption.selectedqty}{else}{$configoption.qtyminimum}{/if}" id="inputConfigOption{$configoption.id}" min="{$configoption.qtyminimum}" onchange="recalctotals()" onkeyup="recalctotals()" class="form-control form-control-qty" />
                                                            <span class="form-control-static form-control-static-inline">
                                                                x {$configoption.options.0.name}
                                                            </span>
                                                        </div>
                                                    {/if}
                                                </div>
                                            </div>
                                        {/if}
                                        {if $num % 2 != 0}
                                            </div>
                                            <div class="row">
                                        {/if}
                                    {/foreach}
                                </div>
                            </div>

                        {/if}

                        {if $customfields}
                            <div class="field-container">
                                {foreach $customfields as $customfield}
                                    <div class="form-group">
                                        <label for="customfield{$customfield.id}">{$customfield.name}</label>
                                        {$customfield.input}
                                        {if $customfield.description}
                                            <span class="field-help-text">
                                                {$customfield.description}
                                            </span>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>

                        {/if}

                        {if $addons || count($addonsPromoOutput) > 0}

                            <div class="sub-heading">
                                <span>{$LANG.cartavailableaddons}</span>
                            </div>

                            {foreach $addonsPromoOutput as $output}
                                <div>
                                    {$output}
                                </div>
                            {/foreach}

                            <div class="row addon-products">
                                {foreach $addons as $addon}
                                    <div class="col-sm-{if count($addons) > 1}6{else}12{/if}">
                                        <div class="panel panel-default panel-addon{if $addon.status} panel-addon-selected{/if}">
                                            <div class="panel-body">
                                                <label>
                                                    <input type="checkbox" name="addons[{$addon.id}]"{if $addon.status} checked{/if} />
                                                    {$addon.name}
                                                </label><br />
                                                {$addon.description}
                                            </div>
                                            <div class="panel-price">
                                                {$addon.pricing}
                                            </div>
                                            <div class="panel-add">
                                                <i class="fa fa-plus"></i>
                                                {$LANG.addtocart}
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>

                        {/if}

                            <p>{$productinfo.description}</p>

                    

                </div>

        </div>
    </div>
</div>

            </form>
<script>setHDDRange(false);setRAMRange(false);setGPURange();</script>