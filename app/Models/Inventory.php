<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function addInventory($id, $type = 1)
    {
        $requisition = Requisition::find($id);
        $items = $requisition->items()->where('status', '<>', 'CANCELED')->get();

        foreach ($items as $i => $item) {
            $inventoryProduct = $this::where('product_id', $item->product_id)->where('hospital_id', $requisition->hospital_id)->where('type', $type)->first();

            if ($inventoryProduct) {
                $inventoryProduct->quantity += $item->received_quantity;
                $inventoryProduct->update();
            } else {
                $this::create([
                    'hospital_id' => $requisition->hospital_id,
                    'department_id' => $type == 2 ? $requisition->department_id : null,
                    'product_id' => $item->product_id,
                    'quantity' => $item->received_quantity,
                    'type' => $type,
                ]);
            }

            if ($type == 2) {
                $hospitalInventory = $this::where('product_id', $item->product_id)->where('hospital_id', $requisition->hospital_id)->where('type', 1)->first();

                if ($hospitalInventory) {
                    $hospitalInventory->quantity -= $item->received_quantity;
                    $hospitalInventory->update();
                }
            }
        }

        return 0;
    }

    # deduct inventory when consume a product
    public function deductInventory($quantity)
    {
        $this->quantity = $this->quantity - $quantity;
        $this->save();
    }
}
