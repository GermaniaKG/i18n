<?php
namespace Germania\i18n;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;


/**
 * Application middleware
 */
class GettextMiddleware implements MiddlewareInterface
{

    use LoggerAwareTrait;

    /**
     * @var string
     */
    public $locale;

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
     * @param string               $locale      Default locale, e.g. "en_US"
     * @param iterable             $domains     gettext domain catalogs
     * @param string               $path        Locales path where the "en_US/LC_MESSAGES" reside
     * @param LoggerInterface|null $logger      Optional: PSR3-Logger
     */
    public function __construct( string $locale, iterable $domains, string $path, LoggerInterface $logger = null)
    {
        $this->setLocale($locale);
        $this->setDomains($domains);
        $this->setPath($path);
        $this->setLogger($logger ?: new NullLogger);
    }



    /**
     * PSR-15 Single Pass
     *
     * @param  ServerRequestInterface  $request Server request instance
     * @param  RequestHandlerInterface $handler Request handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
      $this->setupGettext();
      return $handler->handle($request);
    }



    /**
     * PSR-7 Double Pass
     *
     * @param  ServerRequestInterface $request  PSR-7 request object
     * @param  ResponseInterface      $response PSR-7 response object
     * @param  callable               $next     Next middleware
     * @return callable               $next     Next middleware return
     */
    public function __invoke (ServerRequestInterface $request, ResponseInterface $response, callable $next) {

        $this->setupGettext();
        return $next($request, $response);
    }


    public function setupGettext()
    {

        //
        // Instruct gettext which locale to use for this session.
        //
        putenv("LANG=" . $this->locale);
        setlocale(LC_ALL, $this->locale);


        // gettext:
        //   The catalog file used to store.
        //   The translation messages (MO files) are called "domain".

        // bindtextdomain:
        //   Where to find the domain to use;
        //   domain: catalog name without the .mo extension
        //   path:   parent directory in which the "en_US/LC_MESSAGES" resides

        foreach($this->domains as $domain):
            bindtextdomain($domain, $this->path);
            bind_textdomain_codeset($domain, $this->charset );
        endforeach;

        // gettext: which domain to use for ANY SUBSEQUENT CALLs to gettext()
        # textdomain($domain);


        $this->logger->info("gettext configuration", [
            'locale' => $this->locale,
            'domains' => $this->domains,
            'path' => $this->path,
            'charset' => $this->charset
        ]);
    }



    /**
     * @param string $locale Client locale string, e.g. `en_GB`
     */
    public function setLocale( string $locale ) : self
    {
        $this->locale = $locale;
        return $this;
    }


    /**
     * @param string $path  Path to locales directory
     *
     * @throws  \RuntimeException if `$path` does not exist or is not readable
     */
    public function setPath( string $path ) : self
    {
        if (!is_dir($path) or !is_readable( $path )):
            throw new \RuntimeException("Not readable: " . $path);
        endif;

        $this->path = $path;
        return $this;
    }


    /**
     * @param iterable $domains Domains
     */
    public function setDomains( iterable $domains ) : self
    {
        $this->domains = $domains;
        return $this;
    }


    /**
     * @param string $charset
     */
    public function setCharset( string $charset ) : self
    {
        $this->charset = $charset;
        return $this;
    }


}
