{foreach $sidebar as $item}
    {if $item->getName() == 'Client Contacts' || $item->getName() == 'Client Shortcuts' || $item->getName() == 'My Services Actions' || $item->getName() == 'Support'}
        {continue}
    {/if}
    <div menuItemName="{$item->getName()}" class="panel panel-sidebar {if $item->getClass()}{$item->getClass()}{else}panel-sidebar{/if}{if $item->getExtra('mobileSelect') and $item->hasChildren()} hidden-sm hidden-xs{/if}"{if $item->getAttribute('id')} id="{$item->getAttribute('id')}"{/if}>
        <div class="panel-heading">
            <h3 class="panel-title">
                {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
                {$item->getLabel()}
                {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
            </h3>
        </div>
        {if $item->hasBodyHtml()}
            <div class="panel-body">
                {if !$loggedin}
                    {$item->getBodyHtml()|replace:'/knowledgebase/tag/':"{$seo_faq_root}"}
                {else}
                    {$item->getBodyHtml()}
                {/if}
            </div>
        {/if}
        {if $item->hasChildren()}
            <div class="list-group{if $item->getChildrenAttribute('class')} {$item->getChildrenAttribute('class')}{/if}">
                {foreach $item->getChildren() as $childItem}
                {if $childItem->getName() != 'Quotes' && $childItem->getName() != 'Contacts/Sub-Accounts'}
                    {if $childItem->getUri()}
                        <a menuItemName="{$childItem->getName()}" href="{if $childItem->getUri()|strstr:"knowledgebase" && !$loggedin}{$seo_faq_root}{$childItem->getUri()|regex_replace:"/\/knowledgebase\/\d+\//":""}/c{$childItem->getUri()|regex_replace:"/[^0-9]/":""}/{else}{$childItem->getUri()}{/if}" class="list-group-item{if $childItem->isDisabled()} disabled{/if}{if $childItem->getClass()} {$childItem->getClass()}{/if}{if $childItem->isCurrent()} active{/if}"{if $childItem->getAttribute('dataToggleTab')} data-toggle="tab"{/if}{if $childItem->getAttribute('target')} target="{$childItem->getAttribute('target')}"{/if} id="{$childItem->getId()}">
                            {if $childItem->hasBadge()}<span class="badge">{$childItem->getBadge()}</span>{/if}
                            {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                            {$childItem->getLabel()}
                        </a>
                    {else}
                        <div menuItemName="{$childItem->getName()}" class="list-group-item{if $childItem->getClass()} {$childItem->getClass()}{/if}" id="{$childItem->getId()}">
                            {if $childItem->hasBadge()}<span class="badge">{$childItem->getBadge()}</span>{/if}
                            {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                            {$childItem->getLabel()}
                        </div>
                    {/if}
                {/if}
                {/foreach}
            </div>
        {/if}
        {if $item->hasFooterHtml()}
            <div class="panel-footer clearfix">
                {$item->getFooterHtml()}
            </div>
        {/if}
    </div>
    {if $item->getExtra('mobileSelect') and $item->hasChildren()}
        {* Mobile Select only supports dropdown menus *}
        <div class="panel hidden-lg hidden-md {if $item->getClass()}{$item->getClass()}{else}panel-default{/if}"{if $item->getAttribute('id')} id="{$item->getAttribute('id')}"{/if}>
            <div class="panel-heading">
                <h3 class="panel-title">
                    {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
                    {$item->getLabel()}
                    {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
                </h3>
            </div>
            <div class="panel-body">
                <form role="form">
                    <select class="form-control" onchange="selectChangeNavigate(this)">
                        {foreach $item->getChildren() as $childItem}
                            <option menuItemName="{$childItem->getName()}" value="{$childItem->getUri()}" class="list-group-item" {if $childItem->isCurrent()}selected="selected"{/if}>
                                {$childItem->getLabel()}
                                {if $childItem->hasBadge()}({$childItem->getBadge()}){/if}
                            </option>
                        {/foreach}
                    </select>
                </form>
            </div>
            {if $item->hasFooterHtml()}
                <div class="panel-footer">
                    {$item->getFooterHtml()}
                </div>
            {/if}
        </div>
    {/if}
{/foreach}