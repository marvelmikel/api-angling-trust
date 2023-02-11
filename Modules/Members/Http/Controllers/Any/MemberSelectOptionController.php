<?php

namespace Modules\Members\Http\Controllers\Any;

use App\Http\Controllers\Controller;
use Modules\Members\Entities\MemberSelectOption;

class MemberSelectOptionController extends Controller
{
    public function index()
    {
        $options = [];

        foreach (MemberSelectOption::all() as $option) {
            if (!isset($options[$option->type])) {
                $options[$option->type] = [];
            }

            $options[$option->type][] = [
                'id' => $option->slug,
                'name' => $option->name
            ];
        }

        return $this->success([
            'options' => $options
        ]);
    }
}
