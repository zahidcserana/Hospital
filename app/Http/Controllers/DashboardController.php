<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $s2hPendingRequisitions = 0;
        $s2hApprovedRequisitions = 0;
        $d2sPendingRequisitions = 0;
        $d2sApprovedRequisitions = 0;

        $where = [];

        if (!empty(Auth::user()->role->name) && !in_array(Auth::user()->role->name, ['SAdmin', 'Admin'])) {
            $where = array_merge(array(['requisitions.hospital_id', Auth::user()->hospital_id]), $where);

            if (in_array(Auth::user()->role->name, ['DA', 'DH', 'MS'])) {
                $where = array_merge(array(['requisitions.department_id', Auth::user()->department_id]), $where);
            }
        }

        $pendingStatusS2H = $this->pendingStatusS2H();
        $approvedStatusS2H = $this->approvedStatusS2H();

        $pendingStatusD2S = $this->pendingStatusD2S();
        $approvedStatusD2S = $this->approvedStatusD2S();

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.menu_access')[Auth::user()->role->name]) && in_array('s2h_requisitions', \config('settings.menu_access')[Auth::user()->role->name])) {
            $s2hRequisitions = Requisition::where('type', 1)->where($where)->get();
            $s2hPendingRequisitions = $s2hRequisitions->where('status', $pendingStatusS2H)->count();
            $s2hApprovedRequisitions = $s2hRequisitions->where('status', $approvedStatusS2H)->count();
        }

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.menu_access')[Auth::user()->role->name]) && in_array('d2s_requisitions', \config('settings.menu_access')[Auth::user()->role->name])) {
            $d2sRequisitions = Requisition::where('type', 2)->where($where)->get();
            $d2sPendingRequisitions = $d2sRequisitions->where('status', $pendingStatusD2S)->count();
            $d2sApprovedRequisitions = $d2sRequisitions->where('status', $approvedStatusD2S)->count();
        }

        $data = compact("s2hPendingRequisitions", "s2hApprovedRequisitions", "d2sPendingRequisitions", "d2sApprovedRequisitions");

        return Inertia::render('Home', ['param' => $data]);
    }

    public function pendingStatusS2H()
    {
        $pendingStatusS2H = 'INITIATED';

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.s2hPending')[Auth::user()->role->name])) {
            $pendingStatusS2H = \config('settings.s2hPending')[Auth::user()->role->name];
        }

        return $pendingStatusS2H;
    }

    public function approvedStatusS2H()
    {
        $approvedStatusS2H = 'ACCEPTED';

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.s2hApproved')[Auth::user()->role->name])) {
            $approvedStatusS2H = \config('settings.s2hApproved')[Auth::user()->role->name];
        }

        return $approvedStatusS2H;
    }

    public function pendingStatusD2S()
    {
        $pendingStatusD2S = 'INITIATED';

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.d2sPending')[Auth::user()->role->name])) {
            $pendingStatusD2S = \config('settings.d2sPending')[Auth::user()->role->name];
        }

        return $pendingStatusD2S;
    }

    public function approvedStatusD2S()
    {
        $approvedStatusD2S = 'VERIFIED';

        if (!empty(Auth::user()->role->name) && !empty(\config('settings.d2sApproved')[Auth::user()->role->name])) {
            $approvedStatusD2S = \config('settings.d2sApproved')[Auth::user()->role->name];
        }

        return $approvedStatusD2S;
    }
}
