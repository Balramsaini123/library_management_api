<?php
namespace App\Http\Controllers;

use App\Models\BorrowedBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe;
class StripePaymentController extends Controller
{
    public function overduePayment($book_id, $user_id)
    {
        $book = BorrowedBook::with('book')->where('book_id', $book_id)->where('user_id', $user_id)->firstOrFail();
        return view('stripe', compact('book', 'user_id', 'book_id'));
    }
    
    public function processOverduePayment(Request $request)
    {
        Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    
        Stripe\Charge::create([
            "amount" => 1000, // Set your penalty amount here
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Overdue payment for book ID: " . $request->book_id,
        ]);
    
        // Update book status as paid
        $borrowedBook = BorrowedBook::where('book_id', $request->book_id)->first();
        $borrowedBook->update([
            'penalty_paid' => true,
            'status' => 1, // Mark as returned if applicable
        ]);
    
        Session::flash('success', 'Payment successful!');
        return back();

    }
    
}
