<?php

namespace App\Jobs;

use App\Mail\PendingTaskReminder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPendingTaskReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tomorrow = Carbon::now()->addDay()->startOfDay();

        // Find users with tasks due within the next day
        $users = User::whereHas('tasks', function ($query) use ($tomorrow) {
            $query->where('due_date', '<=', $tomorrow)
                ->where('status', 'pending');
        })->get();

        // Queue email to each user
        foreach ($users as $user) {
            $tasks = $user->tasks()
                ->where('due_date', '<=', $tomorrow)
                ->where('status', 'pending')
                ->get();

            if ($tasks->isNotEmpty()) {
                Mail::to($user->email)->send(new PendingTaskReminder($user, $tasks));
            }
        }
    }
}
