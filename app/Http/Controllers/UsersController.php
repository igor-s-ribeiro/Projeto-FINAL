<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Hash


class UsersController extends Controller
{

    public function emailAndPassword(Request $request)
    {
       $user = User::where("email", '=', $request->email)
        ->first();

        $user = User::where("password", '=', $request->password)
        ->first();

        if(! $user){
            return response()->json([
                'success'   =>  false,
                'errors'    => ['O usuário não existe']
            ], 422);
        }

        $userData = $user->toArray() ;
        $userData['password'] = $request->password;
    // $passwordMatches = Hash::check($request->input('password'), $user->password)


        //retorno para o front(LOGIINNN)!!
        return response()->json( $userData   );
    }

    //Criação de usuário! Valida e faz o commite no banco
    public function create(Request $request)
    {
        //Regras
        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:100',
            'password_confirmation' =>  'same:password',
            'name' => 'required',
            'surname' => 'nullable',
            'cpf' => 'nullable|required',
        ];
        //Mensagens de erro
        $messages = [
            'email.required'    =>  'Por favor, informar o email',
            'email.email'    =>  'O e-mail é inválido',
            'email.unique'  =>  'O e-mail já está cadastrado',
            'password.required' =>  'A senha é obrigatória',
            'password.min' => 'A senha deve possuir no mínimo 6 caracteres',
            'password.max' => 'A senha ultrapassou o limite de 100 caracteres',
            'password_confirmation.same' =>  'A senha não confere',
            'surname.required' => 'O sobrenome é obrigatório',
            'cpf.required' => 'O CPF é obrigatório'
        ];

        //Validação da operação, trigger do erro
        $validator = validator($request->all(), $rules, $messages  );
        if($validator->fails()){
            return response()->json([
                'success'   =>  false,
                'errors'    =>  $validator->errors()->toArray()
            ], 422);
        }

        $user = null;

        DB::beginTransaction();
        // Dentro de uma transaction não é possível fazer select (consulta) no banco de dados
        try{
            $user = User::create([
                'email' =>  $request->email,
                'name'  =>  $request->name,
                'password'  =>$request->password,
                'cpf' => $request->cpf
            ]);

        }catch(QueryException $e){
            DB::rollBack();
            if(app()->environment() == 'production'  ){
                return response()->json([
                    'success'   =>  false,
                    'errors'    =>  ['Erro ao cadastrar usuario. Contate o suporte']
                ], 422);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'errors'    =>  [$e->getMessage()]
                ], 422);
            }
        }
        DB::commit();

        //Se tudo estiver Ok, será retornado o Data(dados do User, com excessão da senha)
        return response()->json([
            'success' => true,
            'data'  => $user->toArray()
            ], 201);
    }
}
