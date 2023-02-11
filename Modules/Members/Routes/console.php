<?php

use Modules\Members\Services\MembershipPriceUpdater;

Artisan::command(
    'members:update-prices <input>',
    function(MembershipPriceUpdater $membershipPriceUpdater) {
        $membershipPriceUpdater->generate();
    });
