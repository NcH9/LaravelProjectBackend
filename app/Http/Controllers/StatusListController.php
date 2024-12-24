<?php

namespace App\Http\Controllers;

use App\Models\StatusList;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class StatusListController extends Controller
{
    //
    public function getAll():Collection {
        $statusList = StatusList::all();
        return $statusList;
    }
    public function getSpecific(int $id) {
        try {
            $status = StatusList::where('id', $id)->firstOrFail();
            return $status->name;
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Status not found'], 404);
        }
    }
}
