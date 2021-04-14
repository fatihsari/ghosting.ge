<form role="form" method="post" action="{if !$loggedin}{$seo_faq_root}{else}{routePath('knowledgebase-search')}{/if}">
    <div class="input-group input-group-lg kb-search">
        {if !$loggedin}<input type="hidden" name="action" value="search"/>{/if}
        <input type="text"  id="inputKnowledgebaseSearch" name="search" class="form-control" placeholder="{$LANG.knowledgebaseserachplaceholder}" value="{$searchterm}" />
        <span class="input-group-btn">
            <input type="submit" id="btnKnowledgebaseSearch" class="btn btn-primary btn-input-padded-responsive" value="{$LANG.search}" />
        </span>
    </div>
</form>

{if $kbcats}
    <h2>{$LANG.knowledgebasecategories}</h2>

    <div class="row kbcategories">
        {foreach name=kbasecats from=$kbcats item=kbcat}
            <div class="col-sm-4">
                <a href="{if !$loggedin}{$seo_faq_root}{$kbcat.urlfriendlyname}/c{$kbcat.id}/{else}{routePath('knowledgebase-category-view',{$kbcat.id},{$kbcat.urlfriendlyname})}{/if}">
                    <span class="glyphicon glyphicon-folder-close"></span> {$kbcat.name} <span class="badge badge-info">{$kbcat.numarticles}</span>
                </a>
                {if $kbcat.editLink}
                    <a href="{$kbcat.editLink}" class="admin-inline-edit">
                        <i class="fa fa-pencil fa-fw"></i>
                        {$LANG.edit}
                    </a>
                {/if}
                <p>{$kbcat.description}</p>
            </div>
        {/foreach}
    </div>
{/if}

{if $kbarticles || !$kbcats}
    {if $tag}
        <h2>{$LANG.kbviewingarticlestagged} '{$tag}'</h2>
    {else}
        <h2>{$LANG.knowledgebasearticles}</h2>
    {/if}

    <div class="kbarticles">
        {foreach from=$kbarticles item=kbarticle}
            <a href="{if !$loggedin}{$seo_faq_root}{$kbarticle.urlfriendlytitle}/a{$kbarticle.id}/{else}{routePath('knowledgebase-article-view', {$kbarticle.id}, {$kbarticle.urlfriendlytitle})}{/if}">
                <span class="glyphicon glyphicon-file"></span>&nbsp;{$kbarticle.title}
            </a>
            {if $kbarticle.editLink}
                <a href="{$kbarticle.editLink}" class="admin-inline-edit">
                    <i class="fa fa-pencil fa-fw"></i>
                    {$LANG.edit}
                </a>
            {/if}
            <p>{$kbarticle.article|truncate:100:"..."}</p>
        {foreachelse}
            {include file="$template/includes/alert.tpl" type="info" msg=$LANG.knowledgebasenoarticles textcenter=true}
        {/foreach}
    </div>
{/if}