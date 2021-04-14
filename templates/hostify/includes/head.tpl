<!-- Styling -->
<link href="{$WEB_ROOT}/templates/{$template}/css/all.styles.min.css" rel="stylesheet">
<!-- Favicons -->
<link rel="apple-touch-icon-precomposed" sizes="57x57"   href="{$WEB_ROOT}/templates/{$template}/img/ico/apple-touch-icon-57-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$WEB_ROOT}/templates/{$template}/img/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72"   href="{$WEB_ROOT}/templates/{$template}/img/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$WEB_ROOT}/templates/{$template}/img/ico/apple-touch-icon-144-precomposed.png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav16.png"  sizes="16x16"   type="image/png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav32.png"  sizes="32x32"   type="image/png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav48.png"  sizes="48x48"   type="image/png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav64.png"  sizes="64x64"   type="image/png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav128.png" sizes="128x128" type="image/png">
<link rel="icon" href="{$WEB_ROOT}/templates/{$template}/img/ico/fav32.png">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
{if $templatefile == "viewticket" && !$loggedin}
  <meta name="robots" content="noindex" />
{/if}
<script type="text/javascript">
    var csrfToken = '{$token}',
        markdownGuide = '{lang key="markdown.title"}',
        locale = '{if !empty($mdeLocale)}{$mdeLocale}{else}en{/if}',
        saved = '{lang key="markdown.saved"}',
        saving = '{lang key="markdown.saving"}',
        whmcsBaseUrl = "{\WHMCS\Utility\Environment\WebHelper::getBaseUrl()}";
</script>
<script src="{$WEB_ROOT}/templates/{$template}/js/scripts.min.js?v={$versionHash}" ></script>

{literal}
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-SW47KK5QDD"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-SW47KK5QDD');
</script>
{/literal}