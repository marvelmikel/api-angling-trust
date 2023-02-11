<?php

use Modules\Auth\Entities\User;
use Modules\Members\Entities\Member;

function wp_url($url = null)
{
    if (!$url) {
        return env('WP_URL');
    }

    return env('WP_URL') . '/' . $url;
}

function can_get_current_user()
{
    return auth()->guard('api')->user() !== null;
}

function current_user(): ?User
{
    return auth()->guard('api')->user();
}

function success_response($data = null)
{
    $response = [
        'success' => true
    ];

    if ($data) {
        $response['data'] = $data;
    }

    return response($response);
}

function error_response($message = null, $code = null)
{
    $response = [
        'success' => false,
        'error' => [
            'message' => $message ?? 'Oops! Something went wrong.',
            'code' => $code ?? 500
        ]
    ];

    return response($response);
}

function implode_skip_empty($glue, $parts)
{
    $data = [];

    foreach ($parts as $part) {
        if ($part) {
            $data[] = $part;
        }
    }

    return implode($glue, $data);
}

function current_member($with = []): ?Member
{
    if (!$user = current_user()) {
        return null;
    }

    return Member::query()
        ->with($with)
        ->where('user_id', $user->id)
        ->firstOrFail();
}

function random_reference($length = 18)
{
    return substr(md5(microtime()), 0, $length);
}

function get_site_origin()
{
    return request()->header('S-Origin');
}

function as_price($total)
{
    return "£" . number_format($total / 100, 2);
}
