<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2021/3/9
 * Time: 8:58
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 't_gift_orderno';

    protected $fillable = ['orderno','country','websiteurl'];

    public $timestamps = false;

}