<?php

namespace Modules\Members\Repositories;

use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use DB;
use Modules\Members\Entities\TradeMember;

class TradeMemberRepository
{
    public static function create(array $data)
    {
        try {

            DB::beginTransaction();

            $user = Sentinel::registerAndActivate($data['user']);

            $tradeMember = new TradeMember();
            $tradeMember->user_id = $user->id;
            $tradeMember->fill($data);
            $tradeMember->save();

            DB::commit();

            return $tradeMember;

        } catch (\Exception $exception) {
            DB::rollback();
            return null;
        }
    }

    public static function update(TradeMember $tradeMember, array $data)
    {
        try {

            $tradeMember->fill($data);
            $tradeMember->save();

            $tradeMember->user->update($data['user']);

            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    public static function completeRegistration(TradeMember $tradeMember)
    {
        $tradeMember->registered_at = Carbon::now();
        $tradeMember->expires_at = Carbon::now()->addYear();

        return $tradeMember->save();
    }

    public static function delete(TradeMember $tradeMember)
    {
        return DB::transaction(function() use ($tradeMember) {
            $user = $tradeMember->user;
            $tradeMember->delete();
            $user->delete();
            return true;
        });
    }
}
