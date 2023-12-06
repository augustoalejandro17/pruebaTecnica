<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebaController extends Controller
{
    CONST FIRST_RANGE = 0.01;
    CONST SECOND_RANGE = 0.05;
    CONST THIRD_RANGE = 0.1;

    public function index(Request $request)
    {
        $user = $request->usuario;
        $password = $request->contraseña;
        $salary = (double)$request->salario_base;
        $worked_days = (int)$request->dias_trabajados;
        $sales_value = (int)$request->valor_ventas;

        $additionalValue = 0;

        if($salary <= 0 || $worked_days <= 0 || $sales_value <= 0 ){
            return response()->json("Uno de los valores ingresados es incorrecto", 202);
        }

        if($sales_value <= 1000) {
            $additionalValue = $salary *  self::FIRST_RANGE;
        } elseif($sales_value <= 5000) {
            $additionalValue = $salary *  self::SECOND_RANGE;
        } elseif($sales_value > 5000){
            $additionalValue = $salary *  self::THIRD_RANGE;
        } else {
            return response()->json("El valor del salario es incorreco", 202);
        }
        return response()->json($additionalValue);
    }
}
