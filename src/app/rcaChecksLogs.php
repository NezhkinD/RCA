<?php


namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class rcaChecksLogs extends Model
{
    protected $table = 'rca_checks_logs';

    public function insertAndGetIds(array $data): array
    {
        DB::table($this->table)->insertOrIgnore($data);

        return DB::table($this->table)
            ->whereIn('check', array_column($data, 'check'))
            ->whereIn('created_at', array_column($data, 'created_at'))
            ->get()
            ->values()
            ->all();
    }

    public function deleteLine(int $id):int
    {
        return DB::table($this->table)
            ->delete($id);
    }
}
