<?php

namespace Gamebetr\Api\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DBD\GlobalAuth\Facades\GlobalAuth;

class TemplateService
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Build variables for landing page templates
     * t is used for template id
     * a is used for affiliate id (player_id), however, this is not tracked in laravel
     * a is tracked in Drupal with cookies for SEO benefits
     * and to avoid directly linking to iframed subdomain
     *
     * By default a random page will be used from the available templates for that domain
     * If a specific template id is passed in that template will be used instead
     */
    public function getVariables()
    {

        // Local dev credentials
        // $variables = [
        //     'domain_id' => 1000001,
        //     'name' => 'Playbetr',
        //     'client_url' => 'https://www.playbetr.com',
        // ];

        // load domain to get logo and other stuff
        $domain = GlobalAuth::getDomain();
        $variables = [];
        $variables['domain_id'] = $domain->id;
        $variables['name'] = $domain->name;

        $variables['logo'] = 'https://static.cdn1.io/' . $variables['domain_id'] . '/assets/logo-light.png';
        $variables['logo-light'] = 'https://static.cdn1.io/' . $variables['domain_id'] . '/assets/logo-light.png';
        $variables['logo-dark'] = 'https://static.cdn1.io/' . $variables['domain_id'] . '/assets/logo-dark.png';

        // used for links like privacy, terms and whatever else
        $variables['client_url'] = $domain->variable('web_base_uri');

        // get correct template ID to use
        $variables['template_id'] = $this->getTemplateId($variables['domain_id']);

        // handle locale
        if ($this->request->locale) {
            \App::setLocale($this->request->locale);
        }
        $variables['locale'] = \App::getLocale();

        return $variables;
    }

    public function getTemplateId(int $domain_id) {
        $mappings = $this->templateDomainMappings();

        // if template passed in use that if enabled for domain
        if ($t = $this->request->query('t')) {
            // if in array use this id
            if (in_array($t, $mappings[$domain_id])) {
                return $t;
            }
            // if not continue and use default
        }

        // default to random template
        $template_id = $mappings[$domain_id][array_rand($mappings[$domain_id])];

        return $template_id;
    }

    /**
     * This shows which template ids belong to which domain id
     * In the future this could be done in a database table, domain variable or config
     * For now this should be okay to keep management clean and simple
     * as the details and tests are ironed out
     */
    public function templateDomainMappings() {
        return [
            1000001 => [
                '8',
                '10',
                '12',
                '13',
                '14',
                '15',
                '19',
                '20',
                '25',
                '26',
                '27',
                '28',
                '29',
                '30',
                '31',
                '32',
                '33',
                '34',
                '35',
            ],
            1000008 => [
                '5',
                '6',
                '7',
                '9',
                '21',
                '22',
                '23',
                '24',
            ],
        ];
    }
}
