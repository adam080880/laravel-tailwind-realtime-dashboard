<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StreamData;

class StreamDataController extends Controller
{
    public function home()
    {
        $authUser = auth()->user();
        $users = [];

        if ($authUser->role == 2) {
            $users = \App\Models\User::get();
        }

        return view('home', [
            'user' => $authUser,
            'users' => $users
        ]);
    }

    public function dashboard()
    {
        return view('stream');
    }

    public function change_password(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password'
        ]);

        $user = auth()->user();
        $newUser = \App\Models\User::find($user->id);

        if (!\Hash::check($validated['old_password'], $newUser->password)) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'old_password' => ['Password lama tidak sesuai'],
             ]);

             throw $error;
        }

        $newUser->password = $validated['new_password'];
        $newUser->save();

        return redirect()->back()->with('success_message', 'Password berhasil diubah');
    }

    public function delete_user(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
        ]);

        $newUser = \App\Models\User::find($validated['userId']);

        if ($newUser) {
            $newUser->delete();
        };

        return redirect()->back()->with('user_success_message', 'Berhasil hapus user');
    }

    public function update_user(Request $request)
    {
        $validated = $request->validate([
            'userId' => '',
            'name' => 'required',
            'email' => 'required',
            'role' => 'required',
            'password' => '',
        ]);

        if ($validated['userId']) {
            $newUser = \App\Models\User::find($validated['userId']);

            if ($validated['password']) {
                $newUser->password = $validated['password'];
            }
        } else {
            $newUser = new \App\Models\User();
            $newUser->password = $validated['password'];
        }
        $newUser->name = $validated['name'];
        $newUser->email = $validated['email'];
        $newUser->role = $validated['role'];
        $newUser->verified = true;
        $newUser->save();

        return redirect()->back()->with('user_success_message', 'Berhasil ' . ($validated['userId'] ? 'ubah' : 'buat') . ' user');
    }

    public function confirm_user(Request $request)
    {
        $validated = $request->validate([
            'userId' => 'required|exists:users,id',
        ]);

        $newUser = \App\Models\User::find($validated['userId']);

        if ($newUser) {
            $newUser->verified = true;
            $newUser->save();
        };

        return redirect()->back()->with('user_success_message', 'Berhasil verifikasi user');
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

        $currentHistoryIds = StreamData::select(DB::raw('MAX(id) as id'), 'pointName')
            ->whereIn('pointName', $labels)
            ->groupBy('pointName')
            ->get();

        $currentHistory = StreamData::select('id', 'pointName', 'pointValue', 'pointQuality', 'pointTimestamp')
            ->whereIn('id', $currentHistoryIds->pluck('id'))
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
