<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PendingTaskReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tasks;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param \Illuminate\Database\Eloquent\Collection $tasks
     * @return void
     */
    public function __construct(User $user, $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pending Task Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pending-task-reminder',
            with: [
                'user' => $this->user,
                'tasks' => $this->tasks,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
