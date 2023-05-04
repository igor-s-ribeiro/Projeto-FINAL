<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pets;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


class PetsController extends Controller
{
    public function petsCreate(Request $request)
    {
        //Regras
        $rules = [
            'nome' => 'nullable|required|max:150',
            'raca' => 'nullable|required|max:100',
            'cor' =>  'required',
            'idade' => 'required',
            'sexo' => 'required',
            'peso' => 'required',
            'porte' => 'required',
            'comportamento' => 'required',
            'adestramento' => 'required',
            'origem_da_raca' => 'required',
            'condicoes_especiais' => 'required',
            'expectativa_de_vida' => 'required',
        ];
        //Mensagens de erro
        $messages = [
            'nome.nullable'    =>  'Por favor, informe o nome do animal',
            'nome.required'    =>  'O nome é inválido',
            'nome.max'  =>  'Limite de caracteres ultrapassado',
            'raca.nullable' =>  'Por favor, informe a raça do animal',
            'raca.required' => 'Raça inválida',
            'raca.max' => 'O nome raca ultrapassou o limite de caracteres',
            'cor.required' =>  'Cor inválida',
            'idade.required' => 'Idade inválida',
            'sexo.required' => 'Sexo inválido',
            'peso.required' => 'Peso inválido',
            'porte.required' => 'Porte inválido',
            'comportamento.required' => 'Comportamento inválido',
            'adestramento.required' => 'Adestramento inválido',
            'origem_da_raca.required' => 'Origem inválida',
            'condicoes_especiais.required' => 'Condições especiais inválida',
            'expectativa_de_vida.required' => 'Expectativa de vida inválida'
        ];

        //Validação da operação, trigger do erro
        $validator = validator($request->all(), $rules, $messages  );
        if($validator->fails()){
            return response()->json([
                'success'   =>  false,
                'errors'    =>  $validator->errors()->toArray()
            ], 422);
        }

        $pets = null;

        DB::beginTransaction();
        // Dentro de uma transaction não é possível fazer select (consulta) no banco de dados
        try{
            $pets = Pets::create([
                'nome' =>  $request->nome,
                'raca'  =>  $request->raca,
                'cor'  => $request->cor,
                'idade' => $request->idade,
                'sexo' => $request->sexo,
                'peso' => $request->peso,
                'porte' => $request->porte,
                'comportamento' => $request->comportamento,
                'adestramento' => $request->adestramento,
                'origem_da_raca' => $request->origem_da_raca,
                'condicoes_especiais' => $request->condicoes_especiais,
                'expectativa_de_vida' => $request->expectativa_de_vida

            ]);

        }catch(QueryException $e){
            DB::rollBack();
            if(app()->environment() == 'production'){
                return response()->json([
                    'success'   =>  false,
                    //Erro do banco de dados
                    'errors'    =>  ['Erro ao cadastrar o Pet. Contate o suporte']
                ], 422);
            }else{
                return response()->json([
                    'success'   =>  false,
                    'errors'    =>  [$e->getMessage()]
                ], 422);
            }
        }
        DB::commit();

        //Se tudo estiver Ok, será retornado o Data(dados do pet)
        return response()->json([
            'success' => true,
            'data'  => $pets->toArray()
            ], 201);
    }

    public function consultaPets(Request $request)
    {
       $pets = Pets::where("nome", '=', $request->nome)
        ->first();

        //$pets = Pets::where("dono", '=', $request->dono)
        //->first()

        if(! $pets){
            return response()->json([
                'success'   =>  false,
                'errors'    => ['O Pet não existe']
            ], 422);
        }

        $petsData = $pets->toArray();

        //retorno para o front(PÉTIIISSS)!!
        return response()->json( $petsData);
    }
}
