<?php
/**
 * Created by PhpStorm.
 * User: blede
 * Date: 31.03.2019
 * Time: 22:40
 */

namespace App\ClingoModels;

interface ClingoMapper
{
    public function loadFromClingo( $string );
    public function getClingo( $withRelationShips );
    public function getClingoID();
}