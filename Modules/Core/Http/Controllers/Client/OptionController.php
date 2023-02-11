<?php

namespace Modules\Core\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Entities\Option;
use Modules\Core\Repositories\OptionRepository;

class OptionController extends Controller
{
    public function get(string $key)
    {
        $option = Option::query()
            ->where('key', $key)
            ->firstOrFail();

        return $this->success([
            'value' => $option->value
        ]);
    }

    public function set(string $key, Request $request)
    {
        $option = OptionRepository::createOrUpdate($key, $request->input('value'));

        return $this->success([
            'value' => $option->value
        ]);
    }
}
