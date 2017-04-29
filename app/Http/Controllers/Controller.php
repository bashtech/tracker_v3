<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Toastr;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $toastMessage
     */
    protected function showToast($toastMessage)
    {
        Toastr::success(
            $toastMessage,
            "Success",
            [
                'positionClass' => 'toast-top-right',
                'progressBar' => true
            ]
        );
    }
}
