<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrokerInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $address;
    public $workOrder;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $address, $workOrder)
    {
        $this->customerName = $customerName;
        $this->address = $address;
        $this->workOrder = $workOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Broker Inquiry Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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

    public function build(): BrokerInquiryMail
    {
        return $this->subject('询价邮件')
            ->view('emails.broker_inquiry') // 指向模板
            ->with([
                'customerName' => $this->customerName,
                'address' => $this->address,
                'workOrder' => $this->workOrder,
            ]);
    }
}
