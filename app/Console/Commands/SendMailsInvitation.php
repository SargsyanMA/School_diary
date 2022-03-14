<?php

namespace App\Console\Commands;

use App\Mail\MailInvitation;
use App\Mail\MailScores;
use App\User;
use Illuminate\Console\Command;
use \Mail;

class SendMailsInvitation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendMails:invitation {role} {--id=} {--grade=} {--email=} {--check=}';

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
        $check = $this->option('check');
        $gradeId = $this->option('grade');

        $role = $this->argument('role', 'parent');

		$users = User::query()
			->orderBy('class', 'ASC')
			->orderBy('name', 'ASC')
            ->when($gradeId, function ($query) use ($gradeId, $role) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query
                    ->where('class', $gradeId)
                    ->where('active', 1)
                    ->where('role_id', $role == 'student' ? 2 : 4);
            })

            ->when($id, function($query) use ($id) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query->where('id', $id);
            }, function ($query) use ($role) {
				/** @var \Illuminate\Database\Eloquent\Builder $query */
				$query
                    ->where('active', 1)
                    ->where('invite', 0)
                    ->where('role_id', $role == 'student' ? 2 : 4);
            })
			->get();

        foreach ($users as $user) {
			$data = [
				'user' => $user,
				'isStudent' => $user->role->name == 'student'
			];
			try {
			    echo $user->email . ' '.$user->name . PHP_EOL;

			    if(!$check) {
                    if (!empty($user->passwordClean)) {
                        Mail::to($email ? $email : $user->email)->send(new MailInvitation($data));
                        Mail::to('uncle.slavik@gmail.com')->send(new MailInvitation($data));
                        $user->invite = 1;
                        $user->save();
                    }
                }
            }
            catch (\Exception $e) {
                echo $e->getMessage();
            }
		}
    }
}
