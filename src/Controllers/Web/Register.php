<?php

namespace Gamebetr\Api\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\TemplateService;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Cdp\Cdp;

class Register extends Controller
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

        // return view('api::user.default.register')->with('variables', $variables);

        // do CDP page view event here
        $cdp = new Cdp(['domainId' => GlobalAuth::getDomain()->id]);
        $cdp->page(
            [
                'name' => 'Laravel Page',
                'anonymousId' => 'anonid',
            ]
        );
        // do CDP starts registration event here
        $cdp->track(
            [
                'event' => 'starts_registration',
                'anonymousId' => 'anonid',
            ]
        );

        return view('api::user.custom.' . $variables['template_id'] . '.register')->with('variables', $variables);
    }
}
