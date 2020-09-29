<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// rota para criação de tokens
Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // criação de 3 tokens
    $token_can_all = $user->createToken('can_all')->plainTextToken;
    $token_can_update = $user->createToken('can_update', ['system:update'])->plainTextToken;
    $token_can_create = $user->createToken('can_create', ['system:create'])->plainTextToken;

    $abilities = [$token_can_all, $token_can_update, $token_can_create];

    return $abilities;
});


Route::group(['middleware' => ['auth:sanctum']], function(){
    // Retornando usuário com token válido
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rota que retorna usuários para a aplicação frontend
    Route::get('users', function(Request $request){
        return User::all();
    });

    // Listagem de todos os token
    Route::get('list_tokens', function(Request $request){
        return $request->user()->tokens;
    });

    // rota de teste de habilidades do token
    Route::get('token_abilities', function(Request $request){

        $abilities = [];

        if ($request->user()->tokenCan('system:update')) {
            array_push($abilities, 'posso atualizar');
        }

        if ($request->user()->tokenCan('system:create')) {
            array_push($abilities, 'posso criar');
        }

        if ($request->user()->tokenCan('system:delete')) {
            array_push($abilities, 'posso deletar porque eu posso tudo!!!');
        }

        return $abilities;
    });

    // rota que remove todos os tokens
    Route::delete('revoke_all_tokens', function(Request $request){
        // Revoke all tokens...
        $request->user()->tokens()->delete();
        return response()->json([], 204);
    });

    // rota que remove o token passado para requisição (current)
    Route::delete('revoke_current_token', function(Request $request){
        // Revoke the user's current token...
        $request->user()->currentAccessToken()->delete();
        return response()->json([], 204);
    });

    // rota que remove um token em específico
    Route::delete('revoke_specific_token', function(Request $request){
        // Revoke a specific token...
        $id = $request->input('id');
        $request->user()->tokens()->where('id', $id)->delete();
        return response()->json([], 204);
    });

});

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
