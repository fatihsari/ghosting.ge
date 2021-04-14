<form role="form" method="post" action="{if !$loggedin}{$seo_faq_root}{else}{routePath('knowledgebase-search')}{/if}">
    <div class="input-group input-group-lg kb-search">
        {if !$loggedin}<input type="hidden" name="action" value="search"/>{/if}
        <input type="text" id="inputKnowledgebaseSearch" name="search" class="form-control" placeholder="{$LANG.knowledgebaseserachplaceholder}" />
        <span class="input-group-btn">
            <input type="submit" id="btnKnowledgebaseSearch" class="btn btn-primary btn-input-padded-responsive" value="{$LANG.search}" />
        </span>
    </div>
</form>

<h2>{$LANG.knowledgebasecategories}</h2>

{if $kbcats}
    <div class="row kbcategories">
        {foreach from=$kbcats name=kbcats item=kbcat}
            <div class="col-sm-4">
                <a href="{if !$loggedin}{$seo_faq_root}{$kbcat.urlfriendlyname}/c{$kbcat.id}/{else}{routePath('knowledgebase-category-view', {$kbcat.id}, {$kbcat.urlfriendlyname})}{/if}">
                    <i class="fa fa-folder-open-o"></i>
                    {$kbcat.name} ({$kbcat.numarticles})
                </a>
                {if $kbcat.editLink}
                    <a href="{$kbcat.editLink}" class="admin-inline-edit">
                        <i class="fa fa-pencil fa-fw"></i>
                        {$LANG.edit}
                    </a>
                {/if}
                <p>{$kbcat.description}</p>
            </div>
            {if $smarty.foreach.kbcats.iteration mod 3 == 0}
                </div><div class="row">
            {/if}
        {/foreach}
    </div>
{else}
    {include file="$template/includes/alert.tpl" type="info" msg=$LANG.knowledgebasenoarticles textcenter=true}
{/if}

{if $kbmostviews}

    <h2>{$LANG.knowledgebasepopular}</h2>

    <div class="kbarticles">
        {foreach from=$kbmostviews item=kbarticle}
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
        {/foreach}
    </div>

{/if}