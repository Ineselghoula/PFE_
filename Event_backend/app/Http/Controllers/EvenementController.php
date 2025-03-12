<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evenement;

class EvenementController extends Controller
{
    public function search(Request $request)
    {
        $query = Evenement::query();

        if ($request->has('keyword')) {
            $query->where('titre', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('categorie', $request->category);
        }

        if ($request->has('region') && $request->region != '') {
            $query->where('adresse', 'like', '%' . $request->region . '%');
        }

        if ($request->has('event_date') && $request->event_date != '') {
            $query->whereDate('date_debut', $request->event_date);
        }

        return response()->json($query->get());
    }
}
