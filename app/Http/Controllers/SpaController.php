<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Domain;
use App\TimezoneWidget;

class SpaController extends Controller
{
    /**
     * Get the SPA view.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
      $domain = explode('.', $request->getHost());

      $subdomain_first = $domain[0];

      $domain = Domain::where('domain', $subdomain_first)->first();

      session(['domain' => $domain->id]);
      
      return view('spa')->with('domain', $domain);
    }
    
    public function timeZones() {
        return response()->json(TimezoneWidget::timezoneValues());
    }

}
