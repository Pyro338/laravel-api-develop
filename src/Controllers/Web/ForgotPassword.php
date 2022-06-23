<?php

namespace Gamebetr\Api\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\TemplateService;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Cdp\Cdp;

class ForgotPassword extends Controller
{
    /**
     * Invoke.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // process correct template
        $template = new TemplateService($request);
        $variables = $template->getVariables();

        // do CDP page view event here
        $cdp = new Cdp(['domainId' => GlobalAuth::getDomain()->id]);
        $cdp->page(
            [
                'name' => 'Laravel Page',
                'anonymousId' => 'anonid',
            ]
        );

        return view('api::user.default.forgotpassword')->with('variables', $variables);
    }
}
