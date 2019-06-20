<?php namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Model;

class TransportEntity extends Model {
    protected $table = 'transport_entity';
    protected $fillable = ['car_number','last_loc_time','entity_info','entity_status','last_company_id','last_contact_id',
        'last_vendor_company_id','last_sub_status','last_geohash'];


    protected $casts = [
        'entity_info' => 'json'
    ];


    public static function findOrCreateByCarNumber($car_number) {
        $entity = self::where('car_number',strtoupper($car_number))->first();
        if (!$entity) {
            $entity = self::create([
                'car_number' => strtoupper($car_number),
                'last_loc_time' => date('Y-m-d H:i:s'),
                'entity_info' => []
            ]);
        }
        return $entity;
    }

}