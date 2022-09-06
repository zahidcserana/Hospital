<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitionDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function updateItems($requisition, $items)
    {
        foreach ($items as $row) {
            $itemData = $this::find($row['id']);

            $this->returnQtyAdjust($itemData, $row, $requisition);
            $this->damageQtyAdjust($itemData, $row, $requisition);

            $data = $this->formData($row);

            $itemData->update($data);
        }
    }

    public function returnQtyAdjust($itemData, $row, $requisition)
    {
        if ($itemData->return_quantity < $row['return_quantity']) {
            $inventory = Inventory::where('product_id', $row['product_id'])
                ->where('hospital_id', $requisition->hospital_id)
                ->where('department_id', $requisition->department_id)
                ->where('type', $requisition->type)
                ->first();

            if ($inventory && ($row['return_quantity'] <= $itemData->received_quantity)) {
                $inventory->quantity -= ($row['return_quantity'] - $itemData->return_quantity);
                $inventory->update();
            }
        }
    }

    public function damageQtyAdjust($itemData, $row, $requisition)
    {
        if ($itemData->damaged_quantity < $row['damaged_quantity']) {
            $inventory = Inventory::where('product_id', $row['product_id'])
                ->where('hospital_id', $requisition->hospital_id)
                ->where('department_id', $requisition->department_id)
                ->where('type', $requisition->type)
                ->first();

            if ($inventory && ($row['damaged_quantity'] <= $itemData->received_quantity)) {
                $inventory->quantity -= ($row['damaged_quantity'] - $itemData->damaged_quantity);
                $inventory->update();
            }
        }
    }


    public function formData($row)
    {
        return [
            'expected_quantity' => $row['expected_quantity'],
            'approved_quantity' => $row['approved_quantity'],
            'received_quantity' => $row['received_quantity'],
            'return_quantity' => $row['return_quantity'],
            'damaged_quantity' => $row['damaged_quantity'],
            'tp' => $row['tp'],
            'mrp' => $row['mrp'],
            'batch_no' => $row['batch_no'],
            'mfg_date' => $row['mfg_date'],
            'exp_date' => $row['exp_date'],
            'remarks' => $row['remarks'],
        ];
    }
}
