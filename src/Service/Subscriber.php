<?php

namespace App\Service;

use App\Entity\Rate;
use App\Entity\Subscription\CurrencySubscription;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Subscriber
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $templating;
    /**
     * @var RouterInterface $router
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Subscriber constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     * @param RouterInterface $router
     * @param UserRepository $userRepository
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $templating,
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->mailer         = $mailer;
        $this->templating     = $templating;
        $this->router         = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $data
     * @param string $subject
     * @param string $from
     * @param string $template
     * @param string $templateContentType
     */
    public function sendEmailToUsers($data, $subject, $from, $template, $templateContentType = 'text/html'): void
    {
        $message = (new \Swift_Message($subject));

        $message
            ->setFrom($from)
            ->setBody($template, $templateContentType);

        if (is_array($data)) {
            foreach ($data as $user) {
                /** @var User $user */
                $message->setTo($user->getEmail());
                $this->mailer->send($message);
            }
        } elseif ($data instanceof User) {
            $message->setTo($data->getEmail());
            $this->mailer->send($message);
        } else {
            $message->setTo($data);
            $this->mailer->send($message);
        }
    }

    /**
     * @param array $lowestRates
     * @param array $highestRates
     * @param string $from
     * @param string $templateContentType
     */
    public function sendRatesUpdatesEmailToActiveUsers(
        array $lowestRates = [],
        array $highestRates = [],
        $from = null,
        $templateContentType = 'text/html'
    ): void {
        if (empty($from)) {
            $from = getenv('MAILER_USER');
        }

        $rates   = $this->formatRatesData($lowestRates, $highestRates);
        $message = (new \Swift_Message('Rates updates'));
        $headers = $message->getHeaders();

        $headers->addTextHeader('List-Unsubscribe', '<' . getenv('MAILER_USER') . '>');
        $message->setFrom($from);

        foreach ($this->userRepository->getActiveUsers() as $user) {
            /** @var User $user */
            $subscriptions = $user->getCurrencySubscriptions();
            $ratesData     = [];

            foreach ($subscriptions as $subscription) {
                /** @var CurrencySubscription $subscription */
                $currencyId = $subscription->getCurrency()->getId();

                if (isset($rates[$currencyId])) {
                    $ratesData[] = $rates[$currencyId];
                }
            }

            if (empty($ratesData)) {
                continue;
            }

            try {
                $profileUrl = $this->router->generate('user_profile', [], UrlGeneratorInterface::ABSOLUTE_URL);
                $template   = $this
                    ->templating
                    ->render('email/rate/updates.html.twig', compact('ratesData', 'profileUrl'));
            } catch (\Twig_Error $exception) {
                break;
            }

            $message
                ->setTo($user->getEmail())
                ->setBody($template, $templateContentType);

            $this->mailer->send($message);
        }
    }

    /**
     * @param array $lowestRates
     * @param array $highestRates
     * @return array
     */
    private function formatRatesData(array $lowestRates = [], array $highestRates): array
    {
        $rates = [];

        foreach ($lowestRates as $lowestRate) {
            /** @var Rate $lowestRate */
            $currencyId = $lowestRate->getCurrency()->getId();

            if (!isset($rates[$currencyId])) {
                $rates[$currencyId] = [];
            }

            $rates[$currencyId]['lowest'] = $lowestRate;

            if (!isset($rates[$currencyId]['currency'])) {
                $rates[$currencyId]['currency'] = $lowestRate->getCurrency();
            }
        }

        foreach ($highestRates as $highestRate) {
            /** @var Rate $highestRate */
            $currencyId = $highestRate->getCurrency()->getId();

            if (!isset($rates[$currencyId])) {
                $rates[$currencyId] = [];
            }

            $rates[$currencyId]['highest'] = $highestRate;

            if (!isset($rates[$currencyId]['currency'])) {
                $rates[$currencyId]['currency'] = $highestRate->getCurrency();
            }
        }

        return $rates;
    }
}