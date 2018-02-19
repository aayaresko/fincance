<?php

namespace App\Service;



class Subscriber
{
    const USERS = [
        'aayaresko@gmail.com',
        //'evyaresko@gmail.com'
    ];

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Subscriber constructor.
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public function getActiveUsers()
    {
        return self::USERS;
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
            foreach ($data as $item) {
                $message->setTo($item);
                $this->mailer->send($message);
            }
        } else {
            $message->setTo($data);
            $this->mailer->send($message);
        }
    }
}