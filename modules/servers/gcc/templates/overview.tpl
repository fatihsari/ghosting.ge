{foreach $hookOutput as $output}
    <div>
        {$output}
    </div>
{/foreach}

{if $systemStatus != 'Active'}

    <div class="alert alert-warning text-center" role="alert" id="cPanelSuspendReasonPanel">
        {if $suspendreason}
            <strong>{$suspendreason}</strong><br />
        {/if}
        {$LANG.cPanel.packageNotActive} {$status}.<br />
        {if $systemStatus eq "Pending"}
            {$LANG.cPanel.statusPendingNotice}
        {elseif $systemStatus eq "Suspended"}
            {$LANG.cPanel.statusSuspendedNotice}
        {/if}
    </div>

{/if}

<div class="panel panel-default" id="cPanelBillingOverviewPanel">
    <div class="panel-heading">
        <h3 class="panel-title">{$LANG.cPanel.billingOverview}</h3>
    </div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-5">
                {if $firstpaymentamount neq $recurringamount}
                    <div class="row" id="firstPaymentAmount">
                        <div class="col-xs-6 text-right" >
                            {$LANG.firstpaymentamount}
                        </div>
                        <div class="col-xs-6">
                            {$firstpaymentamount}
                        </div>
                    </div>
                {/if}
                {if $billingcycle != $LANG.orderpaymenttermonetime && $billingcycle != $LANG.orderfree}
                    <div class="row" id="recurringAmount">
                        <div class="col-xs-6 text-right">
                            {$LANG.recurringamount}
                        </div>
                        <div class="col-xs-6">
                            {$recurringamount}
                        </div>
                    </div>
                {/if}
                <div class="row" id="billingCycle">
                    <div class="col-xs-6 text-right">
                        {$LANG.orderbillingcycle}
                    </div>
                    <div class="col-xs-6">
                        {$billingcycle}
                    </div>
                </div>
                <div class="row" id="paymentMethod">
                    <div class="col-xs-6 text-right">
                        {$LANG.orderpaymentmethod}
                    </div>
                    <div class="col-xs-6">
                        {if $paymentmethod eq 'Invoice'}
                            {$LANG.ghosting.gateways.Invoice}
                        {else}
                            {$LANG.ghosting.gateways.Visa}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row" id="registrationDate">
                    <div class="col-xs-6 col-md-5 text-right">
                        {$LANG.clientareahostingregdate}
                    </div>
                    <div class="col-xs-6 col-md-7">
                        {$regdate}
                    </div>
                </div>
                <div class="row" id="nextDueDate">
                    <div class="col-xs-6 col-md-5 text-right">
                        {$LANG.clientareahostingnextduedate}
                    </div>
                    <div class="col-xs-6 col-md-7">
                        {$nextduedate}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{if $configurableoptions}
    <div class="panel panel-default" id="cPanelConfigurableOptionsPanel">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-4"><h3 class="panel-title">{$LANG.orderconfigpackage}</h3></div>
                <div class="col-md-8 text-right">
                    <a href="javascript;" data-toggle="modal" data-target="#confirmRebootModal" style="font-size: 14px; color: #c30101;"><i class="fa fa-refresh" aria-hidden="true" style="margin-right: 4px;"></i>{$LANG.ghosting.reboot.label}</a>
                </div>
            </div>
            
        </div>
        <div class="panel-body">
            {foreach from=$configurableoptions item=configoption}
                <div class="row">
                    <div class="col-md-5 col-xs-6 text-right">
                        <strong>{$configoption.optionname}</strong>
                    </div>
                    <div class="col-md-7 col-xs-6 text-left">
                        {if $configoption.optiontype eq 3}{if $configoption.selectedqty}{$LANG.yes}{else}{$LANG.no}{/if}{elseif $configoption.optiontype eq 4}{$configoption.selectedqty} x {$configoption.selectedoption}{else}{$configoption.selectedoption}{/if}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}

{if $systemStatus == 'Active' && $producttype=="hostingaccount"}
<div class="row">
    <div class="col-md-12 col-xs-12 text-center">
        <form action="https://{$sub_hostname}.{$hostname}:2082" method="post" target="_blank" role="form" name="frmZLogin" id="frmZLogin">
            <input type="hidden" class="form-control" id="inputUsername" name="inUsername" value="{$username}"/>
            <input type="hidden" class="form-control" id="inPassword" name="inPassword" value="{$password}"/>
            <input type="hidden" class="form-control" id="inContainerId" name="inContainerId" value="{$container_id}"/>
			<button type="submit" class="btn btn-primary btn-lg" name="sublogin2" value="LogIn">{$LANG.ghosting.control_panel} <i class="fa fa-arrow-circle-right"></i></button>
		</form>
		
		<div class="modal fade" id="confirmRebootModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">{$LANG.ghosting.reboot.confirm}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-footer">
                <form method="post" action="clientarea.php?action=productdetails">
                    <input type="hidden" name="id" value="{$serviceid}" />
                    <input type="hidden" name="modop" value="custom" />
                    <input type="hidden" name="a" value="reboot" />
                    <input type="submit" class="btn btn-primary" style="background:#c30101" value="{$LANG.ghosting.reboot.yes}" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{$LANG.ghosting.reboot.no}</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        
    </div>
</div>
{/if}
{if $customfields}
    <div class="panel panel-default" id="cPanelAdditionalInfoPanel">
        <div class="panel-heading">
            <h3 class="panel-title">{$LANG.additionalInfo}</h3>
        </div>
        <div class="panel-body">
            {foreach from=$customfields item=field}
                <div class="row">
                    <div class="col-md-5 col-xs-6 text-right">
                        <strong>{$field.name}</strong>
                    </div>
                    <div class="col-md-7 col-xs-6 text-left">
                        {if empty($field.value)}
                            {$LANG.blankCustomField}
                        {else}
                            {$field.value}
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}