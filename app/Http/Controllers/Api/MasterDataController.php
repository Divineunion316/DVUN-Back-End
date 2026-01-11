<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use App\Models\City;

class MasterDataController extends Controller
{
    public function index()
    {
        return response()->json([
            'states' => $this->states(),
            'cities' => $this->cities(),
            'genders' => $this->genders(),
            'marital_statuses' => $this->maritalStatuses(),
            'mother_tongues' => $this->motherTongues(),
            'known_languages' => $this->knownLanguages(),
            'heights' => $this->heights(),
            'weights' => $this->weights(),
        ]);
    }

    private function states()
    {
        return State::select('id', 'name')->orderBy('name')->get();
    }

    private function cities()
    {
        return City::select('id', 'state_id', 'name')->orderBy('name')->get();
    }

    public function citiesByState($stateId)
    {
        return City::where('state_id', $stateId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    private function genders()
    {
        return ['male', 'female', 'other'];
    }

    private function maritalStatuses()
    {
        return ['single', 'married', 'divorced', 'widowed'];
    }

    private function motherTongues()
    {
        return ['Tamil', 'Telugu', 'Hindi', 'Kannada', 'Malayalam', 'English'];
    }

    private function knownLanguages()
    {
        return ['Tamil', 'English', 'Hindi', 'Telugu', 'Kannada', 'Malayalam'];
    }

    private function heights()
    {
        return range(140, 200);
    }

    private function weights()
    {
        return range(40, 120);
    }
}
