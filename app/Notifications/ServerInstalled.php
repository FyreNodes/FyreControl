<?php

namespace Pterodactyl\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Pterodactyl\Events\Event;
use Illuminate\Container\Container;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pterodactyl\Contracts\Core\ReceivesEvents;
use Illuminate\Contracts\Notifications\Dispatcher;
use Pterodactyl\Models\Server;
use Pterodactyl\Models\User;

class ServerInstalled extends Notification implements /*ShouldQueue,*/ ReceivesEvents
{
    //use Queueable;
    public Server $server;
    public User $user;

    /**
     * Handle a direct call to this notification from the server installed event. This is configured
     * in the event service provider.
     *
     * @param Event $event
     * @throws BindingResolutionException
     */
    public function handle(Event $event): void
    {
        $event->server->loadMissing('user');

        $this->server = $event->server;
        $this->user = $event->server->user;

        // Since we are calling this notification directly from an event listener we need to fire off the dispatcher
        // to send the email now. Don't use send() or you'll end up firing off two different events.
        Container::getInstance()->make(Dispatcher::class)->sendNow($this->user, $this);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return Message
     */
    public function toMail(): Message
    {
        return (new Message())->subject('Your Instance has deployed.')->line1('Your instance '.$this->server->name.' has finished deploying.')->line2('Login now to use your newly created instance.')->view('vendor.notifications.installed');
    }
}
