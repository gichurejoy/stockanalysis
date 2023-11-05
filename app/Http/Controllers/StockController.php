<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Execption;
use App\Models\SalesData;
use Carbon\Carbon;

class StockController extends Controller
{

    public function index(){

        return view('chart');

    }

    public function getAll(){
        // $data = DB::select("select distinct branch from sales_data");
        $branch = 'MER002';
        $data = DB::select("select distinct item_code, item_name from sales_data where branch='$branch'");

        dd(response()->json($data));
    }

    public function getItemsByBranch(Request $request, $branch){
        // $branch = trim($request->get('branch'));
        $distinctItem = DB::select("select distinct item_code, item_name from sales_data where branch='$branch'");
        return response()->json($distinctItem);
    }

    public function getUniqueBranches(){
        $data = DB::select("select distinct branch from sales_data");
        return response()->json($data);
    }

    public function getStock(Request $request)
    {
        $branch = $request->input('branch');
        $itemCode = $request->input('itemCode');

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // $salesData = SalesData::where('branch', $branch)
        //     ->where('item_code', $itemCode)
        //     ->orderBy('doc_date', 'asc')
        //     ->get();

        $distinctBranches = DB::select("select distinct branch from sales_data");
        $distinctItem = DB::select("select distinct item_code, item_name from sales_data where branch='MER002'");
        $salesData = DB::select("select * from sales_data where item_code = '$itemCode' and branch = '$branch' order by doc_date");
        $productCount=DB::select("select count(distinct item_code) as itemcode_count from sales_data");
        $branchCount = DB::select("select count(distinct branch) as branch_count from sales_data")[0]->branch_count;

        
        
        $runningStock = 0;
        $results = [];

        foreach ($salesData as $data) {
            $runningStock += $data->qty_in - $data->qty_out;

            if (isset($prevDate)) {
                $daysBetween = Carbon::parse($data->doc_date)->diffInDays($prevDate);
            } else {
                $daysBetween = 0;
            }

            $result = [
                'branch' => $data->branch,
                'itemCode' => $data->item_code,
                'itemName' => $data->item_name,
                'qtyIn' => $data->qty_in,
                'qtyOut' => $data->qty_out,
                'docDate' => $data->doc_date,
                'runningStock' => $runningStock,
                'daysBetween' => $daysBetween,
            ];

            $results[] = $result;

            $prevDate = $data->doc_date;
        }

        $res = array_filter($results, function($x) use ($startDate, $endDate){
            $docDate = $x['docDate'];
            return ($docDate >= $startDate && $docDate <= $endDate);
        });

        //return $res;

        $chart = [
            'labels' => [],
            'qtyIn' => [],
            'qtyOut' => [],
            'runningStock' => [],
        ];

        foreach ($res as $result) {
            $chart['labels'][] = $result['docDate'];
            $chart['qtyIn'][] = $result['qtyIn'];
            $chart['qtyOut'][] = $result['qtyOut'];
            $chart['runningStock'][] = $result['runningStock'];
        }

       return $chart;

        // return view('chart', [
        //     'distinctBranches' => $distinctBranches,
        //     'distinctItem' => $distinctItem,
        // ]);
        
        // return view('chart', compact('chart'));
        // return response()->json(['data' => $results]);
        // return response()->view(['data' => $results]);
    }
}
