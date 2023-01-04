<?php
namespace Germania\i18n;

use Pimple\Container as PimpleContainer;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Negotiation\LanguageNegotiator;

class ServiceProvider implements ServiceProviderInterface
{




    /**
     * @param PimpleContainer $dic Pimple Instance
     */
    public function register(PimpleContainer $dic)
    {
        $dic['i18n.Config'] = function($dic) {

           $config = [
                'domains' => [ 'app' ],
                'path' => 'locales',
                'available' => [
                    'en_US'
                ],
                'default' => 'en_US',
                'override' => null
            ];

            $dic['i18n.Logger']->debug('Setup i18n.Config', $config);

            return $config;
        };

        $dic['i18n.Logger'] = function($dic) {
           return new NullLogger;
        };

        $dic['i18n.Language.override'] = function($dic) {
            $config = $dic['i18n.Config'];
            return $config['override'];
        };

        $dic['i18n.Language.domains'] = function($dic) {
            $config = $dic['i18n.Config'];
            return $config['domains'];
        };
        $dic['i18n.Language.path'] = function($dic) {
            $config = $dic['i18n.Config'];
            return $config['path'];
        };

        $dic['i18n.Language.available'] = function($dic) {
            $config = $dic['i18n.Config'];
            return $config['available'];
        };

        $dic['i18n.Language.default'] = function($dic) {
            $config = $dic['i18n.Config'];
            return $config['default'];
        };







        // @return string
        $dic['i18n.RequestedLanguage'] = function($dic) {
            if ($override = $dic['i18n.Language.override']):
                return $override;
            endif;

            $default      = $dic['i18n.Language.default'];
            $available    = $dic['i18n.Language.available'];
            $logger       = $dic['i18n.Logger'];
            $header_value = $dic['request']->getHeader( 'Accept-Language' );
            // string
            $accepted_language_factory = new AcceptedLanguageStringFactory( $default, $logger );
            $accepted_language = $accepted_language_factory( $header_value );

            // Negotiation\AcceptLanguage
            $negotiator    = new LanguageNegotiator;
            $best_language = $negotiator->getBest($accepted_language, $available);

            $result_lang = $best_language ? $best_language->getType() : $default;
            $logger->info("Language negotiation", [
                'result' => $result_lang,
                'accepted_language' => $accepted_language,
                'available' => $available,
                'default_fallback' => $default
            ]);

            $normalizer = new LanguageNormalizer;
            return $normalizer($result_lang);
        };




        $dic['i18n.GettextMiddleware'] = function($dic) {
            $client_lang  = $dic['i18n.RequestedLanguage'];
            $domain    = $dic['i18n.Language.domains'];
            $path      = $dic['i18n.Language.path'];
            $logger    = $dic['i18n.Logger'];
            return new GettextMiddleware($client_lang, $domain, $path, $logger);
        };




    }
}
