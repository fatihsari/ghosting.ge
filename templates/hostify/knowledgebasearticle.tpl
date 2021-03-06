<div class="kb-article-title">
    <a href="#" class="btn btn-link btn-print" onclick="window.print();return false"><i class="fa fa-print"></i></a>
    <h2>{$kbarticle.title}</h2>
</div>

{if $kbarticle.voted}
    {include file="$template/includes/alert.tpl" type="success alert-bordered-left" msg="{lang key="knowledgebaseArticleRatingThanks"}" textcenter=true}
{/if}

<div class="kb-article-content">
    {$kbarticle.text}
    <div style="clear:both"></div>
    <ul class="kb-article-details">
        {foreach item=keyword from=$kbarticle.keywords}
            <li><a href="{if !$loggedin}{$seo_faq_root}{$kbarticle.urlfriendlytitle}/a{$kbarticle.id}/{else}{routePath('knowledgebase-article-view', {$kbarticle.id}, {$kbarticle.urlfriendlytitle})}{/if}"><i class="fa fa-tag"></i>{$keyword}</a></li>
        {/foreach}
    </ul>
</div>
<div style="clear:both"></div>
{if $kbarticle.editLink}
    <a href="{$kbarticle.editLink}" class="btn btn-default btn-sm pull-right">
        <i class="fa fa-pencil fa-fw"></i>
        {$LANG.edit}
    </a>
{/if}

<ul class="kb-article-details">
    {if $kbarticle.tags }
        <li><i class="fa fa-tag"></i> {$kbarticle.tags}</li>
    {/if}
    <li><i class="fa fa-star"></i> {$kbarticle.useful} {$LANG.knowledgebaseratingtext}</li>
</ul>
<div class="clearfix"></div>

<div class="kb-rate-article hidden-print">
    <form action="{if !$loggedin}{$seo_faq_root}{$kbarticle.urlfriendlytitle}/a{$kbarticle.id}/{else}{routePath('knowledgebase-article-view', {$kbarticle.id}, {$kbarticle.urlfriendlytitle})}{/if}" method="post">
        <input type="hidden" name="useful" value="vote">
        {if $kbarticle.voted}{$LANG.knowledgebaserating}{else}{$LANG.knowledgebasehelpful}{/if}
        {if $kbarticle.voted}
            {$kbarticle.useful} {$LANG.knowledgebaseratingtext} ({$kbarticle.votes} {$LANG.knowledgebasevotes})
        {else}
            <button type="submit" name="vote" value="yes" class="btn btn-lg btn-link"><i class="fa fa-thumbs-o-up"></i> {$LANG.knowledgebaseyes}</button>
            <button type="submit" name="vote" value="no" class="btn btn-lg btn-link"><i class="fa fa-thumbs-o-down"></i> {$LANG.knowledgebaseno}</button>
        {/if}
    </form>
</div>

{if $kbarticles}
    <div class="kb-also-read">
        <h3>{$LANG.knowledgebaserelatedarticles}</h3>
        <div class="kbarticles">
            {foreach key=num item=kbarticle from=$kbarticles}
                <div>
                    <a href="{if !$loggedin}{$seo_faq_root}{$kbarticle.urlfriendlytitle}/a{$kbarticle.id}/{else}{routePath('knowledgebase-article-view', {$kbarticle.id}, {$kbarticle.urlfriendlytitle})}{/if}">
                        <i class="glyphicon glyphicon-file"></i> {$kbarticle.title}
                    </a>
                    {if $kbarticle.editLink}
                        <a href="{$kbarticle.editLink}" class="admin-inline-edit">
                            <i class="fa fa-pencil fa-fw"></i>
                            {$LANG.edit}
                        </a>
                    {/if}
                    <p>{$kbarticle.article|truncate:100:"..."}</p>
                </div>
            {/foreach}
        </div>
    </div>
{/if}