<?php

namespace App\Http\Controllers\Api;

require __DIR__ . '/../../../../vendor/autoload.php';


use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\DB;
use DateTime;


class CommonController extends Controller
{
    use Helpers;
    protected $uid;
    public $request;
    public $now_date;
    public $perfix;
    //public $api_uri = 'http://192.168.1.232:89';



    protected function return_success($data = [], $msg = 'success')
    {
//        return $this->response->array(['code' => 0, 'data' => $data, 'msg' => $msg]);
        return response()->json(['code' => 200, 'data' => $data, 'msg' => $msg])->setCallback(request()->input('callback'));
    }

    protected function return_error($msg = 'error', $code = '')
    {
        return response()->json(['code' => -1, 'msg' => $msg])->setCallback(request()->input('callback'));

        if ($this->request->ajax()) {
            $code = !empty($code) ? $code : config('third.error_code');
//            return $this->response->array(['code' => $code, 'msg' => $msg]);
            return response()->json(['code' => $code, 'msg' => $msg])->setCallback(request()->input('callback'));
        } else {
            return view('errors.500', ['msg' => $msg]);
        }
    }

    protected function paginate($data, $msg = 'success')
    {
        $return_data = array(
            'dataList' => $data['data'],
            'records' => $data['total'],
            'total' => $data['last_page'],
            'page' => $data['current_page'],
//            'total_data' => isset($data['total_data']) ? $data['total_data'] : '',
            'row'=> $data['to']

        );
//        return $this->response->array(['code' => 0, 'data' => $return_data, 'msg' => $msg]);
        return response()->json(['code' => 200, 'data' => $return_data, 'msg' => $msg])->setCallback(request()->input('callback'));
    }


    /**
     * @param $form_data
     * @param $field_arr
     * @param $lang
     * @return array
     * 根据$field_arr检测数据是否传过来
     */
    public function checkData($form_data, $field_arr, $isset = false)
    {
        foreach ($field_arr as $key => $val) {
            if (!array_key_exists($key, $form_data)) {
                return $this->return_error($val . __('Common.data_empty'));
            }
            if ($isset && !isset($form_data[$key])) {
                return $this->return_error($val . __('Common.data_empty'));
            }
        }
        return false;
    }


    /**
     * @return float
     * 当前毫秒数
     */
    function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    function excel($data, $filename, $html = '')
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (preg_match('/msie/', $ua) || preg_match('/edge/', $ua)) {
            $filename = str_replace('+', '%20', urlencode($filename));
        }
        header("Content-type: application/vnd.ms-excel; charset=gbk");
        header('Content-Disposition: attachment; filename=' . $filename . date('Y-m-d_H:i:s') . '.xls');
        if ($data) {
            $html = '<table border="1">';
//$list为数据库查询结果，既二维数组。利用循环出表格，直接输出，既在线生成execl文件
            foreach ($data as $key => $val) {
                $html .= '<tr>';
                foreach ($val as $v) {
                    $html .= '<td>' . $v . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table>';
        }
        $html = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>" . $html . "</body></html>";
        exit($html);
    }

    function excel_csv($data, $filename)
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename.csv");
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        foreach ($data as &$item) {  //$item为一维数组哦
            $item = (array)$item;
            foreach ($item as &$val) {
                $val = iconv("UTF-8", "GBK//IGNORE", $val);
            }
            fputcsv($fp, $item);
        }
        exit;
    }



    /**
     * 获得完整sql
     */
    public function get_sql($data)
    {
        $bindings = $data->getBindings();
        $sql = str_replace('?', '"%s"', $data->toSql());
        $sql = sprintf($sql, ...$bindings);
        return $sql;
    }

    /**
     * 返回case sql语句;
     */
    public function get_case_sql($field, $arr, $as = false)
    {
        $sql = 'case ' . $this->perfix . $field;
        $else_sql = '';
        foreach ($arr as $key => $val) {
            if ($key == 'else') {
                $else_sql .= ' else "' . $val . '"';
            } else {
                $sql .= ' when ' . $key . ' then "' . $val . '"';
            }
        }
        $sql .= $else_sql;
        $sql .= ' end' . ($as ? ' as ' . $as : '');
        return $sql;
    }

    public function uploadPic($folder_name = 'product_img', array $cuts = [false, 100])
    {
        $file_name = 'file';
        if ($this->request->hasFile($file_name)) {
            if ($cuts[0]) {
                $path = (new ImageUploadHandler())->save_cuts($this->request->file($file_name), $folder_name, $cuts[1] > 100 ? 100 : $cuts[1]);
                return $this->return_success($path);
            } else {
                $path = (new ImageUploadHandler())->save($this->request->file($file_name), $folder_name);
                return $this->return_success(asset('storage/' . substr($path, strpos($path, '/') + 1)));
            }
        }
        return $this->return_error(__('Common.data_error'));
    }

    public function curl_request($url, $method, $content = [], $header = [])
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); //  如果不是https的就注释 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
//        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return ['state' => false, 'msg' => curl_error($curl)];
        }
        curl_close($curl);
        $data = json_decode($result, true);
        return ['state' => true, 'data' => $data];
    }

}
