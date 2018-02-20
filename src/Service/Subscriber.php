<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class Subscriber
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Subscriber constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param UserRepository $userRepository
     */
    public function __construct(\Swift_Mailer $mailer, UserRepository $userRepository)
    {
        $this->mailer         = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @return User[]
     */
    public function getActiveUsers()
    {
        return $this->userRepository->getActiveUsers();
    }

    /**
     * @param mixed $data
     * @param string $subject
     * @param string $from
     * @param string $template
     * @param string $templateContentType
     */
    public function sendEmailToUsers($data, $subject, $from, $template, $templateContentType = 'text/html')
    {
        $message = (new \Swift_Message($subject));
        $message->setFrom($from);
        $message->setBody($template, $templateContentType);
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
}