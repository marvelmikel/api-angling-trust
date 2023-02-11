<?php

namespace Modules\Members\Repositories;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use Modules\Members\Entities\Fishery;

class FisheryRepository
{
    public static function create(array $data)
    {
//        try {
//
//            DB::beginTransaction();

            $user = Sentinel::registerAndActivate($data['user']);

            unset($data['user']);

            $fishery = new Fishery();
            $fishery->user_id = $user->id;
            $fishery->fill($data);
            $fishery->save();

//            DB::commit();

            return $fishery;

//        } catch (\Exception $exception) {
//            DB::rollback();
//            return null;
//        }
    }

    public static function update(Fishery $fishery, array $data)
    {
        try {

            $fishery->fill($data);
            $fishery->save();

            $fishery->user->update($data['user']);

            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function completeRegistration(Fishery $fishery)
    {
        $fishery->registered_at = Carbon::now();
        $fishery->expires_at = Carbon::now()->addYear();

        return $fishery->save();
    }

    public static function delete(Fishery $fishery)
    {
        return DB::transaction(function() use ($fishery) {
            $user = $fishery->user;
            $fishery->delete();
            $user->delete();
            return true;
        });
    }
}
