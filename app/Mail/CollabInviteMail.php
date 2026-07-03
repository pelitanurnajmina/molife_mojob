<?php

namespace App\Mail;

use App\Models\BusinessCollaborator;
use App\Models\BusinessProduct;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollabInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BusinessCollaborator $collab,
        public BusinessProduct $product,
        public ?User $inviter,
    ) {}

    public function build()
    {
        $inviterName = $this->inviter?->username ?: 'Seseorang';

        return $this->subject($inviterName . ' mengundang kamu berkolaborasi di molife')
            ->view('emails.collab-invite', [
                'inviterName' => $inviterName,
                'productName' => $this->product->name,
                'acceptUrl'   => route('kolaborasi.terima', $this->collab->token),
            ]);
    }
}
