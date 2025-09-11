<?php

namespace App\Http\Controllers;

use App\Models\Portal;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        $portal = Portal::first();
        return view('modules.portal.form', compact('portal'));
    }

    public function update(Request $request, $id)
    {
        $updatePortal = Portal::find($id);
        $updatePortal->fill($request->all());
        $updatePortal->save();
        return redirect()->back()->with('success', 'Portal atualizado com sucesso.');
    }
}
