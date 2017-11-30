<?php
namespace Germania\i18n;

class Translator {


    /**
     * @var string
     */
    public $client_lang;


    /**
     * @var string
     */
    public $default_lang;


    /**
     * @param string $client_lang
     * @param string $default_lang
     */
    public function __construct( $client_lang, $default_lang )
    {
        $this->client_lang = $client_lang;
        $this->default_lang = $default_lang;
    }


    /**
     * @param string $lang
     */
    public function setLanguage( $lang )
    {
        $this->client_lang = $lang;
    }


    /**
     * @return string $lang
     */
    public function getLanguage(  )
    {
        return $this->client_lang;
    }


    /**
     * @param  string[]|string $variable
     * @param  string          $client_lang Optional: override for this time
     *
     * @return mixed
     */
    public function __invoke( $variable, $client_lang = null )
    {
        if (!is_array($variable)):
            return $variable;
        endif;

        $client_lang  = strtolower($client_lang ?: $this->client_lang);
        $default_lang = strtolower($this->default_lang);

        if (isset($variable[ $client_lang ])):
            return $variable[ $client_lang ];
        elseif (isset($variable[ $default_lang ])):
            return $variable[ $default_lang ];
        else:
            throw new \RuntimeException("Could not find neither '$client_lang' nor '$default_lang' in variable.");
        endif;

    }
}
