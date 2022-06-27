<?php

namespace Pterodactyl\Services\Billing;

use Carbon\Carbon;
use Pterodactyl\Models\Type;

class TypeService
{
    public Type $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        $type = $this->type;
        $type->name = $data['name'];
        $type->default_image = $data['default_image'];
        $type->egg = $data['egg'];
        $type->updated_at = Carbon::now();
        $type->created_at = Carbon::now();
        Type::query()->insert($type->getAttributes());
    }

    /**
     * @param array $data
     * @return void
     */
    public function update(array $data): void
    {
        $type = $this->type;
        $type->name = $data['name'];
        $type->default_image = $data['default_image'];
        $type->egg = $data['egg'];
        $type->updated_at = Carbon::now();
        Type::query()->where('id', '=', $data['id'])->update($type->getAttributes());
    }
}
