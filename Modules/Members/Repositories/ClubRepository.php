<?php

namespace Modules\Members\Repositories;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use Modules\Core\Entities\Address;
use Modules\Members\Entities\Club;
use Modules\Members\Entities\Member;

class ClubRepository
{
    public static function create(array $data)
    {
        try {

            DB::beginTransaction();

            $user = Sentinel::registerAndActivate($data['user']);

            $club = new Club();
            $club->user_id = $user->id;
            $club->fill($data);
            $club->save();

            DB::commit();

            return $club;

        } catch (\Exception $exception) {
            DB::rollback();
            return null;
        }
    }

    public static function update(Club $club, array $data)
    {
        try {

            $club->fill($data);
            $club->save();

            $club->user->update($data['user']);

            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function completeRegistration(Club $club)
    {
        $club->registered_at = Carbon::now();
        $club->expires_at = Carbon::now()->addYear();

        return $club->save();
    }

    public static function delete(Club $club)
    {
        return DB::transaction(function() use ($club) {
            $user = $club->user;
            $club->delete();
            $user->delete();
            return true;
        });
    }
}
