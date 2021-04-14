<?php
function hook_ghostingseo($params) {
    $return = array();
    if($params['loggedin'])
    {
        $return['seo_home_url'] = $params['systemurl'];
        $return['seo_web_hosting_url'] = $params['systemurl'].'custom_product.php?pid=10';
        $return['seo_vps_servers_url'] = $params['systemurl'].'custom_product.php?pid=11';
        $return['seo_databases_url'] = $params['systemurl'].'custom_product.php?pid=12';
        $return['seo_profesional_service_url'] = $params['systemurl'].'custom_service.php';
        $return['seo_about_us_url'] = $params['systemurl'].'about_us.php';
        $return['seo_terms_and_conditions_url'] = $params['systemurl'].'document.php?terms';
        $return['seo_privacy_policy_url'] = $params['systemurl'].'document.php?policy';
        $return['seo_faq_root'] = $params['systemurl'].'knowledgebase';
    }
    else
    {
        switch($params['language'])
        {
            case 'georgian' :
            {
                $return['seo_home_url'] = $params['systemurl'].'ka/';
                $return['seo_web_hosting_url'] = $params['systemurl'].'ka/ვებ-ჰოსტინგი/';
                $return['seo_vps_servers_url'] = $params['systemurl'].'ka/vps-სერვერი/';
                $return['seo_databases_url'] = $params['systemurl'].'ka/მონაცემთა-ბაზა/';
                $return['seo_profesional_service_url'] = $params['systemurl'].'ka/პროფესიონალური-ჰოსტინგი/';
                $return['seo_about_us_url'] = $params['systemurl'].'ka/კომპანიის-შესახებ/';
                $return['seo_terms_and_conditions_url'] = $params['systemurl'].'ka/წესები-და-პირობები/';
                $return['seo_privacy_policy_url'] = $params['systemurl'].'ka/კონფიდენციალურობის-პოლიტიკა/';
                $return['seo_faq_root'] = $params['systemurl'].'ka/დახმარება/';
                break;
            }
            case 'russian' :
            {
                $return['seo_home_url'] = $params['systemurl'].'ru/';
                $return['seo_web_hosting_url'] = $params['systemurl'].'ru/веб-хостинг/';
                $return['seo_vps_servers_url'] = $params['systemurl'].'ru/vps-сервер/';
                $return['seo_databases_url'] = $params['systemurl'].'ru/удаленная-база-данных/';
                $return['seo_profesional_service_url'] = $params['systemurl'].'ru/профессиональный-хостинг/';
                $return['seo_about_us_url'] = $params['systemurl'].'ru/о-нас/';
                $return['seo_terms_and_conditions_url'] = $params['systemurl'].'ru/условия-и-положения/';
                $return['seo_privacy_policy_url'] = $params['systemurl'].'ru/политика-конфиденциальности/';
                $return['seo_faq_root'] = $params['systemurl'].'ru/помощь/';
                break;
            }
        	default :
            {
                $return['seo_home_url'] = $params['systemurl'].'en/';
                $return['seo_web_hosting_url'] = $params['systemurl'].'en/web-hosting/';
                $return['seo_vps_servers_url'] = $params['systemurl'].'en/vps-server/';
                $return['seo_databases_url'] = $params['systemurl'].'en/remote-database/';
                $return['seo_profesional_service_url'] = $params['systemurl'].'en/profesional-hosting/';
                $return['seo_about_us_url'] = $params['systemurl'].'en/about-us/';
                $return['seo_terms_and_conditions_url'] = $params['systemurl'].'en/terms-and-conditions/';
                $return['seo_privacy_policy_url'] = $params['systemurl'].'en/privacy-policy/';
                $return['seo_faq_root'] = $params['systemurl'].'en/help/';
                break;
            }
        }
    }
    $current_url = urldecode(str_replace('&amp;', '&', "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"));
    $url_data = parse_url($current_url);
    if(!$params['loggedin'] && (empty($url_data['path']) || $url_data['path']=='/' || $url_data['path']=='/index.php'))
    {
        header("Location: $return[seo_home_url]", true, 301);
        exit;
    }
    $current_url_path = $url_data['path'] . (!empty($url_data['query']) ? '?'.$url_data['query'] : '');
    $res = mysql_query("SELECT * FROM mod_ghostingseoaddon WHERE '$current_url_path' REGEXP `regex` LIMIT 1");
    $data = mysql_fetch_assoc($res);
    if($data)
    {
        $return['seotitle'] = $data['pageheader_'.$params['language']];
        $return['seokeyword'] = $data['keyword_'.$params['language']];
        $return['seodecription'] = $data['description_'.$params['language']];
        $return['fburl'] = !empty($data['ogurl']) ? $data['ogurl'] : $current_url;
        $return['fbtype'] = !empty($data['ogtype']) ? $data['ogtype'] : 'website';
        $return['fbtitle'] = !empty($data['ogtitle_'.$params['language']]) ? $data['ogtitle_'.$params['language']] : $params['displayTitle'];
        $return['fbimage'] = !empty($data['ogimage']) ? $data['ogimage'] : $params['systemurl'].'assets/img/fb_image_'.$params['language'].'.png';
        $return['fbdesc'] = $data['ogdesc_'.$params['language']];
    }
    else
    {
        $return['seotitle'] = '';
        $return['seokeyword'] = '';
        $return['seodecription'] = '';
        $return['fburl'] = $current_url;
        $return['fbtype'] = 'website';
        $return['fbtitle'] = $params['displayTitle'];
        $return['fbimage'] = $params['systemurl'].'assets/img/fb_image_'.$params['language'].'.png';
        $return['fbdesc'] = $data['ogdesc_'.$params['language']];
    }
    return $return;
}

add_hook('ClientAreaPage', 1, 'hook_ghostingseo');