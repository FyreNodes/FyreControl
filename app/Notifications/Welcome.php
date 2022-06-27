<?php

namespace Pterodactyl\Notifications;

use Pterodactyl\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class Welcome extends Notification implements ShouldQueue
{
    use Queueable;
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
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
     * @return Message
     */
    public function toMail(): Message
    {
        return (new Message())->subject('Welcome to FyreNodes!')->greeting('Greetings '.$this->user->name_first.'!')->line1('Your FyreID has been created.')->line2('To deploy a server, head to the store and select the plan you want. Then fill out the details and subscribe, then you are good to go.')->view('vendor.notifications.welcome');
    }
}
