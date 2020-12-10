<?php

namespace Ntpages\LaravelRedirector\Events;

use Ntpages\LaravelRedirector\Models\Redirect;
use Illuminate\Queue\SerializesModels;

abstract class RedirectorEvent
{
    use SerializesModels;

    /**
     * @var Redirect
     */
    public $redirect;

    public function __construct(Redirect $redirect)
    {
        $this->redirect = $redirect;
    }
}
