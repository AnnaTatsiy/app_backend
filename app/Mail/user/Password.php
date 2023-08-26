<?php

namespace App\Mail\user;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Password extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $password;
    public $surname;
    public $patronymic;
    public $name;
    public $birth;
    public $email;
    public $number;
    public $registration;
    public $passport;
    public $header;

    /**
     * Create a new message instance.
     */
    public function __construct($surname, $name, $patronymic, $password, $birth, $email, $passport ,$number, $registration, $header)
    {
        $this->password = $password;
        $this->surname = $surname;
        $this->patronymic = $patronymic;
        $this->name = $name;
        $this->birth = date("d.m.Y", strtotime($birth));
        $this->email = $email;
        $this->number = $number;
        $this->registration = $registration;
        $this->passport = $passport;
        $this->header = $header;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Проверка персональных данных и получение пароля для входа в личный кабинет',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.password',
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
