<?php
namespace Germania\i18n;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AcceptedLanguageStringFactory
{

    /**
     * @var string
     */
    public $default;


    /**
     * @var LoggerInterface
     */
    public $logger;


    /**
     * @param string               $default Default language
     * @param LoggerInterface|null $logger  Optional PSR-3 Logger
     */
    public function __construct($default, LoggerInterface $logger = null )
    {
        $this->default = $default;
        $this->logger = $logger ?: new NullLogger;
    }

    /**
     * @return string
     */
    public function __invoke( $header_value )
    {
        if (empty($header_value)):
            $this->logger->info( "Header value for accepted language is empty, return default", [
                'language' => $this->default
            ]);
            return $this->default;

        elseif (!is_array( $header_value )
        and !is_string( $header_value)):
            $this->logger->notice( "Invalid Header value for accepted language, neither string nor array. Return default", [
                'language' => $this->default
            ]);
            return $this->default;

        endif;

        $result = is_array( $header_value ) ? array_shift($header_value) : $header_value;
        $this->logger->info( "Extracted accepted language from header value", [
            'language' => $result
        ]);

        return $result;
    }
}
