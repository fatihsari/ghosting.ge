{if $x_navbar}
{foreach $x_navbar as $item}
    <li menuItemName="{$item->getName()}" class="{if $item->hasChildren()}dropdown{/if}{if $item->getClass()} {$item->getClass()}{/if}" id="{$item->getId()}">
        <a {if $item->hasChildren()}class="dropdown-toggle" data-toggle="dropdown" href="#"{else}href="{$item->getUri()}"{/if}{if $item->getAttribute('target')} target="{$item->getAttribute('target')}"{/if}>
            {if $item->hasIcon()}<i class="{$item->getIcon()}"></i>&nbsp;{/if}
            {$item->getLabel()}
            {if $item->hasBadge()}&nbsp;<span class="badge">{$item->getBadge()}</span>{/if}
            {if $item->hasChildren()}&nbsp;<b class="caret"></b>{/if}
        </a>
        {if $item->hasChildren()}
            <ul class="dropdown-menu">
            {foreach $item->getChildren() as $childItem}
                <li menuItemName="{$childItem->getName()}"{if $childItem->getClass()} class="{$childItem->getClass()}"{/if} id="{$childItem->getId()}">
                    <a href="{$childItem->getUri()}"{if $childItem->getAttribute('target')} target="{$childItem->getAttribute('target')}"{/if}>
                        {if $childItem->hasIcon()}<i class="{$childItem->getIcon()}"></i>&nbsp;{/if}
                        {$childItem->getLabel()}
                        {if $childItem->hasBadge()}&nbsp;<span class="badge">{$childItem->getBadge()}</span>{/if}
                    </a>
                </li>
            {/foreach}
            </ul>
        {/if}
    </li>
{/foreach}
{else}
<li><a href="{$seo_home_url}" class="up">{$LANG.ghosting.nav.main}</a></li>
<li><a href="{$seo_web_hosting_url}" class="up">{$LANG.ghosting.nav.webhosting}</a></li>
<li><a href="{$seo_vps_servers_url}" class="up">{$LANG.ghosting.nav.vps}</a></li>
<li><a href="{$seo_databases_url}" class="up">{$LANG.ghosting.nav.db}</a></li>
<li><a href="{$seo_profesional_service_url}" class="up">{$LANG.ghosting.nav.service}</a></li>
<li><a href="{$seo_about_us_url}" class="up">{$LANG.ghosting.nav.about}</a></li>
<li><a href="{$systemurl}contact.php" class="up">{$LANG.ghosting.nav.contact}</a></li>
{/if}