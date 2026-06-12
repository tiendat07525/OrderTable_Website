<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public $booking;
    public function __construct($booking)
    {
        $this->booking = $booking->load(['user', 'table']);
    }

    public function build()
    {
        return $this->subject('Đặt bàn thành công tại Golden Spoons')
            ->view('emails.bookingSuccessMail');
    }
}
