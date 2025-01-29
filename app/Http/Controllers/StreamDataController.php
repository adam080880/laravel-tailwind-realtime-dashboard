<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StreamData;

class StreamDataController extends Controller
{
    public function dashboard()
    {
        return view('home');
    }

    public function getBranchAndLabel()
    {
        $streamData = StreamData::select('pointName')->groupBy('pointName')->get();
        $mappingDataByBranch = [];

        foreach ($streamData as $data) {
            [$code, $etc] = explode(':', $data->pointName);
            [$branch] = explode('.', $etc);

            if (!isset($mappingDataByBranch[$branch])) {
                $mappingDataByBranch[$branch] = [];
            }

            $mappingDataByBranch[$branch][] = $data->pointName;
        }

        return $mappingDataByBranch;
    }

    public function getBranchesAndLabel()
    {
        return response()->json($this->getBranchAndLabel());
    }

    public function getHistoryByLabels(Request $request)
    {
        $labels = $request->post('labels', []);

        $currentHistory = StreamData::select('id', 'pointName', 'pointValue', 'pointQuality', 'pointTimestamp')
            ->whereIn('pointName', $labels)
            ->orderBy('id', 'DESC')
            ->limit(100)
            ->get();

        return response()->json($currentHistory);
    }

    public function getSnapshotByLabelsAndLastId(Request $request)
    {
        $labels = $request->post('labels', []);
        $lastId = $request->post('lastId', 0);

        $currentHistory = StreamData::select('id', 'pointName', 'pointValue', 'pointQuality', 'pointTimestamp')
            ->whereIn('pointName', $labels)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($currentHistory);
    }

    public function getSnapshotByLabels(Request $request)
    {
        $labels = $request->post('labels', []);

        $currentSnapshotByPointName = StreamData::select(DB::raw('MAX(id) as id'))
            ->whereIn('pointName', $labels)
            ->groupBy('pointName')
            ->get();

        $currentSnapshotIds = $currentSnapshotByPointName->pluck('id');

        $currentSnapshot = StreamData::select('pointName', 'pointValue', 'pointQuality', 'pointTimestamp')
            ->whereIn('id', $currentSnapshotIds)
            ->get();

        return response()->json($currentSnapshot);
    }
}
