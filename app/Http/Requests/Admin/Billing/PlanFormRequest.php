<?php

namespace Pterodactyl\Http\Requests\Admin\Billing;

use Pterodactyl\Http\Requests\Admin\AdminFormRequest;

class PlanFormRequest extends AdminFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|string',
            'price' => 'required|string|digits_between:1,4',
            'cpu' => 'required|integer|digits_between:1,3',
            'memory' => 'required|integer|digits_between:1,5',
            'disk' => 'required|integer|digits_between:1,8',
            'swap' => 'required|integer|digits_between:0,6',
            'io' => 'required|integer|digits_between:3,3',
            'databases' => 'required|integer|digits_between:1,2',
            'allocations' => 'required|integer|digits_between:1,2',
            'backups' => 'required|integer|digits_between:1,2'
        ];
    }
}
