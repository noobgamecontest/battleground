<?php

namespace App\Services\Message;

use App\Exceptions\Message\UnexpectedMessageTypeException;

class Message
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $content;

    /**
     * Message constructor.
     * @param string $type
     * @param string $content
     * @throws UnexpectedMessageTypeException
     */
    public function __construct(string $type, string $content)
    {
        $this->setType($type);
        $this->content = $content;
    }

    /**
     * @param string $type
     * @throws UnexpectedMessageTypeException
     */
    protected function setType(string $type) : void
    {
        if (! in_array($type, ['success', 'danger', 'info', 'warning'])) {
            throw new UnexpectedMessageTypeException($type);
        }

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
