<?php
namespace Germania\i18n;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;


/**
 * Application middleware
 */
class GettextMiddleware
{


    /**
     * @var string
     */
    public $client_lang;

    /**
     * @var array
     */
    public $domains;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $charset = 'UTF-8';

    /**
     * @var LoggerInterface
     */
    public $logger;


    /**
     * @param string               $client_lang Client language, e.g. "en_US"
     * @param string               $domains     gettext domain catalogs
     * @param string               $path        path to locales
     * @param LoggerInterface|null $logger      Optional PSR3-Logger
     */
    public function __construct( $client_lang, $domains, $path, LoggerInterface $logger = null)
    {
        $this->client_lang = $client_lang;
        $this->domains = $domains;
        $this->path = $path;
        $this->logger = $logger ?: new NullLogger;
    }


    /**
     * @param  ServerRequestInterface $request  PSR-7 request object
     * @param  ResponseInterface      $response PSR-7 response object
     * @param  callable               $next     Next middleware
     * @return callable               $next     Next middleware return
     */
    public function __invoke (ServerRequestInterface $request, ResponseInterface $response, callable $next) {

        // s.th. like "en_US"
        //       en: language code, ISO 639-1
        //       US: country code, ISO 3166-1 alpha-2 specification
        $client_language = $this->client_lang;


        //
        // Instruct gettext which locale to use for this session.
        //
        putenv("LANG=" . $client_language);
        setlocale(LC_ALL, $client_language);


        // gettext: The catalog file used to store.
        // the translation messages (MO files) are called "domain".
        $domains = $this->domains;

        // bindtextdomain:
        //   where to find the domain to use;
        //   domain: catalog name without the .mo extension
        //   path:   parent directory in which the "en_US/LC_MESSAGES" resides
        $path = $this->path;

        if (!is_readable( $path )):
            throw new \RuntimeException("Not readable: " . $path);
        endif;

        foreach($domains as $domain):
            bindtextdomain($domain, $path);
            bind_textdomain_codeset($domain, $this->charset );
        endforeach;

        // gettext: which domain to use for ANY SUBSEQUENT CALLs to gettext()
        # textdomain($domain);


        $this->logger->info("gettext configuration", [
            'language' => $client_language,
            'domains' => $domains,
            'path' => $path,
            'charset' => $this->charset
        ]);

        return $next($request, $response);
    }


}
