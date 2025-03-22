<?php

namespace App\Helpers;

class DateHelper
{
    /**
     * Calcula a idade com base na data de nascimento
     * Considera o mês e dia para o cálculo preciso
     * 
     * @param string $birthDate Data de nascimento no formato Y-m-d
     * @return int|null Idade calculada ou null se a data for inválida
     */
    public static function calculateAge($birthDate)
    {
        if (empty($birthDate)) {
            return null;
        }
        
        try {
            $birth = new \DateTime($birthDate);
            $today = new \DateTime('today');
            
            $age = $today->diff($birth)->y;
            
            return $age;
        } catch (\Exception $e) {
            error_log("Erro ao calcular idade: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Formata a data do formato Y-m-d para d/m/Y
     * 
     * @param string $date Data no formato Y-m-d
     * @return string|null Data formatada ou null se a data for inválida
     */
    public static function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        try {
            $dateObj = new \DateTime($date);
            return $dateObj->format('d/m/Y');
        } catch (\Exception $e) {
            error_log("Erro ao formatar data: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verifica se a data é válida
     * 
     * @param string $date Data a ser verificada
     * @return bool True se a data for válida, false caso contrário
     */
    public static function isValidDate($date)
    {
        if (empty($date)) {
            return false;
        }
        
        try {
            $dateObj = new \DateTime($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}