<?php
namespace Germania\i18n;

class LanguageNormalizer
{
    public function __invoke( $language)
    {
        $language = str_replace("-", "_", $language);

        $parts = explode("_", $language);

        if (count($parts) == 1):
            return $language;
        endif;

        $parts[1] = strtoupper( $parts[1] );

        $language = implode("_", $parts);
        return $language;
    }
}
