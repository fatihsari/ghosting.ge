{if $producttotals}
    {foreach $producttotals.configoptions as $configoption}
        {if $configoption}
            <div class="clearfix">
                <span class="pull-left">&nbsp;&raquo; {$configoption.name}: {$configoption.optionname}</span>
                <span class="pull-right">{if preg_match("/\d/", $configoption.recurring)}{$configoption.recurring}{else}{$configoption.recurring|replace:' ':'0.00 '}{/if}</span>
            </div>
        {/if}
    {/foreach}
     <div class="summary-totals">
    {foreach from=$producttotals.pricing.recurringexcltax key=cycle item=recurring}
                <div class="clearfix">
                    <span class="pull-left">{$cycle}:</span>
                    <span class="pull-right">{$recurring}</span>
                </div>
            {/foreach}
    </div>
    <div class="total-due-today">
        <span class="amt">{$producttotals.pricing.totaltoday}</span>
    </div>
{/if}
