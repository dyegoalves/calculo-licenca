<?php

namespace App\Http\Controllers;

use App\Processo;
use DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use NumberFormatter;

class ConsultarController extends Controller
{
    public function index()
    {
       $bloqueio = 'sim';
       return view("sistema.consultas.consultarprocesso")->with(compact('bloqueio'));
    }

	public function fazerconsultarprocesso()
	{




			$numprocessocount = Processo::where('num_processo', Input::get("num_processo"))->count();
			if(Input::get("num_processo") == ""){
				$msgerro = "Informe um Numero do processo para consultar , não pode ser vazio";
				return back()->withInput()->with(compact('msgerro'));
			}
			if($numprocessocount < 1){
				$msgerro = "O processo consultado nº ". Input::get('num_processo'). " nao existe cadastrado no sistema";
				return back()->withInput()->with(compact('msgerro'));
			}

			//Todos os dados relacionados ao processo sao recuperado e enviados para view consultarprocesso em forma de Session
			$processo = DB::table('processos')->where('num_processo', Input::get("num_processo"))->first();
			$processo  = Processo::find($processo->id);
			$empreendimento = $processo->empreendimento;
			$empresa = $empreendimento->empresa;
			$atividade = $empreendimento->atividade;
			$subatividade = $empreendimento->subatividade;
			$porte  = $empreendimento->porte;
			$ppd = $empreendimento->subatividade->ppd;
			$valordalicenca = $processo->calculo->valor;
			$fmt = numfmt_create( "pt_BR" ,NumberFormatter::CURRENCY );
			$valordalicenca = numfmt_format($fmt, $valordalicenca);
			$valordalicenca = str_replace('R$', "" , $valordalicenca);


		  $tipo = DB::table('tipoprecos')
				->where		('LP' , $valordalicenca)
				->orWhere	('LI' , $valordalicenca)
				->orWhere	('LO' , $valordalicenca)
				->get();

			if($tipo[0]->LP == $valordalicenca ){

				$tipodelicenca = "LP";

			}

			if($tipo[0]->LI == $valordalicenca ){

				$tipodelicenca = "LI";

			}

			if($tipo[0]->LO == $valordalicenca ){

				$tipodelicenca = "LO";

			}


		 	return back()
					->with(compact( 'empreendimento','empresa' , 'atividade' , 'subatividade' , 'tipodelicenca', 'porte' , 'ppd' , 'valordalicenca'))
					->withInput();
	}
}
