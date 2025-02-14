<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCompletedNotification extends Notification
{
    use Queueable;

    protected $task;

    public function __construct($task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tarefa Concluída')
            ->line('A tarefa "' . $this->task->title . '" foi concluída.')
            ->line('Descrição: ' . $this->task->description)
            ->action('Ver Projeto', url('/projects/' . $this->task->project_id))
            ->line('Obrigado por usar nosso sistema!');
    }
}