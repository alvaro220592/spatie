<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Functionality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function getPermissions(Request $request){
        $role = Role::find($request->role_id);

        $functionalitiesPermissionsRoles = Functionality::with(['permissions.roles'])->get();

        return response()->json([
                'role_id' => $role->id,
                'funcionalidades' => $functionalitiesPermissionsRoles
            ]
        );
    }

    public function update(Request $request){
        try {
            $role = Role::find($request->role_id);
            $permissoes_form = $request->permissoes;

            foreach($permissoes_form as $permissao_form){

                if ($permissao_form['checked'] == true) {
                    $role->givePermissionTo(Permission::find($permissao_form['permissao_id']));
                } else {
                    $role->revokePermissionTo(Permission::find($permissao_form['permissao_id']));
                }
            }

            return response()->json([
                'success' => true,
                'redirect_url' => route('profile.index')
            ]);

        } catch(\Throwable $th) {
            return response()->json('erro: ' . $th->getMessage());
        }
    }
}
