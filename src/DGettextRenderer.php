<?php
namespace Germania\i18n;



class DGettextRenderer {

    /**
     * @var string
     */
    public $domain;


    /**
     * @param string $domain
     */
    public function __construct( $domain )
    {
        $this->domain = $domain;
    }


    /**
     * @param  string $msgid     'msgid' for dgettext
     * @param  array  $arguments String replacement array
     * @return string
     */
    public function __invoke( $msgid, $arguments = array() )
    {
        $result = dgettext( $this->domain, $msgid );

        if (!$arguments):
            return $result;
        endif;

        return str_replace(array_keys($arguments), array_values($arguments), $result);
    }
}
