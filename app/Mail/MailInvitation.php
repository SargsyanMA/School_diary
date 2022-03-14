<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $isStudent;

	/**
	 * Create a new message instance.
	 *
	 * @param array $params
	 */
    public function __construct(array $params)
    {
        $this->user = $params['user'];
        $this->isStudent = $params['isStudent'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
			->subject(' Добро пожаловать на электронный портал школы "Золотое сечение"')
			->view('mail.invitation');
    }
}
