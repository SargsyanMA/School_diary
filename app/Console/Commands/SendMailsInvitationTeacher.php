<?php

namespace App\Console\Commands;

use App\Mail\MailInvitation;
use App\Mail\MailInvitationTeacher;
use App\User;
use Illuminate\Console\Command;
use \Mail;

class SendMailsInvitationTeacher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendMails:invitation-teacher {--id=} {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send mails with invitations';

    /**
     * Create a new command instance.
	 */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->option('id');
        $email = $this->option('email');

		$teachers = User::query()
			->orderBy('class', 'ASC')
			->orderBy('name', 'ASC')
            ->where('active', 1)
            ->whereIn('role_id', [1,3])
            ->when($id, function($query) use ($id) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('id', $id);
            })
			->get();

		foreach ($teachers as $t) {

			$data = [
				'user' => $t
			];

			try {

                if (!empty($t->passwordClean)) {
                    Mail::to($email ? $email : $t->email)->send(new MailInvitationTeacher($data));
                }
            }
            catch (\Exception $e) {
			    echo $e->getMessage();
            }
		}
    }
}
