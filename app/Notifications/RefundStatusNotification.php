<?php

namespace App\Notifications;

use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $refund;
    protected $status;

    public function __construct(Refund $refund, string $status)
    {
        $this->refund = $refund;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = '';
        $message = '';

        switch ($this->status) {
            case 'processing':
                $subject = 'Refund Anda Sedang Diproses - OtakAtik Academy';
                $message = 'Tim kami sedah mulai memproses permintaan refund Anda untuk kursus ' . $this->refund->registration->course->title;
                $actionText = 'Lihat Status Refund';
                break;
            case 'completed':
                $subject = 'Refund Berhasil Diproses - OtakAtik Academy';
                $message = 'Refund Anda sebesar Rp ' . number_format($this->refund->amount, 0, ',', '.') . ' telah berhasil diproses dan akan masuk ke rekening Anda dalam 1-3 hari kerja.';
                $actionText = 'Lihat Detail Refund';
                break;
            default:
                $subject = 'Update Status Refund - OtakAtik Academy';
                $message = 'Ada update terbaru untuk refund Anda.';
                $actionText = 'Lihat Refund';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line($message)
            ->line('Refund ID: #REF-' . $this->refund->id)
            ->line('Jumlah Refund: Rp ' . number_format($this->refund->amount, 0, ',', '.'))
            ->action($actionText, route('student.refund.detail', $this->refund->id))
            ->line('Terima kasih telah menggunakan OtakAtik Academy.');
    }

    public function toDatabase($notifiable)
    {
        $statusLabel = match($this->status) {
            'processing' => '⏳ Sedang Diproses',
            'completed' => '✓ Berhasil',
            default => 'Update Status',
        };

        return [
            'refund_id' => $this->refund->id,
            'status' => $this->status,
            'status_label' => $statusLabel,
            'amount' => $this->refund->amount,
            'course_title' => $this->refund->registration->course->title,
            'message' => match($this->status) {
                'processing' => 'Refund Anda sedang diproses oleh tim admin.',
                'completed' => 'Refund Anda telah berhasil diproses dan segera masuk ke rekening Anda.',
                default => 'Ada update terbaru untuk refund Anda.',
            },
        ];
    }
}
