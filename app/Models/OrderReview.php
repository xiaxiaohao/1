<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2021/3/9
 * Time: 8:58
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
    protected $table = 't_gift_orderreview';

    protected $fillable = ['userName','userEmail','appNumber','review','stars','orderno'];

    public $timestamps = false;


}