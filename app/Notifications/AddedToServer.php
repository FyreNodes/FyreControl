<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AddedToServer extends Notification //implements ShouldQueue
{
    //use Queueable;

    /**
     * @var object
     */
    public $server;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $server)
    {
        $this->server = (object) $server;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return Message
     */
    public function toMail($notifiable): Message
    {
        return (new Message())->line1('Hi '.$this->server->user.'. You have been added to a server.')->line2('You have been added as a subuser for the following server, allowing you certain control over the server. Server Name: ' . $this->server->name)->action('Visit Server', url('/server/' . $this->server->uuidShort));
    }
}
