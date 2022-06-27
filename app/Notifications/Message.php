<?php

namespace Pterodactyl\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SimpleMessage;

class Message extends MailMessage
{
    public string $line1;
    public string $line2;

    public function line1($text): Message
    {
        $this->line1 = $text;

        return $this;
    }

    public function line2($text): Message
    {
        $this->line2 = $text;

        return $this;
    }

    public function toArray(): array
    {
        return parent::toArray() + [
            'line1' => $this->line1,
            'line2' => $this->line2,
        ];
    }
}
