<?php

namespace App\Services\Message;

use Illuminate\Session\Store;
use App\Exceptions\Message\UnexpectedMessageTypeException;

class MessageService
{
    /**
     * @var Store
     */
    protected $session;

    /**
     * MessageService constructor.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * @param $type
     * @param $content
     * @throws UnexpectedMessageTypeException
     */
    public function set($type, $content) : void
    {
        $this->session->flash('message', new Message($type, $content));
    }

    /**
     * Détermine si un message a été sauvegardé
     *
     * @return bool
     */
    public function hasOne() : bool
    {
        return $this->session->exists('message');
    }

    /**
     * @return Message
     */
    public function get() : Message
    {
        return $this->session->get('message');
    }
}
