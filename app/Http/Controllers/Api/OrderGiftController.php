<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2021/3/8
 * Time: 9:57
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\Import;
use App\Models\Order;
use App\Models\OrderReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use DateTime;
use Maatwebsite\Excel\Facades\Excel;

class OrderGiftController extends CommonController
{



    //获取订单号，查询分页
    public function GetOrderNoPageList(Request $request)
    {
        $data1 = $request->getContent();
        $d1 = json_decode($data1);
        $orderno = $d1->orderNo;
        $rows = $d1->pagination->rows;
        $page = $d1->pagination->page;
        if ($rows != 0) {
            $pageSize = $rows;
        } else {
            $pageSize = 10;
        }
        if ($page != 0) {
            $currentPage = $page;
        } else {
            $currentPage = 1;
        }
        if ($orderno != '') {
            $query = DB::table('t_gift_orderno')->where('orderno', $orderno);
        } else {
            $query = DB::table('t_gift_orderno');
        }
        $data = $query->paginate($pageSize, ['*'], 'page', $currentPage)->toArray();
        return $this->paginate($data);

    }


    //获取订单评价，查询分页
    public function GetOrderReviewPageList(Request $request)
    {
        $data1 = $request->getContent();
        $d1 = json_decode($data1);
        $orderno = $d1->orderNo;
        $rows = $d1->pagination->rows;
        $page = $d1->pagination->page;
        if ($rows != 0) {
            $pageSize = $rows;
        } else {
            $pageSize = 10;
        }
        if ($page != 0) {
            $currentPage = $page;
        } else {
            $currentPage = 1;
        }
        if ($orderno != '') {
            $query = DB::table('t_gift_orderreview')->where('orderNo', $orderno);
        } else {
            $query = DB::table('t_gift_orderreview');
        }
        $data = $query->paginate($pageSize, ['*'], 'page', $currentPage)->toArray();
        return $this->paginate($data);
    }

    //获取订单号信息
    public function GetOrderNoInfo(Request $request)
    {
        $no = trim($request->orderNo);
        $w ="/[a-zA-Z0-9]{19}/";
      if (!preg_match($w, $no) && strlen($no)!=19) {
        return $this->return_error("The order number does not conform to the specification");
       }
        $query = DB::table('t_gift_orderno')->where('orderNo', $no)->get();
        if (!Empty($query)) {
            return $this->return_success($query);
        }
        else {
            return $this->return_error('The order number does not exist');
        }

    }


    //获取订单评价信息
    public function GetOrderReviewInfo(Request $request)
    {
        $id = trim($request->id);
        $query = DB::table('t_gift_orderreview')->where('id', $id)->get();
        if (!Empty($query)) {
            return $this->return_success($query);
        } else {
            return $this->return_error('The order review  does not exist');
        }
//        return $this->return_success($query);
    }


    public function SaveOrderNo(Request $request)
    {
        $data1 = $request->all();
        $id = $data1['id'];
        if ($id != 0) {
            $new = Order::find($id);
        } else {
            $new = new Order();
        }

//        dump(strlen($data1['orderNo']));

        if (isset($data1['orderNo'])) {
//            $w = "/^\\d+(\\.\\d{1,8})?$/";
            $w ="/[a-zA-Z0-9]{19}/";
            if (!preg_match($w, $data1['orderNo']) && strlen($data1['orderNo'])!=19) {
//                dd(2);
                return $this->return_error("The order number does not conform to the specification");
            }
        }

        $new->orderNo = $data1['orderNo'];
        $new->country = $data1['country'];
        $new->webSiteUrl = $data1['webSiteUrl'];
        $now = new DateTime();
        $new->createtime = $now;
        $res = $new->save();
        if ($res) {
            return $this->return_success('', 'Save success');
        } else {
            return $this->return_error('Save failed');
        }
    }

    public function SaveOrderReview(Request $request)
    {
        $data1 = $request->all();
        $id = $data1['id'];
        $res = null;
        if ($id != 0) {
            $new = OrderReview::find($id);
        } else {
            $new = new OrderReview();
        }
        if (isset($data1['orderNo'])) {
//            $w = "/^\\d+(\\.\\d{1,8})?$/";
            $w ="/[a-zA-Z0-9]{19}/";
            if (!preg_match($w, $data1['orderNo']) && strlen($data1['orderNo'])!=19) {
//                dd(2);
                return $this->return_error("The order number does not conform to the specification");
            }
        }
        $new->userName =  $data1['userName'];
        $new->userEmail = $data1['userEmail'];
        $new->appNumber = $data1['appNumber'];
//        $new->orderNo = $data1['orderNo'];
        $new->review =$data1['review'];
        $new->stars = $data1['stars'];
        $now = new DateTime();
        $new->createtime = $now;
        $res = $new->save();
        if ($res) {
            return $this->return_success('', 'Save success');
        } else {
            return $this->return_error('Save failed');
        }

    }


    public function ImportOrderNo(Request $request)
    {
//        header("Access-Control-Allow-Origin: http://10.27.153.64:8085/");
//        header("Access-Control-Allow-Origin:*");
        $files = $request->file('file');

        $msg_arr[] = '';
        $ext = $files->getClientOriginalExtension();
        if ($ext != 'xls' and $ext != 'xlsx') {
            return $this->return_error('Wrong file type');
        }

//        $file = $files->store('temp');
//        Excel::load($files, function($reader) {
//            $data = $reader->all();
//            dd($data);
//        });


        $array = Excel::toArray(new Import, $files);
        $dates = ($array[0]);
        $count = 0;
        $ssc = 0;
         for ($key = 1; $key < count($dates); $key++) {
            $new = new Order();
            $orderNo = str_replace(" ", '', $dates[$key][0]);
             if (isset($orderNo)) {
//            $w = "/^\\d+(\\.\\d{1,8})?$/";
                 $w ="/[a-zA-Z0-9]{19}/";
                 if (!preg_match($w, $orderNo) && strlen($orderNo)!=19) {
                     $count ++;
                     continue;
//                     return $this->return_error("The order number does not conform to the specification");
                 }
             }

            $find = DB::table('t_gift_orderno')->where('orderNo', $orderNo)->get();
            if ($find->isEmpty()) {
                $new->orderNo = $orderNo;
                $now = new DateTime();
                $new->createtime = $now;
                $new->save();
                $count ++;
                $ssc++;
            }else{
                $count ++;
                continue;
            }
        }
        return $this->response->array(['code'=>200 ,'msg' => 'Save success,total: '.$count.' success: '.$ssc.'']);
//            return $this->return_success('', 'Save success,total: '.$count.' success: '.$ssc.'');
    }


}

