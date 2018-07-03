<?php

namespace App\Models\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class SupplierPayment extends Model
{
    protected $table = 'bill_pays';

    const MORPH_KEY = 'supplier_pays';

//    public function paginate()
//    {
//        $perPage = Request::get('per_page', 10);
//
//        $page = Request::get('page', 1);
//
//        $start = ($page-1)*$perPage;
//
//        // 运行sql获取数据数组
//        $sql = 'select * from ...';
//
//        $result = DB::select($sql);
//
//        $movies = static::hydrate($result);
//
//        $paginator = new LengthAwarePaginator($movies, $total, $perPage);
//
//        $paginator->setPath(url()->current());
//
//        return $paginator;
//    }

    public static function with($relations)
    {
        return new static;
    }
}
