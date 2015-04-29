<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Routing\Controller;
use View;

class BaseController extends Controller
{
    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if (! is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }
}
