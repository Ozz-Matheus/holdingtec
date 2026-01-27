<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $tenantName;

    public string $superAdminPass;

    /**
     * Create a new message instance.
     */
    public function __construct(string $tenantName, string $superAdminPass)
    {
        $this->tenantName = $tenantName;
        $this->superAdminPass = $superAdminPass;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Nuevas Credenciales')
            ->view('emails.tenant-credentials');
    }
}
