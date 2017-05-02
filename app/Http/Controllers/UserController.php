<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\UploadManager;
use App\Post;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('dashboard.user.index')->with("users", $users);
    }

    /**
     * Cadastra no banco de dados o cliente.
     *
     * @param  \Illuminate\Http\Request  $request(name, email, password)
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'is_master' => 'required'
        ]);

        /*Retorna os erros se ouver*/
        if ($validator->fails()){
            return response()->json(
                $validator->errors()
            , 422);
        }

        $createdUser = User::create([
            'name' => ucfirst($request->input()['name']),
            'email' => $request->input()['email'],
            'password' => bcrypt($request->input()['password']),
            'is_master' => $request->input()['is_master']
        ]);

        return $createdUser;
        //return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $request (name, email)
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'name' => 'required'
        ]);

        /*Retorna os erros se ouver*/
        if ($validator->fails()){
            return response()->json([
                $validator->errors()
            ]);
        }

        $updatedUser = $id->update([
            'name' => ucfirst($request->input()['name']),
            'email' => $request->input()['email'],
        ]);

        return response()->json([
            'status' => 'Atualizado com sucesso!'
        ], 200);
    }

    /**
     * Troca de senha do usuário
     *
     * @param  int  $id(id do usuário), $request(senha)
     *
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, User $id)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6'
        ]);

        /*Retorna os erros se ouver*/
        if ($validator->fails()){
            return response()->json([
                $validator->errors()
            ]);
        }

        $id->password = bcrypt($request['new_password']);
        $id->update();

        return response()->json([
            'status' => 'Senha alterada com sucesso!'
        ], 200);
    }

    /**
     * Envia avatar do usuario
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function uploadAvatar(Request $request, User $id)
    {
        //dd($request->all());
        //dd($request->user()->id);     usar isso se possivel quando tiver login
        $validator = Validator::make($request->all(), [
            'avatar' => 'mimes:jpeg,bmp,png,jpg'
        ]);

        /*Retorna os erros se ouver*/
        if ($validator->fails()){
            return response()->json([
                $validator->errors()
            ]);
        }

        $path = UploadManager:: storeAvatar($id, $request->file('avatar'));

        $id->avatar = $path;
        $id->update();

        return response()->json([
            'status' => 'Avatar atualizado com sucesso!'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $id)
    {
        $id->delete();

        return response()->json([
            'status' => 'Deletado com sucesso!'
        ], 200);
    }

    /**
     * Retorna informações da conta de todos os uruários
     *
     *  @return \Illuminate\Http\Response
     */
    public function getUsers() {
        $users = User::all();

        //filtra o array trocando os valores 1 por 0 na posição is_master
        $users->filter(function($val){
            if ($val->is_master == 1){
                $val->is_master = "Sim";
            }else{
                $val->is_master = "Não";
            }
            return $val;
        });

        return response()->json([
        $users
        ], 200);
    }

    /**
     * Retorna informações completas sobre um único usuário como numero de posts e infos básicas...
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getUserComplete(User $user) {
        $user->post_count = Post::where('user_id', $user->id)->where('allowed', 1)->count();
        $user->created_date = $user->created_at->format('d-m-Y');

        //$user->is_master = ($user->is_master)? "Sim" : "Não";
        return response()->json([
            $user
        ], 200);
    }
}
