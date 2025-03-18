<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCode extends Notification implements ShouldQueue
{
    use Queueable;

    private $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vérifiez votre e-mail - Planova')
            ->greeting('Bonsoir ' . $notifiable->name . '!')
            ->line('Bienvenue sur Planova ! Veuillez utiliser le code de vérification ci-dessous pour vérifier votre adresse e-mail.')
            ->line('Votre code de vérification est : ' . $this->code)
            ->line('Ce code expirera dans 15 minutes.')
            ->line('Si vous n\'avez pas créé de compte, aucune action n\'est requise.');
    }

    public function toArray($notifiable)
    {
        return [
            'code' => $this->code
        ];
    }
}