<?php

namespace Gamebetr\Api\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Gamebetr\Api\Services\TemplateService;

class LandingPage extends Controller
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

        return view('api::landingpage.' . $request->page)->with('variables', $variables);
    }
}
