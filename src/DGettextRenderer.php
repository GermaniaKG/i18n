<?php
namespace Germania\i18n;



class DGettextRenderer {


    /**
     * Domain name for dgettext
     *
     * @var string
     */
    public $domain;

    /**
     * @var callable
     */
    public $dgettext;


    /**
     * @param string $domain gettext domain
     */
    public function __construct( string $domain )
    {
        $this->setDomain($domain);
        $this->setFn( function( $domain, $msgid ) {
            return dgettext( $domain, $msgid );
        });
    }


    /**
     * @param  string $msgid     'msgid' for dgettext
     * @param  array  $arguments String replacement array
     * @return string
     */
    public function __invoke( string $msgid, array $arguments = array() )
    {
        $result = dgettext( $this->domain, $msgid );

        if (!$arguments):
            return $result;
        endif;


        $needles = array_map(function($needle) {
            return '{' . $needle . '}';
        }, array_keys($arguments));

        return str_replace($needles, array_values($arguments), $result);
    }


    /**
     * @param string $domain gettext domain
     */
    public function setDomain( string $domain ) : self
    {
        $this->domain = $domain;
        return $this;
    }

    public function setFn( callable $fn ) : self
    {
        $this->dgettext = $fn;
        return $this;
    }
}
