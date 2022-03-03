<?php

namespace App\Http\Repositories;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

class LocationRepository
{
    protected $country;
    protected $state;
    protected $city;

    public function __construct(Country $country,State $state,City $city)
    {
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
    }

    public function getAll($data) {
        //return $this->loan::with('banks')->find($data['id']);
    }

    public function getByParams($data) {
        if(isset($data['type'])){
            if($data['type']=='country') {
                return $this->country->orderBy('name')->get();
            }
            elseif($data['type']=='state' || $data['type']=='business_state') {
                return $this->state->where('country_id',$data['id'])->orderBy('name')->get();
            }
            elseif($data['type']=='city' || $data['type']=='business_city') {
                return $this->city->where('state_id',$data['id'])->orderBy('name')->get();
            }
            else {
                throw new \Exception('Invalid type');
            }
        }
        else {
            throw new \Exception('Parameter Missing');
        }
    }
}