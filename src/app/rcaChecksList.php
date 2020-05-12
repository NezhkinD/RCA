<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class rcaChecksList extends Model
{
    protected $table = 'rca_checks_list';

    public function insertAndGetIds(array $data): array
    {
        DB::table($this->table)->insertOrIgnore($data);

        return DB::table($this->table)
            ->whereIn('number', array_column($data, 'number'))
            ->whereIn('created_at', array_column($data, 'created_at'))
            ->get()
            ->values()
            ->all();
    }

    public function getUsingId(int $id): array
    {
        return DB::table($this->table)
            ->join('rca_numbers', 'rca_checks_list.number', '=', 'rca_numbers.id')
            ->where('rca_checks_list.id', '=', $id)
            ->get()
            ->values()
            ->all();
    }

    public function updateUsingId(int $id, array $data): void
    {
        DB::table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    public function updateFieldUpdatedAt(int $id): void
    {
        $date = (new DateTime)->format('Y-m-d H:i:s.u');

        DB::table($this->table)
            ->where('id', $id)
            ->update(['updated_at' => $date]);
    }

    public function getFeed(): array
    {
        return DB::table($this->table)
            ->join('rca_numbers', $this->table . '.number', '=', 'rca_numbers.id')
            ->where($this->table . '.status', '=', 'done')
            ->where($this->table . '.view', '=', false)
            ->get()
            ->values()
            ->all();
    }

    public function viewed(array $data)
    {
        return DB::table($this->table)
            ->where('status', '=', 'done')
            ->whereIn('id', $data)
            ->update(['view' => 'true']);
    }
}
