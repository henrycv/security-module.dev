<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * When visiting the homepage, this listener redirects the user to the most
 * appropriate localized version according to the browser settings.
 *
 * See http://symfony.com/doc/current/components/http_kernel/introduction.html#the-kernel-request-event
 *
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class RedirectToPreferredLocaleListener
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * List of supported locales.
     *
     * @var string[]
     */
    private $locales = array();

    /**
     * List of supported locales.
     *
     * @var string[]
     */
    private $localesString = array();

    /**
     * @var string
     */
    private $defaultLocale = '';

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param string $locales Supported locales separated by '|'
     * @param string|null $defaultLocale
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, $translator, $locales, $defaultLocale = null)
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;

        $this->locales = explode('|', trim($locales));
        $this->localesString = $locales;
        if (empty($this->locales)) {
            throw new \UnexpectedValueException('The list of supported locales must not be empty.');
        }

        $this->defaultLocale = $defaultLocale ?: $this->locales[0];

        if (!in_array($this->defaultLocale, $this->locales)) {
            throw new \UnexpectedValueException(sprintf('The default locale ("%s") must be one of "%s".', $this->defaultLocale, $locales));
        }

        // Add the default locale at the first position of the array,
        // because Symfony\HttpFoundation\Request::getPreferredLanguage
        // returns the first element when no an appropriate language is found
        array_unshift($this->locales, $this->defaultLocale);
        $this->locales = array_unique($this->locales);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $hasLocaleInUrl = preg_match(
            '/\/(es|en|fr|de|cs|nl|ru|uk|ro|pt_BR|pl|it|ja|id|ca)$' .
                '|\/(es|en|fr|de|cs|nl|ru|uk|ro|pt_BR|pl|it|ja|id|ca)\/.*/',
            $request->getPathInfo()
        );

        if ($hasLocaleInUrl) {
            $preferredLanguage = $request->getLocale();
            $request->getSession()->set('_locale', $request->getLocale());
        } else {
            $preferredLanguage = $request->getSession()->get('_locale');
        }
        $request->setLocale($preferredLanguage);
        $request->getPreferredLanguage($this->locales);

        $this->translator->setLocale($preferredLanguage);

        // Ignore sub-requests and all URLs but the homepage
        if (!$event->isMasterRequest() || '/' !== $request->getPathInfo()) {
            return;
        }
        // Ignore requests from referrers with the same HTTP host in order to prevent
        // changing language for users who possibly already selected it for this application.
        if (0 === stripos($request->headers->get('referer'), $request->getSchemeAndHttpHost())) {
            return;
        }

        $languageOnSession = $request->getSession()->get('_locale');
        if ($languageOnSession) {
            $preferredLanguage = $languageOnSession;
        } else {
            $preferredLanguage = $request->getPreferredLanguage($this->locales);
        }

        if ($preferredLanguage !== $this->defaultLocale) {
            $response = new RedirectResponse($this->urlGenerator->generate('homepage', array('_locale' => $preferredLanguage)));
            $event->setResponse($response);
        }
    }
}
