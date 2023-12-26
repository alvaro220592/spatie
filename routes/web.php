<?php


use Illuminate\Support\Facades\Route;
use App\Models\Admin\Functionality;
use Spatie\Permission\Models\Permission;
use \App\Http\Controllers\Admin\ProfileController;
use \App\Http\Controllers\Admin\FunctionalityController;
use App\Http\Controllers\Admin\PermissionController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function(){

    Route::group(['prefix' => 'perfisDeAcesso'], function(){
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/salvarPerfil', [ProfileController::class, 'store'])->name('profile.store');
        Route::post('/todos', [ProfileController::class, 'getRoles'])->name('profile.getRoles');
        Route::post('/alterar', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/editar/{id}', [ProfileController::class, 'edit'])->name('profile.edit');
    });

    Route::group(['prefix' => 'permissoes'], function(){
        Route::post('/update', [PermissionController::class, 'update'])->name('permission.update');
        Route::post('/permissoesDeFuncionalidades', [PermissionController::class, 'getPermissions'])->name('permission.getPermissions');
    });

    Route::group(['prefix' => 'funcionalidades'], function(){
        Route::get('/', [FunctionalityController::class, 'index'])->name('functionality.index');
    });

    Route::get('/admin', function(){
        Auth::user()->assignRole(1);
    });

    Route::get('novaFuncionalidade', function(){
        // Functionality::insert([
        //     ['name' => 'Perfis de acesso'],
        //     ['name' => 'Funcionalidades'],
        // ]);
        dd(Functionality::all());
    });

    Route::get('novaPermissao', function(){
        // Permission::create(
        //     ['name' => 'Visualizar', 'guard_name' => 'web', 'functionality_id' => 2]
        // );


        Permission::insert([
            ['name' => 'Visualizar perfil de acesso', 'guard_name' => 'web', 'functionality_id' => 1],
            ['name' => 'Cadastrar perfil de acesso', 'guard_name' => 'web', 'functionality_id' => 1],
            ['name' => 'Editar perfil de acesso', 'guard_name' => 'web', 'functionality_id' => 1],
            ['name' => 'Excluir perfil de acesso', 'guard_name' => 'web', 'functionality_id' => 1],

            ['name' => 'Visualizar funcionalidade', 'guard_name' => 'web', 'functionality_id' => 2],
            ['name' => 'Cadastrar funcionalidade', 'guard_name' => 'web', 'functionality_id' => 2],
            ['name' => 'Editar funcionalidade', 'guard_name' => 'web', 'functionality_id' => 2],
            ['name' => 'Excluir funcionalidade', 'guard_name' => 'web', 'functionality_id' => 2],
        ]);

        dd(Permission::all());
    });

    Route::get('/mudarSenha', function(){
        $user = User::find(Auth::user()->id);
        $user->password = '12345';
        // $user->update();
    });
});
