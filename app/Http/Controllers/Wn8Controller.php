<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Wn8Controller extends Controller
{
    public function __construct()
    {

    }

    /**
     * Retrieves expected tank values and puts them in a readable list.
     *
     * @return array
     */
    private function getExpectedTankValues(): array
    {
        $expectedTankValuesJson = file_get_contents("https://static.modxvm.com/wn8-data-exp/json/wn8exp-2021-04-13.json");
        $expectedTankValuesArray = json_decode($expectedTankValuesJson, true);
        $sortedExpectedValues = [];

        foreach($expectedTankValuesArray['data'] as $key => $value) {
            $tankId = $value['IDNum'];
            unset($value['IDNum']);

            $sortedExpectedValues[$tankId] = $value;
        }

        return $sortedExpectedValues;
    }

    private function getWn8(array $expectedValues, array $playerValues, int $tankId): ?float
    {
        $expDmg = $expectedValues[$tankId]["expDamage"];
        $expSpot = $expectedValues[$tankId]["expSpot"];
        $expFrag = $expectedValues[$tankId]["expFrag"];
        $expDef = $expectedValues[$tankId]["expDef"];
        $expWinRate = $expectedValues[$tankId]["expWinRate"];

        $avgDmg = $vehicle_damage / $vehicle_battles;
        $avgSpot = $vehicle_spotted / $vehicle_battles;
        $avgFrag = $vehicle_frags / $vehicle_battles;
        $avgDef = $vehicle_basedefense / $vehicle_battles;

        $rDAMAGE = $avgDmg / $expDmg;
        $rSPOT = $avgSpot / $expSpot;
        $rFRAG = $avgFrag / $expFrag;
        $rDEF = $avgDef / $expDef;
        $rWIN = $vehicle_winrate / $expWinRate;

        $rWINc = max(0, ( $rWIN - 0.71 ) / ( 1 - 0.71 ));
        $rDAMAGEc = max(0, ( $rDAMAGE - 0.22 ) / ( 1 - 0.22 ));
        $rFRAGc = max(0, min($rDAMAGEc + 0.2, ( $rFRAG - 0.12 ) / ( 1 - 0.12 )));
        $rSPOTc = max(0, min($rDAMAGEc + 0.1, ( $rSPOT - 0.38 ) / ( 1 - 0.38 )));
        $rDEFc = max(0, min($rDAMAGEc + 0.1, ( $rDEF - 0.10 ) / ( 1 - 0.10 )));

        $wn8 = ( 980 * $rDAMAGEc ) + ( 210 * $rDAMAGEc * $rFRAGc ) + ( 155 * $rFRAGc * $rSPOTc ) + ( 75 * $rDEFc * $rFRAGc ) + ( 145 * MIN(1.8, $rWINc) );

        return $wn8;
    }

    /**
     * @param int $tankId
     * @param array $playerValues
     * @return float|null
     * @throws Exception
     */
    public function getWn8ByTank(int $tankId, array $playerValues): ?float
    {
        $expectedValues = $this->getExpectedTankValues();

        if(!in_array($tankId, $expectedValues)) {
            throw new Exception('Given tank ID does not exist.');
        }

        return $this->getWn8($expectedValues, $playerValues, $tankId);
    }

    /**
     * @param array $playerValues
     * @param array $playerVehicles
     * @return array
     */
    public function getWn8AllTanks(array $playerValues, array $playerVehicles): array
    {
        $expectedValues = $this->getExpectedTankValues();

        $wn8 = [];
        foreach ($playerVehicles as $tanktype => $tank) {
            $wn8[$tank] = $this->getWn8($expectedValues, $playerValues, $tank);
        }

        return $wn8;
    }

    public function getWn8Total()
    {

    }
}
