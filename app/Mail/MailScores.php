<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MailScores extends Mailable
{
    use Queueable, SerializesModels;

    public $scores;
    public $attendance;
    public $weightedAverage;
    public $totalAverage;
    public $scorePeriod;
    public $schedule;
    public $date;
    public $student;
    public $period;
    public $homeworks;

	/**
	 * Create a new message instance.
	 *
	 * @param array $params
	 */
    public function __construct(array $params)
    {
        $this->scores = $params['scores'];
        $this->schedule = $params['schedule'];
        $this->attendance =  $params['attendance'];
        $this->weightedAverage = $params['weightedAverage'];
        $this->totalAverage = $params['totalAverage'];
        $this->scorePeriod = $params['scorePeriod'];
        $this->date = $params['date'];
        $this->student = $params['student'];
        $this->total = $params['total'];
        $this->period = $params['period'];
        $this->homeworks = $params['homeworks'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->total) {
            return $this
                ->subject('Итоговые оценки')
                ->view('mail.scores-total');
        }
        else {
            return $this
                ->subject('Оценки за период: ' . Carbon::parse($this->date[0])->format('d.m.Y') . ' - ' . Carbon::parse($this->date[1])->format('d.m.Y') )
                ->view('mail.scores');
        }
    }
}
