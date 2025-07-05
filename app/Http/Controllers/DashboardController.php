<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Type;
use App\Models\Category;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $types = Type::all();
        $categories = Category::all();

        return view('dashboard.dashboard', compact('types', 'categories'));
    }

    public function transaction(Request $request)
    {
        // dd($request);
        \App\Models\Transaction::create([
            'user_id' =>Auth::id(),
            'date' => Carbon::now(),
            'amount' =>$request->amount,
            'type_id' =>$request->type_id,
            'category_id' =>$request->category_id,
        ]);

        return response()->json(['message' => 'Transaction saved successfully.']);
    }
}
