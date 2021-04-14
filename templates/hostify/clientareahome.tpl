<div class="tiles clearfix">
    <div class="row">
        <div class="col-sm-4 col-xs-12 tile" onclick="window.location='clientarea.php?action=services'">
            <a href="clientarea.php?action=services">
                <div class="icon"><i class="fa fa-cube"></i></div>
                <div class="stat">{$clientsstats.productsnumactive}</div>
                <div class="title">{$LANG.navservices}</div>
                <div class="highlight"></div>
            </a>
        </div>
        <div class="col-sm-4 col-xs-12 tile" onclick="window.location='supporttickets.php'">
            <a href="supporttickets.php">
                <div class="icon"><i class="fa fa-comments"></i></div>
                <div class="stat">{$clientsstats.numactivetickets}</div>
                <div class="title">{$LANG.navtickets}</div>
                <div class="highlight"></div>
            </a>
        </div>
        <div class="col-sm-4 col-xs-12 tile" onclick="window.location='clientarea.php?action=invoices'">
            <a href="clientarea.php?action=invoices">
                <div class="icon"><i class="fa fa-credit-card"></i></div>
                <div class="stat">{$clientsstats.numunpaidinvoices}</div>
                <div class="title">{$LANG.navinvoices}</div>
                <div class="highlight"></div>
            </a>
        </div>
    </div>
</div>

{foreach from=$addons_html item=addon_html}
    <div>
        {$addon_html}
    </div>
{/foreach}

<div class="client-home-panels">
    <div class="row">
        <div class="col-sm-12">

            {function name=outputHomePanels}
                <div menuItemName="{$item->getName()}" class="panel panel-default panel-accent-{$item->getExtra('color')}{if $item->getClass()} {$item->getClass()}{/if}"{if $item->getAttribute('id')} id="{$item->getAttribute('id')}"{/if}>
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            {if $item->getExtra('btn-link') && $item->getExtra('btn-text')}
                                <div class="pull-right">
                                    <a href="{$item->getExtra('btn-link')}" class="btn btn-default bg-color-{$item->getExtra('color')} btn-xs">
                                        {if $item->getExtra('btn-icon')}<i class="fa {$item->getExtra('btn-icon')}"></i>{/if}
                                        {$item->getExtra('btn-text')}
                                    </a>
                                </div>
                            {/if}
                            {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
                            {$item->getLabel()}
                            {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
                        </h3>
                    </div>
                    {if $item->hasBodyHtml()}
                        <div class="panel-body">
                            {$item->getBodyHtml()}
                        </div>
                    {/if}
                    {if $item->hasChildren()}
                        <div class="list-group{if $item->getChildrenAttribute('class')} {$item->getChildrenAttribute('class')}{/if}">
                            {foreach $item->getChildren() as $childItem}
                                {if $childItem->getUri()}
                                    <a menuItemName="{$childItem->getName()}" href="{$childItem->getUri()}" class="list-group-item{if $childItem->getClass()} {$childItem->getClass()}{/if}{if $childItem->isCurrent()} active{/if}"{if $childItem->getAttribute('dataToggleTab')} data-toggle="tab"{/if}{if $childItem->getAttribute('target')} target="{$childItem->getAttribute('target')}"{/if} id="{$childItem->getId()}">
                                        {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                                        {$childItem->getLabel()}
                                        {if $childItem->hasBadge()}&nbsp;<span class="badge">{$childItem->getBadge()}</span>{/if}
                                    </a>
                                {else}
                                    <div menuItemName="{$childItem->getName()}" class="list-group-item{if $childItem->getClass()} {$childItem->getClass()}{/if}" id="{$childItem->getId()}">
                                        {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                                        {$childItem->getLabel()}
                                        {if $childItem->hasBadge()}&nbsp;<span class="badge">{$childItem->getBadge()}</span>{/if}
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                    {/if}
                    <div class="panel-footer">
                        {if $item->hasFooterHtml()}
                            {$item->getFooterHtml()}
                        {/if}
                    </div>
                </div>
            {/function}

            {foreach $panels as $item}
                {if $item@iteration is odd}
                    {outputHomePanels}
                {/if}
            {/foreach}

        </div>
        <div class="col-sm-12">

            {foreach $panels as $item}
                {if $item@iteration is even && $item->getIcon() != 'fa fa-newspaper-o'}
                    {outputHomePanels}
                {/if}
            {/foreach}

        </div>
    </div>
</div>
