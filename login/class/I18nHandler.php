<?php
/**
 * PHPLogin\I18nHandler
 */
namespace PHPLogin;

/**
 * Handling for internationalization
 *
 */
class I18nHandler extends AppConfig
{
    function build(String $language) {
        require $this->base_dir . "/i18n/$language.php";
        return $i18n;
    }
}
