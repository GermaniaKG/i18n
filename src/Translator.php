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
     * @param string $client_lang   Example: "de"
     * @param string $default_lang  Example: "en"
     */
    public function __construct( string $client_lang, string $default_lang )
    {
        $this->setLanguage($client_lang);
        $this->setDefaultLanguage($default_lang);
    }


    /**
     * @param string $lang
     */
    public function setLanguage( string $lang ) : self
    {
        $this->client_lang = $lang;
        return $this;
    }


    /**
     * @param string $lang
     */
    public function setDefaultLanguage( string $lang ) : self
    {
        $this->default_lang = $lang;
        return $this;
    }


    /**
     * @return string
     */
    public function getLanguage(  )
    {
        return $this->client_lang;
    }


    /**
     * @return string
     */
    public function getDefaultLanguage(  )
    {
        return $this->default_lang;
    }


    /**
     * @param  string[]|mixed  $variable     String or Array with language keys
     * @param  string          $client_lang  Optional: override for this time
     *
     * @return mixed
     *
     * @throws \RuntimeException if $variable array does not contain neither client nor default language keys
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
