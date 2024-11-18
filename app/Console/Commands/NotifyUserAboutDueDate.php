<?php

namespace App\Console\Commands;

use App\Mail\NotificationEmail;
use App\Models\BorrowedBook;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyUserAboutDueDate extends Command
{
    protected $signature = 'notify:due-dates';

    protected $description = 'Notify users about upcoming or overdue book return dates';

    public function handle()
    {
        $today = Carbon::now();
        $tomorrow = Carbon::now()->addDay();
        $borrowedBooks = BorrowedBook::where('notification_sent', false)
            ->where('status', 0)
            ->whereDate('due_date', '<=', $tomorrow)
            ->get();

        foreach ($borrowedBooks as $book) {
            $user = $book->user;
            $isOverdue = $today->gt($book->due_date);
            $penalty = $isOverdue ? 10 : 0;

            $mailData = [
                'name' => $user->name,
                'book_title' => $book->book->title,
                'message' => $isOverdue
                    ? "The due date for returning your borrowed book, '{$book->book->title}', has passed. A penalty of ₹{$penalty} has been applied."
                    : "Reminder: The due date for returning your book, '{$book->book->title}', is tomorrow.",
                'payment_link' => $isOverdue
                    ? "http://127.0.0.1:8000/payment/overdue/{$book->book_id}/{$book->user_id}"
                    : null,
            ];

            try {
                Mail::to($user->email)->send(new NotificationEmail($mailData));
                $book->notification_sent = true;
                $book->save();
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }

        $this->info('Due date notifications sent successfully!');
    }
}
