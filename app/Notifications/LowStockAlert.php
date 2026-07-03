<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Domains\ECommerce\Models\Product;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $remainingStock;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, int $remainingStock)
    {
        $this->product = $product;
        $this->remainingStock = $remainingStock;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Low Stock Alert: ' . $this->product->name)
                    ->line('The stock for ' . $this->product->name . ' is running low.')
                    ->line('Remaining stock: ' . $this->remainingStock)
                    ->action('View Product', route('commerce.products.edit', $this->product))
                    ->line('Please restock soon to avoid missing out on orders!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'remaining_stock' => $this->remainingStock,
        ];
    }
}
