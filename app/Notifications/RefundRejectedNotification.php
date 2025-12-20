<?php

namespace App\Notifications;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $refund;

    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Permintaan Refund Ditolak - OtakAtik Academy')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Sayangnya, permintaan refund Anda untuk kursus ' . $this->refund->registration->course->title . ' telah ditolak.')
            ->line('Alasan: ' . $this->refund->admin_notes)
            ->line('Refund ID: #REF-' . $this->refund->id)
            ->action('Lihat Detail Refund', route('student.refund.detail', $this->refund->id))
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi tim support kami.')
            ->line('Terima kasih telah menggunakan OtakAtik Academy.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'refund_id' => $this->refund->id,
            'status' => 'rejected',
            'status_label' => '✗ Ditolak',
            'amount' => $this->refund->amount,
            'course_title' => $this->refund->registration->course->title,
            'message' => 'Permintaan refund Anda telah ditolak. Alasan: ' . $this->refund->admin_notes,
        ];
    }
}
