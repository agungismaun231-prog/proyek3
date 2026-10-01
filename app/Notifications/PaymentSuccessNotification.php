<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * Create a new notification instance.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Kirim via database (untuk ditampilkan di UI) dan email
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✓ Pembayaran Berhasil - ' . $this->transaction->transaction_number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pembayaran Anda telah berhasil diproses!')
            ->line('**Nomor Transaksi:** ' . $this->transaction->transaction_number)
            ->line('**Total Pembayaran:** Rp ' . number_format($this->transaction->total, 0, ',', '.'))
            ->line('**Tanggal:** ' . $this->transaction->payment_date?->format('d M Y H:i') ?? now()->format('d M Y H:i'))
            ->action('Lihat Detail Transaksi', route('ecommerce.transactions.show', $this->transaction))
            ->line('Terima kasih telah menggunakan layanan kami!')
            ->salutation('Salam hormat,\\nTim Jasa Servis AC');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_success',
            'transaction_id' => $this->transaction->id,
            'transaction_number' => $this->transaction->transaction_number,
            'amount' => $this->transaction->total,
            'message' => 'Pembayaran Rp ' . number_format($this->transaction->total, 0, ',', '.') . ' untuk transaksi ' . $this->transaction->transaction_number . ' telah berhasil!',
        ];
    }
}
