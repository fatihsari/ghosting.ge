<ol class="breadcrumb">
    {foreach $breadcrumb as $item}
        <li{if $item@last} class="active"{/if}>
            {if $item@first && !$item@last}<a href="{$seo_home_url}">{elseif !$item@last}
                {if !$loggedin && $item.link|strstr:"knowledgebase.php"}
                    {if $item.link|strstr:"displaycat"}
                        <a href="{$seo_faq_root}{$item.label|replace:' ':'-'}/c{$item.link|regex_replace:'/[^\d]/':''}">
                    {else}
                        <a href="{$seo_faq_root}">
                    {/if}
                {else}
                    <a href="{$item.link}">
                {/if}
            {/if}
            {$item.label}
            {if !$item@last}</a>{/if}
        </li>
    {/foreach}
</ol>