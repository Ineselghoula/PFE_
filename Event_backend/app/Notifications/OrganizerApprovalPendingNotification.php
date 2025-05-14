<?php 
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Iluuminate\Contracts\Quene\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrganizerApprovalPendingNotification extends Notification{
    use Queueable;
    public function __construct(){

    }

    public function via ($notifiable){
        return ['mail'];
    }

    public function toMail ($notifiable)
    {
        return (new MailMessage)
         ->subject('Votre compte Planova organisateur est en attente d\'approbation')
         ->line('Merci de vous etre inscrit en tant qu\'organisateur.')
         ->line('Votre compte est en attende d\'approbation par un admisnistrateur de Planova')
         ->line('Vous recervez une notificarion une fois que votre compte sera approuvé')
         ->line('Merci de votre patience');
    }
}