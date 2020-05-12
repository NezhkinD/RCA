<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class rcaNumbers extends Model
{
    protected $table = 'rca_numbers';

    public function insertAndGetIds(array $data): array
    {
        DB::table($this->table)->insertOrIgnore($data);

        return DB::table($this->table)
            ->whereIn('uuid', array_column($data, 'uuid'))
            ->get()
            ->values()
            ->all();
    }
}
