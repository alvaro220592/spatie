<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Functionality;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProfileController extends Controller
{
    public function index(){
        $roles = Role::all();
        return view('admin.profile.index', compact('roles'));
    }

    public function store(Request $request){
        try {

            Role::create(['name' => $request->role]);

            return response()->json([
                'success' => true,
                'message' => 'Salvo com sucesso'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $th->getMessage()
            ]);
        }
    }

    public function getRoles(Request $request){
        $busca = $request->busca;
        $roles = Role::query();

        if ($busca) {
            $roles->where('name', 'like', "%$busca%");
        }
        return response()->json($roles->paginate(5));
    }

    public function edit($id){
        $role = Role::find($id);
        $funcionalidades = Functionality::all();
        return view('admin.profile.edit', compact('role', 'funcionalidades'));
    }

    public function update(Request $request){
        try {

            $role = Role::find($request->id);
            $role->name = $request->name;
            $role->update();

            return response()->json([
                'success' => true,
                'message' => 'Salvo com sucesso'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erro: ' . $th->getMessage()
            ]);
        }
    }
}
