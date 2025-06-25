<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Support\Facades\DB;

class OrderPaymentService
{
    public function create(array $data): OrderPayment
    {
        return DB::transaction(function () use ($data) {
            $orderPayment = OrderPayment::create($data);
            $this->updateOrderPaymentStatus($orderPayment->order_id);
            return $orderPayment;
        });
    }

    public function update(OrderPayment $orderPayment, array $data): OrderPayment
    {
        return DB::transaction(function () use ($orderPayment, $data) {
            $orderPayment->update($data);
            $this->updateOrderPaymentStatus($orderPayment->order_id);
            return $orderPayment;
        });
    }

    protected function updateOrderPaymentStatus(int $orderId): void
    {
        $order = Order::with('order_payments')->findOrFail($orderId);

        $totalPaid = $order->order_payments->sum('value');
        $totalValue = $order->total_value;

        if ($totalPaid == 0) {
            $order->order_status_id = 1; // Pendiente
        } elseif ($totalPaid < $totalValue) {
            $order->order_status_id = 2; // Pago Parcial
        } elseif ($totalPaid >= $totalValue) {
            $order->order_status_id = 3; // Pago Total
        }

        $order->save();
    }
}
