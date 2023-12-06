<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebaController extends Controller
{
    CONST FIRST_RANGE = 0.01;
    CONST SECOND_RANGE = 0.05;
    CONST THIRD_RANGE = 0.1;
    CONST MIN_DAYS_WORKED = 30;
    CONST USER = 'admin@admin.com';
    CONST PASSWORD = 'Clave123+';

    public function index(Request $request)
    {
        try {
            $user = $request->usuario;
            $password = $request->contraseña;
            $salary = (double)$request->salario_base;
            $worked_days = (int)$request->dias_trabajados;
            $sales_value = (int)$request->valor_ventas;
            $recalculated_percentage = 0;
            $salary_recalculated = $salary;

            $this->checkCredentials($user, $password);

            if($salary <= 0 || $worked_days <= 0 || $sales_value <= 0 ){
                return response()->json("Uno de los valores ingresados es incorrecto", 202);
            }

            if($worked_days < self::MIN_DAYS_WORKED) {
                $salary_recalculated = $this->getNewSalary($salary, $worked_days);
                $recalculated_percentage = $this->getPercentage($worked_days);
            }

            $additional_value = $this->getAdditionValue($salary_recalculated, $sales_value);

            $salary_response = (double)$salary_recalculated + (double)$additional_value;

            $response = [
                'salario_base' => $salary,
                'dias_trabajados' => $worked_days,
                'valor_ventas' => $sales_value,
                'salario_calculado' => $salary_response,
                'porcentage_prorrateo' => $recalculated_percentage
            ];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $error_message = $th->getMessage();
            return response()->json(['error' => $error_message], $th->getCode());
        }
    }

    public function checkCredentials($user, $password) 
    {
        if(!$user || !$password) {
            throw new \Exception("Ingresar las credenciales", 202);
        }
        if(!($user == self::USER && $password = self::PASSWORD)) {
            throw new \Exception("Las credenciales son incorrectas", 202);
        }
    }

    public function getAdditionValue($salary, $sales_value) 
    {
        if($sales_value <= 1000) {
            return $salary *  self::FIRST_RANGE;
        } elseif($sales_value <= 5000) {
            return $salary *  self::SECOND_RANGE;
        } elseif($sales_value > 5000){
            return $salary *  self::THIRD_RANGE;
        } else {
            throw new \Exception("El valor del salario es incorrecto", 202);
        }
    }

    public function getNewSalary($salary, $worked_days)
    {
        return ($salary*$worked_days)/self::MIN_DAYS_WORKED;
    }

    public function getPercentage($worked_days)
    {
        return 100 - ((100*$worked_days)/self::MIN_DAYS_WORKED);
    }
}
