<?php 
namespace WHMCS\View\Admin\HealthCheck;


class HealthCheckRepository
{
    protected $osChecker = NULL;
    protected $whmcsChecker = NULL;
    protected $curlChecker = NULL;
    protected $httpChecker = NULL;

    const RECOMMENDED_DB_COLLATIONS = "utf8mb4_unicode_ci";
    const MINIMUM_MEMORY_LIMIT = 67108864;
    const RECOMMENDED_MEMORY_LIMIT = 134217728;
    const DEFAULT_MEMORY_LIMIT_FOR_AUTO_UPDATE = self::MINIMUM_MEMORY_LIMIT;

    public function __construct(\WHMCS\Environment\OperatingSystem $osChecker = NULL, \WHMCS\Environment\WHMCS $whmcsChecker = NULL, \WHMCS\Environment\Http $httpChecker = NULL)
    {
        $this->osChecker = (is_null($osChecker) ? new \WHMCS\Environment\OperatingSystem() : $osChecker);
        $this->whmcsChecker = (is_null($whmcsChecker) ? new \WHMCS\Environment\WHMCS() : $whmcsChecker);
        $this->httpChecker = (is_null($httpChecker) ? new \WHMCS\Environment\Http() : $httpChecker);
    }

    protected function buildCheckResults(array $results)
    {
        $healthChecks = new \Illuminate\Support\Collection();
        foreach( $results as $result ) 
        {
            if( !is_null($result) ) 
            {
                $healthChecks->put($result->getName(), $result);
            }

        }
        return $healthChecks->sort(function(HealthCheckResult $a, HealthCheckResult $b)
{
    $logLevelOrders = array( \Psr\Log\LogLevel::DEBUG => 0, \Psr\Log\LogLevel::INFO => 1, \Psr\Log\LogLevel::NOTICE => 2, \Psr\Log\LogLevel::WARNING => 3, \Psr\Log\LogLevel::ERROR => 4, \Psr\Log\LogLevel::CRITICAL => 5, \Psr\Log\LogLevel::ALERT => 6, \Psr\Log\LogLevel::EMERGENCY => 7 );
    if( $a->getSeverityLevel() == $b->getSeverityLevel() ) 
    {
        return 0;
    }

    return ($logLevelOrders[$a->getSeverityLevel()] < $logLevelOrders[$b->getSeverityLevel()] ? 1 : -1);
}

);
    }

    public function keyChecks()
    {
        $healthChecks = array( $this->checkForUpdateVersionAvailable(), $this->getQuickLinks() );
        return $this->buildCheckResults($healthChecks);
    }

    public function nonKeyChecks()
    {
        $healthChecks = array( $this->checkForLaxFilePermissions(), $this->displayWhmcsPaths(), $this->checkForCustomPathUsage(), $this->checkDefaultTemplateUsage(), $this->hasCronRunToday(), ($this->whmcsChecker->shouldPopCronRun() ? $this->hasPopCronRunInLastHour() : null), $this->checkPhpVersion(), $this->checkErrorDisplay(), $this->checkPhpErrorLevels(), $this->checkRequiredPhpExtensions(), $this->checkRecommendedPhpExtensions(), $this->checkRequiredPhpFunctions(), $this->checkPhpMemoryLimit(), $this->checkPhpSessionSupport(), $this->checkPhpTimezone(), $this->checkCurlVersion(), $this->checkForCurlSslSupport(), $this->checkForCurlSecureTlsSupport(), $this->checkForSiteSsl(), $this->checkDbVersion(), $this->checkUpdaterRequirements(), ($this->whmcsChecker->isUsingSMTP() ? $this->checkSMTPMailEncryption() : null) );
        return $this->buildCheckResults($healthChecks);
    }

    protected function displayWhmcsPaths()
    {
        return new HealthCheckResult("customPaths", "WHMCS", \AdminLang::trans("healthCheck.currentPaths"), \Psr\Log\LogLevel::DEBUG, "<p>" . \AdminLang::trans("healthCheck.currentPathsSuccess") . "</p>" . "<ul>" . "<li>" . \AdminLang::trans("healthCheck.currentPathsAttachmentsDirectory", array( ":directory" => \App::getApplicationConfig()->attachments_dir )) . "</li>" . "<li>" . \AdminLang::trans("healthCheck.currentPathsDownloadsDirectory", array( ":directory" => \App::getApplicationConfig()->downloads_dir )) . "</li>" . "<li>" . \AdminLang::trans("healthCheck.currentPathsCompiledTemplatesDirectory", array( ":directory" => \App::getApplicationConfig()->templates_compiledir )) . "</li>" . "<li>" . \AdminLang::trans("healthCheck.currentPathsCronDirectory", array( ":directory" => \App::getApplicationConfig()->crons_dir )) . "</li>" . "<li>" . \AdminLang::trans("healthCheck.currentPathsAdminDirectory", array( ":directory" => ROOTDIR . DIRECTORY_SEPARATOR . \App::getApplicationConfig()->customadminpath )) . "</li>" . "</ul>");
    }

    protected function checkForUpdateVersionAvailable()
    {
        $updater = new \WHMCS\Installer\Update\Updater();
        if( \WHMCS\Version\SemanticVersion::compare($updater->getLatestVersion(), \App::getVersion(), ">") ) 
        {
            $level = \Psr\Log\LogLevel::ERROR;
            $message = \AdminLang::trans("healthCheck.updateAvailable", array( ":version" => $updater->getLatestVersion()->getCasual() )) . "<br>" . \AdminLang::trans("healthCheck.updateAvailableHelp", array( ":href" => "href=\"http://docs.whmcs.com/Upgrading\"" ));
        }
        else
        {
            $level = \Psr\Log\LogLevel::NOTICE;
            $message = \AdminLang::trans("healthCheck.updateNotAvailable");
        }

        return new HealthCheckResult("version", "WHMCS", \App::getVersion()->getCasual(), $level, $message);
    }

    protected function checkForCustomPathUsage()
    {
        $nonCustomPaths = array(  );
        if( !$this->whmcsChecker->isDownloadsPathCustom(\App::getDownloadsDir()) ) 
        {
            $nonCustomPaths[] = "downloads";
        }

        if( !$this->whmcsChecker->isCompiledTemplatesPathCustom(\App::getTemplatesCacheDir()) ) 
        {
            $nonCustomPaths[] = "templates_c";
        }

        if( !$this->whmcsChecker->isAttachmentsPathCustom(\App::getAttachmentsDir()) ) 
        {
            $nonCustomPaths[] = "attachments";
        }

        if( !$this->whmcsChecker->isCronPathCustom(\App::getCronDirectory()) ) 
        {
            $nonCustomPaths[] = "crons";
        }

        $logLevel = (empty($nonCustomPaths) ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::WARNING);
        $body = (empty($nonCustomPaths) ? \AdminLang::trans("healthCheck.usingDefaultPathsSuccess") : \AdminLang::trans("healthCheck.usingDefaultPathsFailure", array( ":nonCustomPaths" => "<li><strong>" . implode("</strong></li><li><strong>", $nonCustomPaths) . "</strong></li>" )));
        return new HealthCheckResult("checkCustomFields", "WHMCS", \AdminLang::trans("healthCheck.usingDefaultPaths"), $logLevel, $body);
    }

    protected function checkForLaxFilePermissions()
    {
        $directoriesNotOwnedByUs = array(  );
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        $bodyHtml = "";
        $directories = array( \App::getDownloadsDir(), \App::getTemplatesCacheDir(), \App::getAttachmentsDir(), \App::getCronDirectory() );
        foreach( $directories as $directory ) 
        {
            if( !$this->osChecker->isOwnedByMe($directory) ) 
            {
                $logLevel = \Psr\Log\LogLevel::WARNING;
                $directoriesNotOwnedByUs[] = $directory;
            }

        }
        if( $this->whmcsChecker->isConfigurationWritable() ) 
        {
            $logLevel = \Psr\Log\LogLevel::ERROR;
            $bodyHtml .= "<p>" . \AdminLang::trans("healthCheck.permissionCheckConfigFileWritable") . "</p>";
        }

        if( $logLevel == \Psr\Log\LogLevel::NOTICE ) 
        {
            return new HealthCheckResult("permissionCheck", "WHMCS", \AdminLang::trans("healthCheck.permissionCheck"), $logLevel, \AdminLang::trans("healthCheck.permissionCheckSuccess"));
        }

        if( !empty($directoriesNotOwnedByUs) ) 
        {
            $bodyHtml .= "<p>" . \AdminLang::trans("healthCheck.permissionCheckUnownedDirectories") . "</p>" . "<ul>" . "<li>" . implode("</li><li>", $directoriesNotOwnedByUs) . "</li>" . "</ul>";
        }

        $bodyHtml .= \AdminLang::trans("healthCheck.permissionCheckUnownedDirectories2", array( ":href" => "href=\"http://docs.whmcs.com/Further_Security_Steps#Secure_the_Writeable_Directories\"" ));
        return new HealthCheckResult("permissionCheck", "WHMCS", \AdminLang::trans("healthCheck.permissionCheck"), $logLevel, $bodyHtml);
    }

    protected function getQuickLinks()
    {
        $updater = new \WHMCS\Installer\Update\Updater();
        $installedVersion = \App::getVersion();
        $latestVersion = $updater->getLatestVersion();
        $changeLogUrl = "http://docs.whmcs.com/Changelog:WHMCS_V%s.%s#Version_%s.%s.%s";
        $recentChangesUrl = "http://docs.whmcs.com/Version_%s.%s.%s_Release_Notes";
        $currentVersionChangeLogUrl = sprintf($changeLogUrl, $installedVersion->getMajor(), $installedVersion->getMinor(), $installedVersion->getMajor(), $installedVersion->getMinor(), $installedVersion->getPatch());
        $currentVersionChangeLogTitle = \AdminLang::trans("healthCheck.currentChangeLogLink", array( ":version" => $installedVersion->getCasual() ));
        $latestVersionChangeLogUrl = sprintf($changeLogUrl, $latestVersion->getMajor(), $latestVersion->getMinor(), $latestVersion->getMajor(), $latestVersion->getMinor(), $latestVersion->getPatch());
        $latestVersionChangeLogTitle = \AdminLang::trans("healthCheck.latestChangeLogLink", array( ":version" => $latestVersion->getCasual() ));
        $currentVersionReleaseNotesUrl = sprintf($recentChangesUrl, $installedVersion->getMajor(), $installedVersion->getMinor(), $installedVersion->getPatch());
        $currentVersionReleaseNotesTitle = \AdminLang::trans("healthCheck.currentReleaseNotesLink", array( ":version" => $installedVersion->getCasual() ));
        $latestVersionReleaseNotesUrl = sprintf($recentChangesUrl, $latestVersion->getMajor(), $latestVersion->getMinor(), $latestVersion->getPatch());
        $latestVersionReleaseNotesTitle = \AdminLang::trans("healthCheck.latestReleaseNotesLink", array( ":version" => $latestVersion->getCasual() ));
        $output = "<ul>" . "<li><a class='autoLinked' href='" . $currentVersionReleaseNotesUrl . "'>" . $currentVersionReleaseNotesTitle . "</a></li>" . "<li><a class='autoLinked' href='" . $currentVersionChangeLogUrl . "'>" . $currentVersionChangeLogTitle . "</a></li>";
        if( \WHMCS\Version\SemanticVersion::compare($latestVersion, $installedVersion, "!=") ) 
        {
            $output .= "</ul>" . "<h2>" . \AdminLang::trans("healthCheck.updatesAreAvailable") . "</h2>" . "<ul>" . "<li><a class='autoLinked' href='" . $latestVersionReleaseNotesUrl . "'>" . $latestVersionReleaseNotesTitle . "</a></li>" . "<li><a class='autoLinked' href='" . $latestVersionChangeLogUrl . "'>" . $latestVersionChangeLogTitle . "</a></li>";
        }

        $output .= "</ul>";
        return new HealthCheckResult("quickLinks", "WHMCS", \AdminLang::trans("healthCheck.quickLinks"), \Psr\Log\LogLevel::DEBUG, $output);
    }

    protected function hasCronRunToday()
    {
        $cronCompletion = $this->whmcsChecker->hasCronCompletedInLastDay();
        return new HealthCheckResult("cron", "WHMCS", \AdminLang::trans("healthCheck.cronJobCompletion"), ($cronCompletion ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::ERROR), "<p>" . (($cronCompletion ? \AdminLang::trans("healthCheck.cronJobCompletionSuccess") : \AdminLang::trans("healthCheck.cronJobCompletionFailure", array( ":href" => "href=\"http://docs.whmcs.com/Cron_Tasks\"" )))) . "</p>");
    }

    protected function hasPopCronRunInLastHour()
    {
        $popCronCompletion = $this->whmcsChecker->hasPopCronRunInLastHour();
        return new HealthCheckResult("popCron", "WHMCS", \AdminLang::trans("healthCheck.popCronTicketImport"), ($popCronCompletion ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::ERROR), "<p>" . (($popCronCompletion ? \AdminLang::trans("healthCheck.popCronTicketImportSuccess") : \AdminLang::trans("healthCheck.popCronTicketImportFailure", array( ":href" => "href=\"http://docs.whmcs.com/Email_Piping#Cron_Piping_Method_2\"" )))) . "</p>");
    }

    protected function checkDefaultTemplateUsage()
    {
        $nonCustomTemplates = array(  );
        if( $this->whmcsChecker->isUsingADefaultOrderFormTemplate(\WHMCS\Config\Setting::getValue("OrderFormTemplate")) ) 
        {
            $nonCustomTemplates[] = \AdminLang::trans("global.cart");
        }

        if( $this->whmcsChecker->isUsingADefaultSystemTemplate(\WHMCS\Config\Setting::getValue("Template")) ) 
        {
            $nonCustomTemplates[] = \AdminLang::trans("global.clientarea");
        }

        $logLevel = (empty($nonCustomTemplates) ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::WARNING);
        $message = (empty($nonCustomTemplates) ? "<p>" . \AdminLang::trans("healthCheck.customTemplatesSuccess") . "</p>" : "<p>" . \AdminLang::trans("healthCheck.customTemplatesFailure") . "</p>" . "<ul>" . "<li><strong>" . implode("</strong></li><li><strong>", $nonCustomTemplates) . "</strong></li>" . "</ul>" . "<p>" . \AdminLang::trans("healthCheck.customTemplatesFailure2", array( ":href" => "href=\"http://docs.whmcs.com/Client_Area_Template_Files#Creating_a_Custom_Template\"" )) . "</p>");
        return new HealthCheckResult("usingCustomTemplates", "WHMCS", \AdminLang::trans("healthCheck.customTemplates"), $logLevel, $message);
    }

    protected function checkDbVersion()
    {
        $minRequiredVersion = "5.1";
        $minRecommendedMySqlVersion = "5.5.3";
        $minRecommendedMariaDbVersion = "5.5";
        $dbEngineName = strtolower(\DI::make("db")->getSqlVersionComment());
        $dbEngineVersion = preg_replace("/[^\\d\\.]*/", "", \DI::make("db")->getSqlVersion());
        $isMariaDb = strpos($dbEngineName, "mariadb") !== false;
        $dbEngineName = ($isMariaDb ? "MariaDB" : "MySQL");
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        $message = \AdminLang::trans("healthCheck.dbVersionIsUpToDate", array( ":dbname" => $dbEngineName, ":currentversion" => $dbEngineVersion ));
        $recommendedVersion = "";
        if( 0 <= version_compare($dbEngineVersion, $minRequiredVersion) ) 
        {
            if( $isMariaDb ) 
            {
                if( version_compare($dbEngineVersion, $minRecommendedMariaDbVersion) < 0 ) 
                {
                    $recommendedVersion = $minRecommendedMariaDbVersion;
                }

            }
            else
            {
                if( version_compare($dbEngineVersion, $minRecommendedMySqlVersion) < 0 ) 
                {
                    $recommendedVersion = $minRecommendedMySqlVersion;
                }

            }

        }
        else
        {
            $logLevel = \Psr\Log\LogLevel::ERROR;
            $message = \AdminLang::trans("healthCheck.dbVersionUpgradeRequired", array( ":dbname" => $dbEngineName, ":currentversion" => $dbEngineVersion ));
        }

        if( !empty($recommendedVersion) ) 
        {
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $message = \AdminLang::trans("healthCheck.dbVersionUpgradeRecommended", array( ":dbname" => $dbEngineName, ":currentversion" => $dbEngineVersion, ":recommendedversion" => $recommendedVersion ));
        }

        return new HealthCheckResult("dbVersion", "DB", \AdminLang::trans("healthCheck.dbVersionTitle"), $logLevel, $message);
    }

    protected function checkDbCollations()
    {
        $dbCollations = $this->whmcsChecker->getDbCollations();
        $allowedCollations = explode(",", strtolower(self::RECOMMENDED_DB_COLLATIONS));
        $collationsText = str_replace(",", " / ", strtolower(self::RECOMMENDED_DB_COLLATIONS));
        $issues = array( "tables" => array(  ), "columns" => array(  ) );
        foreach( $dbCollations["tables"] as $tableCollations ) 
        {
            if( !in_array($tableCollations->collation, $allowedCollations) || 1 < count($dbCollations["tables"]) ) 
            {
                $issues["tables"][] = $tableCollations->collation;
            }

        }
        foreach( $dbCollations["columns"] as $columnCollations ) 
        {
            if( !in_array($columnCollations->collation, $allowedCollations) || 1 < count($dbCollations["columns"]) ) 
            {
                $issues["columns"][] = $columnCollations->collation;
            }

        }
        $messageParams = array( ":collationsText" => $collationsText, ":href" => "href=\"http://docs.whmcs.com/Database_Collations\"" );
        if( empty($issues["tables"]) && empty($issues["columns"]) ) 
        {
            $logLevel = \Psr\Log\LogLevel::NOTICE;
            $message = \AdminLang::trans("healthCheck.dbCollationsOk", $messageParams);
        }
        else
        {
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $message = \AdminLang::trans("healthCheck.dbCollationsNotOk", $messageParams);
        }

        return new HealthCheckResult("dbCollations", "DB", \AdminLang::trans("healthCheck.dbCollationsTitle"), $logLevel, $message);
    }

    protected function checkPhpVersion()
    {
        $majorMinor = PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;
        $body = "<p>";
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        if( \WHMCS\Environment\Php::isSupportedByWhmcs(PHP_VERSION) ) 
        {
            $body .= \AdminLang::trans("healthCheck.phpVersionWhmcsSupported", array( ":version" => PHP_VERSION ));
        }
        else
        {
            $body .= \AdminLang::trans("healthCheck.phpVersionWhmcsUnsupported", array( ":version" => PHP_VERSION ));
            $logLevel = \Psr\Log\LogLevel::ERROR;
        }
        return new HealthCheckResult("phpVersion", "PHP", \AdminLang::trans("healthCheck.phpVersion"), $logLevel, $body);
    }

    protected function checkRequiredPhpExtensions()
    {
        $extensions = array( "curl", "gd", "imap", "ioncube loader", "json", "pdo", "pdo_mysql", "xml" );
        if( version_compare(PHP_VERSION, "7.0.0", "<") ) 
        {
            $extensions[] = "mysql";
        }

        $missingExtensions = array(  );
        foreach( $extensions as $extension ) 
        {
            if( !\WHMCS\Environment\Php::hasExtension($extension) ) 
            {
                $missingExtensions[] = $extension;
            }

        }
        $logLevel = (0 < count($missingExtensions) ? \Psr\Log\LogLevel::ERROR : \Psr\Log\LogLevel::NOTICE);
        $message = (0 < count($missingExtensions) ? "<p>" . \AdminLang::trans("healthCheck.requiredPhpExtensionsFailure") . "</p>" . "<ul>" . "<li><strong>" . implode("</strong></li><li><strong>", $missingExtensions) . "</strong></li>" . "</ul>" . "<p>" . \AdminLang::trans("healthCheck.requiredPhpExtensionsFailure2", array( ":href" => "href=\"http://docs.whmcs.com/System_Requirements\"" )) . "</p>" : "<p>" . \AdminLang::trans("healthCheck.requiredPhpExtensionsSuccess") . "</p>");
        return new HealthCheckResult("requiredPhpExtensions", "PHP", \AdminLang::trans("healthCheck.requiredPhpExtensions"), $logLevel, $message);
    }

    protected function checkRecommendedPhpExtensions()
    {
        $extensions = array( "iconv", "mbstring", "soap", "xmlrpc", "zip" );
        $missingExtensions = array(  );
        foreach( $extensions as $extension ) 
        {
            if( !\WHMCS\Environment\Php::hasExtension($extension) ) 
            {
                $missingExtensions[] = $extension;
            }

        }
        $logLevel = (0 < count($missingExtensions) ? \Psr\Log\LogLevel::WARNING : \Psr\Log\LogLevel::NOTICE);
        $message = (0 < count($missingExtensions) ? "<p>" . \AdminLang::trans("healthCheck.recommendedPhpExtensionsFailure") . "</p>" . "<ul>" . "<li><strong>" . implode("</strong></li><li><strong>", $missingExtensions) . "</strong></li>" . "</ul>" . "<p>" . \AdminLang::trans("healthCheck.recommendedPhpExtensionsFailure2", array( ":href" => "href=\"http://docs.whmcs.com/System_Requirements\"" )) . "</p>" : "<p>" . \AdminLang::trans("healthCheck.recommendedPhpExtensionsSuccess") . "</p>");
        return new HealthCheckResult("recommendedPhpExtensions", "PHP", \AdminLang::trans("healthCheck.recommendedPhpExtensions"), $logLevel, $message);
    }

    protected function checkRequiredPhpFunctions()
    {
        $functions = array( "base64_decode", "copy", "curl_exec", "escapeshellcmd", "file_get_contents", "file_put_contents", "fclose", "fopen", "fsockopen", "fwrite", "ini_get", "ini_set", "is_readable", "is_writable", "readfile", "preg_match_all", "print_r", "set_time_limit", "sscanf", "tempnam", "touch", "unlink" );
        $missingFunctions = array(  );
        foreach( $functions as $function ) 
        {
            if( !\WHMCS\Environment\Php::functionEnabled($function) ) 
            {
                $missingFunctions[] = $function;
            }

        }
        $logLevel = (0 < count($missingFunctions) ? \Psr\Log\LogLevel::ERROR : \Psr\Log\LogLevel::NOTICE);
        $message = (0 < count($missingFunctions) ? "<p>" . \AdminLang::trans("healthCheck.requiredPhpFunctionsFailure") . "</p>" . "<ul>" . "<li><strong>" . implode("</strong></li><li><strong>", $missingFunctions) . "</strong></li>" . "</ul>" . "<p>" . \AdminLang::trans("healthCheck.requiredPhpFunctionsFailure2") . "</p>" : "<p>" . \AdminLang::trans("healthCheck.requiredPhpFunctionsSuccess") . "</p>");
        return new HealthCheckResult("requiredPhpFunctions", "PHP", \AdminLang::trans("healthCheck.requiredPhpFunctions"), $logLevel, $message);
    }

    protected function checkErrorDisplay()
    {
        $displayErrors = $this->whmcsChecker->isDisplayingErrors(\WHMCS\Config\Setting::getValue("DisplayErrors"), \App::getApplicationConfig()->display_errors);
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        $bodyHtml = \AdminLang::trans("healthCheck.errorDisplaySuccess");
        if( $displayErrors ) 
        {
            $logLevel = \Psr\Log\LogLevel::ERROR;
            $bodyHtml = \AdminLang::trans("healthCheck.errorDisplayFailure", array( ":href" => "href=\"http://docs.whmcs.com/Enabling_Error_Reporting\"" ));
        }

        return new HealthCheckResult("errorDisplay", "PHP", \AdminLang::trans("healthCheck.errorDisplay"), $logLevel, $bodyHtml);
    }

    protected function checkPhpErrorLevels()
    {
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        $bodyHtml = \AdminLang::trans("healthCheck.errorLevelsSuccess");
        $displayWarning = \WHMCS\Environment\Php::hasErrorLevelEnabled(error_reporting(), E_WARNING);
        $displayNotice = \WHMCS\Environment\Php::hasErrorLevelEnabled(error_reporting(), E_NOTICE);
        if( $displayWarning || $displayNotice ) 
        {
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $bodyHtml = \AdminLang::trans("healthCheck.errorLevelsFailure", array( ":href" => "href=\"http://docs.whmcs.com/Enabling_Error_Reporting#If_you_are_unable_to_access_the_Admin_Area\"" ));
        }

        return new HealthCheckResult("errorLevels", "PHP", \AdminLang::trans("healthCheck.errorLevels"), $logLevel, $bodyHtml);
    }

    protected function checkPhpMemoryLimit()
    {
        $memoryLimit = \WHMCS\Environment\Php::getPhpMemoryLimitInBytes();
        switch( true ) 
        {
            case 0 <= $memoryLimit && $memoryLimit < self::MINIMUM_MEMORY_LIMIT:
                $logLevel = \Psr\Log\LogLevel::ERROR;
                $message = \AdminLang::trans("healthCheck.phpMemoryTooLow", array( ":memory_limit" => ini_get("memory_limit"), ":href" => "href=\"http://php.net/manual/en/ini.core.php#ini.memory-limit\"" ));
                break;
            case self::MINIMUM_MEMORY_LIMIT <= $memoryLimit && $memoryLimit < self::RECOMMENDED_MEMORY_LIMIT:
                $logLevel = \Psr\Log\LogLevel::WARNING;
                $message = \AdminLang::trans("healthCheck.phpMemoryLow", array( ":memory_limit" => ini_get("memory_limit"), ":href" => "href=\"http://php.net/manual/en/ini.core.php#ini.memory-limit\"" ));
                break;
            default:
                $logLevel = \Psr\Log\LogLevel::NOTICE;
                $message = \AdminLang::trans("healthCheck.phpMemorySuccess", array( ":memory_limit" => ini_get("memory_limit") ));
        }
        return new HealthCheckResult("phpMemoryLimit", "PHP", \AdminLang::trans("healthCheck.phpMemory"), $logLevel, $message);
    }

    protected function checkCurlVersion()
    {
        $curlVersion = curl_version();
        $curlVersionIsGood = \WHMCS\Environment\Curl::hasKnownGoodVersion($curlVersion);
        $message = \AdminLang::trans("healthCheck.curlCurrentMessage", array( ":version" => $curlVersion["version"] ));
        if( $curlVersionIsGood ) 
        {
            $logLevel = \Psr\Log\LogLevel::NOTICE;
            $message .= " " . \AdminLang::trans("healthCheck.curlCurrentMessageSuccess");
        }
        else
        {
            $link = "http://curl.haxx.se/changes.html";
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $message .= " " . \AdminLang::trans("healthCheck.curlNotSecure", array( ":link" => $link )) . " " . \AdminLang::trans("healthCheck.curlNotSecureAdvice", array( ":last_bad_version" => \WHMCS\Environment\Curl::LAST_BAD_VERSION ));
        }

        return new HealthCheckResult("installedCurlVersion", "PHP", \AdminLang::trans("healthCheck.installedCurlVersion"), $logLevel, $message);
    }

    protected function checkForCurlSslSupport()
    {
        $curlHasSsl = \WHMCS\Environment\Curl::hasSslSupport(curl_version());
        return new HealthCheckResult("curlSSL", "WHMCS", \AdminLang::trans("healthCheck.curlSslSupport"), ($curlHasSsl ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::ERROR), "<p>" . (($curlHasSsl ? \AdminLang::trans("healthCheck.curlSslSupportSuccess") : \AdminLang::trans("healthCheck.curlSslSupportFailure", array( ":href" => "http://docs.whmcs.com/System_Requirements" )))) . "</p>");
    }

    protected function checkForCurlSecureTlsSupport()
    {
        $curlHasSecureTls = \WHMCS\Environment\Curl::hasSecureTlsSupport(curl_version());
        return new HealthCheckResult("curlSecureTLS", "WHMCS", \AdminLang::trans("healthCheck.curlSecureTlsSupport"), ($curlHasSecureTls ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::WARNING), "<p>" . (($curlHasSecureTls ? \AdminLang::trans("healthCheck.curlSecureTlsSupportSuccess") : \AdminLang::trans("healthCheck.curlSecureTlsSupportFailure", array( ":href" => "http://docs.whmcs.com/System_Requirements" )))) . "</p>");
    }

    protected function checkPhpSessionSupport()
    {
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        $body = "<p>";
        $customSessionSavePathIsWritable = false;
        $hasSessionSupport = \WHMCS\Environment\Php::hasExtension("session");
        $sessionAutoStartEnabled = \WHMCS\Environment\Php::isSessionAutoStartEnabled();
        $sessionPath = (string) ini_get("session.save_path");
        $hasCustomSessionSavePath = $customSessionSavePathIsWritable = false;
        if( 0 < strlen($sessionPath) ) 
        {
            $hasCustomSessionSavePath = true;
            $customSessionSavePathIsWritable = \WHMCS\Environment\Php::isSessionSavePathWritable();
        }

        if( $hasSessionSupport ) 
        {
            $body .= \AdminLang::trans("healthCheck.phpSessionSupportEnabled");
        }
        else
        {
            $logLevel = \Psr\Log\LogLevel::ERROR;
            $body .= \AdminLang::trans("healthCheck.phpSessionSupportDisabled");
        }

        $body .= "</p><p>";
        if( $sessionAutoStartEnabled ) 
        {
            $logLevel = \Psr\Log\LogLevel::ERROR;
            $body .= \AdminLang::trans("healthCheck.phpSessionSupportAutoStartEnabled");
        }
        else
        {
            $body .= \AdminLang::trans("healthCheck.phpSessionSupportAutoStartDisabled");
        }

        $body .= "</p>";
        if( $hasCustomSessionSavePath ) 
        {
            $body .= "<p>";
            if( $customSessionSavePathIsWritable ) 
            {
                $body .= \AdminLang::trans("healthCheck.phpSessionSupportSavePathIsWritable", array( ":path" => $sessionPath ));
            }
            else
            {
                $logLevel = \Psr\Log\LogLevel::ERROR;
                $body .= \AdminLang::trans("healthCheck.phpSessionSupportSavePathIsNotWritable", array( ":path" => $sessionPath ));
            }

            $body .= "</p>";
        }

        return new HealthCheckResult("sessionSupport", "PHP", \AdminLang::trans("healthCheck.phpSessionSupport"), $logLevel, $body);
    }

    protected function checkPhpTimezone()
    {
        $tzValid = \WHMCS\Environment\Php::hasValidTimezone();
        return new HealthCheckResult("phpSettings", "PHP", \AdminLang::trans("healthCheck.phpTimezone"), ($tzValid ? \Psr\Log\LogLevel::NOTICE : \Psr\Log\LogLevel::ERROR), ($tzValid ? \AdminLang::trans("healthCheck.phpTimezoneOk") : \AdminLang::trans("healthCheck.phpTimezoneNotSet")));
    }

    protected function checkForSiteSsl()
    {
        $title = "Website SSL";
        $sslIsRecommended = \AdminLang::trans("healthCheck.sslIsRecommended");
        if( !$this->httpChecker->siteIsConfiguredForSsl() ) 
        {
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $noSSLWarning = \AdminLang::trans("healthCheck.sslNotConfigured", array( ":url" => \WHMCS\Config\Setting::getValue("SystemURL") ));
            $body = $noSSLWarning . "  " . $sslIsRecommended;
        }
        else
        {
            if( !$this->httpChecker->siteHasVerifiedSslCert() ) 
            {
                $site = \App::getSystemURL();
                $logLevel = \Psr\Log\LogLevel::WARNING;
                $caNotDetectedWarning = \AdminLang::trans("healthCheck.caSslNotDetected", array( ":site" => $site ));
                $body = $caNotDetectedWarning . "  " . $sslIsRecommended;
            }
            else
            {
                $logLevel = \Psr\Log\LogLevel::NOTICE;
                $body = \AdminLang::trans("healthCheck.caSslDetectedOk");
            }

        }

        return new HealthCheckResult("siteSslSupport", "HTTP", $title, $logLevel, $body);
    }

    protected function checkSMTPMailEncryption()
    {
        $title = \AdminLang::trans("healthCheck.emailEncryption");
        if( $this->whmcsChecker->isUsingEncryptedEmailDelivery(\WHMCS\Config\Setting::getValue("SMTPSSL")) ) 
        {
            $logLevel = \Psr\Log\LogLevel::NOTICE;
            $body = \AdminLang::trans("healthCheck.emailEncryptionSuccess");
        }
        else
        {
            $logLevel = \Psr\Log\LogLevel::WARNING;
            $body = \AdminLang::trans("healthCheck.emailEncryptionWarning");
        }

        return new HealthCheckResult("SMTPMailEncryption", "WHMCS", $title, $logLevel, $body);
    }

    public function checkUpdaterRequirements($memoryLimitRequired = self::DEFAULT_MEMORY_LIMIT_FOR_AUTO_UPDATE, \WHMCS\Version\SemanticVersion $updateVersion = NULL)
    {
        $title = \AdminLang::trans("healthCheck.updaterTitle");
        $body = array(  );
        $logLevel = \Psr\Log\LogLevel::NOTICE;
        if( $logLevel == \Psr\Log\LogLevel::NOTICE ) 
        {
            $body[] = \AdminLang::trans("healthCheck.updaterSuccess");
        }

        return new HealthCheckResult("CheckUpdaterRequirements", "WHMCS", $title, $logLevel, "<ul><li>" . implode("</li><li>", $body) . "</li></ul>");
    }

}


