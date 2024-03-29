<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User as UserResource;
use App\Http\Resources\VoucherInstance as VoucherInstanceResource;

use App\Http\Traits\CRUDUtilities;
use App\Http\Traits\StatusUtilities;

use App\User;

class ActionLog extends JsonResource
{
    use CRUDUtilities, StatusUtilities;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $dataRow = $this->getDataRowByPrimaryKey($this->data_type, $this->data_row_id);
        $user = $this->getDataRowByPrimaryKey(User::class, $this->user_id);
        $className = class_basename(get_class($dataRow));
        $attributes = [];

        if ($className == 'Transaction') {

            $statusCode = $dataRow->transactionPoints->status;
            $status = $this->resolveStatusFromStatusCode($statusCode);
            // if ($statusCode != config('constants.status_codes.used_status')) {
                $status .= ' '.$dataRow->transactionPoints->available_points.'/'.$dataRow->transactionPoints->available_points;
            // }

            $voucherInstance = null;
            if ($this->action->type == config('constants.actions.transaction_voucher_used_success')){
                $voucherInstance = $dataRow->voucherInstance;
            }

            $attributes['customer'] = $dataRow->user? new UserResource($dataRow->user): null;
            $attributes['invoice_number'] = $dataRow->invoice_number;
            $attributes['invoice_value'] = $dataRow->invoice_value;
            $attributes['voucher'] = $voucherInstance;
            $attributes['points'] = $dataRow->transactionPoints->original;
            $attributes['status'] = $status;
            $attributes['action'] = $this->action->type;

        } else {

            $statusCode = $dataRow->status;

            $attributes['customer'] = $this->scope->name != config("constants.scopes.cashier")? new UserResource($dataRow->user): null;
            $attributes['invoice_number'] = null;
            $attributes['invoice_value'] = null;
            $attributes['voucher'] = new VoucherInstanceResource($dataRow);
            $attributes['points'] = $dataRow->voucher->points;
            $attributes['status'] = $this->resolveStatusFromStatusCode($statusCode);
            $attributes['action'] = $this->action->type;
        }

        return [
            'id' => $this->id,
            'cashier' => $this->when($this->scope->name == config("constants.scopes.cashier"), new UserResource($dataRow->user), null),
            'customer' => $attributes['customer'],
            'invoice_number' => $this->when(array_key_exists('invoice_number', $attributes), $attributes['invoice_number'], null),
            'invoice_value' => $this->when(array_key_exists('invoice_value', $attributes), $attributes['invoice_value'], null),
            'voucher' => $this->when(array_key_exists('voucher', $attributes), new VoucherInstanceResource($attributes['voucher']), null),
            'points' => $this->when(array_key_exists('points', $attributes), $attributes['points'], null),
            'status' => $this->when(array_key_exists('status', $attributes), $attributes['status'], null),
            'action' => $this->when(array_key_exists('action', $attributes), $attributes['action'], null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
