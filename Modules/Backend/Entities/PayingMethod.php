<?php

namespace Modules\Backend\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Cms\Entities\Category;

class PayingMethod extends Model
{
    protected $table    = 'paying_method';
    protected $fillable = [
        'project_id',
        'project_type_id',
        'first_pay',
        'number_id',
    ];
    public function payCategory(){
        return $this->hasOne(Category::class,'id','project_type_id');
    }
    public function numberCategory(){
        return $this->hasOne(Category::class,'id','number_id');
    }
}
