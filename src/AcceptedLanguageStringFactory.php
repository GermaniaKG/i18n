<?php
namespace Germania\i18n;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\Log\LogLevel;
use Psr\Log\LoggerAwareTrait;

class AcceptedLanguageStringFactory
{

    use LoggerAwareTrait;

    /**
     * @var string
     */
    public $default;


    /**
     * @var LoggerInterface
     */
    public $logger;



    /**
     * @var string Psr\Log\LogLevel
     */
    public $warning_loglevel = LogLevel::NOTICE;

    /**
     * @var string Psr\Log\LogLevel
     */
    public $success_loglevel = LogLevel::INFO;


    /**
     * @param string               $default Default language
     * @param LoggerInterface|null $logger  Optional PSR-3 Logger
     */
    public function __construct(string $default, LoggerInterface $logger = null )
    {
        $this->setDefault($default);
        $this->setLogger($logger ?: new NullLogger);
    }

    /**
     * @param  string|sting[] $header_value Header values
     * @return string
     */
    public function __invoke( $header_value )
    {
        if (empty($header_value)):
            $this->logger->log($this->warning_loglevel, "Header value for accepted language is empty, return default", [
                'language' => $this->default
            ]);
            return $this->default;

        elseif (!is_array( $header_value )
        and !is_string( $header_value)):
            $this->logger->log($this->warning_loglevel, "Expected string or array header values. Return default", [
                'language' => $this->default
            ]);
            return $this->default;
        endif;


        $result = is_array( $header_value )
        ? array_shift($header_value)
        : $header_value;


        $this->logger->log($this->success_loglevel, "Extracted accepted language from header value", [
            'language' => $result
        ]);

        return $result;
    }


    public function setDefault( string $default ) : self
    {
        $this->default = $default;
        return $this;
    }
}
