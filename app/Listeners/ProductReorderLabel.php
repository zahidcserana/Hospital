<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\RequisitionDetail;
use App\Events\DeliveredRequisition;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductReorderLabel
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeliveredRequisition  $event
     * @return void
     */
    public function handle(DeliveredRequisition $event)
    {
        foreach ($event->requisition->items as $item) {
            $this->reorderLabel($item->product_id);
        }
    }

    public function reorderLabel($productId)
    {
        $dateS = Carbon::now()->startOfMonth()->subMonth(3);
        $dateE = Carbon::now()->startOfMonth();

        $requisitionDetail = RequisitionDetail::where('product_id', $productId)
            ->with(['requisition' => function ($query) use ($dateS, $dateE) {
                $query->where('type', 2)
                    ->whereBetween('delivered_date', [$dateS, $dateE]);
            }])
            ->get();

        $deliveredQty = $requisitionDetail->sum('delivered_quantity');

        $product = Product::find($productId);
        $product->reorder_label = $deliveredQty / 3;
        $product->update();
    }
}
