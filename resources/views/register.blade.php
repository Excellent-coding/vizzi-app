<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>MED Week 2020 Registration – Vizzi – Virtual Event Platform</title>

  <link rel="stylesheet" href="{{asset('assets/fonts/simple-line-icons/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="{{asset('assets/fonts/iconsmind-s/css/iconsminds.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/vendor/bootstrap4/bootstrap.min.css')}}">
  <link href="{{asset('assets/font-awesome/css/fontawesome.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/font-awesome/css/solid.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/chosen/chosen.min.css')}}" rel="stylesheet">
  <style>

    body {
        background-color: #c0c0c0!important;
        overflow-x: hidden;
    }

     div.register-box {
        background-color: #fff;
        padding: 25px;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.12), 0 2px 4px 0 rgba(0,0,0,0.08);
    }

    img {
        width: 100%;
    }

    div.chosen-container {
        font-size: 1.3rem;
        line-height: 1.6rem;
    }
    .chosen-container .chosen-results li, .chosen-container-multi .chosen-choices li.search-choice span {
        font-size: 1.3rem;
        line-height: 1.6rem;
    }
    .pac-container:after {
        background-image: none !important;
        height: 0px;
    }
    .chosen-container-active .chosen-choices, .chosen-container-multi .chosen-choices {
        border-radius: .25rem;
        border: 1px solid #ced4da;
        line-height: 1.6rem;
    }

</style>
</head>
<body class="">

<div class="container-flex">
<div class="row">
    <div class="col-sm-12">
        <img src="/assets/img/medweekbanner.jpg"/>
    </div>
</div>
<div class="row">
<div class="col-sm-12">&nbsp;</div>
</div>
<div class="row mx-3">
<div class="register-box col-sm-12">
<h3>Registration for the MED Week 2020 is now open! </h3>
<p>We are excited to celebrate 2020 National Minority Enterprise Development Week as a virtual experience!</p>
<h3>Registration for the Virtual Conference is free!</h3>
<p>
If you would like to take part in MED Week 2020, please fill in your details in this Event Registration Form below and you will be automatically registered. Once Registered you will get a link to complete your Virtual Event Profile one week before the event starts. 
</p>
        <form method="post" action="{{ url('/register') }}" id="register-form">

            {!! csrf_field() !!}

                <input type="hidden" name="id" value="{{ $domain->id }}"/>
                <input type="hidden" name="redirect" value="register-success"/>

    <div class="register-box-body">


        
        
  <fieldset class="form-group has-feedback field-name{{ $errors->has('sessions') ? ' has-error' : '' }}">
    <div class="row">
      <legend class="col-form-label col-sm-2 pt-0"><strong>Select Sessions</strong></legend>
                
                <span class="glyphicon glyphicon-user form-control-feedback"></span>

                @if ($errors->has('register_metadata'))
                    <span class="help-block">
                        <strong>{{ $errors->first('register_metadata') }}</strong>
                    </span>
                @endif


      <div class="col-sm-10">


        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-0" name="register_metadata[]" class="form-check-input" value="thriving-through-adversity-calling-all-warriors" aria-labelledby="nf-label-field-26-0">
        <label for="register_metadata[]-0" id="nf-label-field-26-0" class="form-check-label"/>Thriving Through Adversity: Calling all Warriors</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-1" name="register_metadata[]" class="form-check-input" value="keep-moving-forward-take-your-business-from-stalled-to-stellar" aria-labelledby="nf-label-field-26-1">
        <label for="register_metadata[]-1" id="nf-label-field-26-1" class="form-check-label"/>Keep Moving Forward: Take Your Business from Stalled to Stellar</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-2" name="register_metadata[]" class="form-check-input" value="growing-your-business-with-or-without-capital" aria-labelledby="nf-label-field-26-2">
        <label for="register_metadata[]-2" id="nf-label-field-26-2" class="form-check-label"/>Growing Your Business: With or Without Capital</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-3" name="register_metadata[]" class="form-check-input" value="global-shift-disruption-trends-and-opportunities-related-to-exporting-and-international-business" aria-labelledby="nf-label-field-26-3">
        <label for="register_metadata[]-3" id="nf-label-field-26-3" class="form-check-label"/>Global Shift: Disruption, Trends, and Opportunities related to Exporting and International Business</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-4" name="register_metadata[]" class="form-check-input" value="multiple-perspectives-on-increasing-capital-for-minority-business" aria-labelledby="nf-label-field-26-4">
        <label for="register_metadata[]-4" id="nf-label-field-26-4" class="form-check-label"/>Multiple Perspectives on Increasing Capital for Minority Business</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-5" name="register_metadata[]" class="form-check-input" value="enduring-truths-chamber-presidents-talk-adversity-policy-and-persistence" aria-labelledby="nf-label-field-26-5">
        <label for="register_metadata[]-5" id="nf-label-field-26-5" class="form-check-label"/>Enduring Truths: Chamber Presidents Talk Adversity, Policy, and Persistence</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-6" name="register_metadata[]" class="form-check-input" value="supply-chain-shift-how-mbes-strengthened-a-manufacturer-s-response-to-disruption" aria-labelledby="nf-label-field-26-6">
        <label for="register_metadata[]-6" id="nf-label-field-26-6" class="form-check-label"/>Supply Chain Shift: How MBEs Strengthened a Manufacturer’s Response to Disruption</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-7" name="register_metadata[]" class="form-check-input" value="diversity-wins-lessons-in-contracting-and-inclusion-from-local-level-transportation-procurement" aria-labelledby="nf-label-field-26-7">
        <label for="register_metadata[]-7" id="nf-label-field-26-7" class="form-check-label"/>Diversity Wins: Lessons in Contracting and Inclusion from Local- level Transportation Procurement</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-8" name="register_metadata[]" class="form-check-input" value="straight-talk-planning-positioning-and-pitching-your-business" aria-labelledby="nf-label-field-26-8">
        <label for="register_metadata[]-8" id="nf-label-field-26-8" class="form-check-label"/>Straight Talk: Planning, Positioning, and Pitching Your Business</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-9" name="register_metadata[]" class="form-check-input" value="artificial-intelligence-ai-will-dominate-sourcing-in-the-next-5-years-are-you-ready" aria-labelledby="nf-label-field-26-9">
        <label for="register_metadata[]-9" id="nf-label-field-26-9" class="form-check-label"/>Artificial Intelligence (AI) Will Dominate Sourcing in the Next 5 years. Are You Ready?</label>
    </div>



        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-11" name="register_metadata[]" class="form-check-input" value="embracing-digital-transformation-in-the-new-business-economy" aria-labelledby="nf-label-field-26-11">
        <label for="register_metadata[]-11" id="nf-label-field-26-11" class="form-check-label"/>Embracing Digital Transformation in the New Business Economy</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-12" name="register_metadata[]" class="form-check-input" value="e-everything-using-design-thinking-as-a-framework-for-engaging-stakeholders" aria-labelledby="nf-label-field-26-11">
        <label for="register_metadata[]-12" id="nf-label-field-26-12" class="form-check-label"/>E-Everything Using Design Thinking as a Framework for Engaging Stakeholders


</label>
    </div>

    
    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-13" name="register_metadata[]" class="form-check-input" value="how-do-mbes-embrace-digital-transformation-in-the-new-business-economy" aria-labelledby="nf-label-field-26-10">
        <label for="register_metadata[]-13" id="nf-label-field-26-13" class="form-check-label"/>How do MBEs Embrace Digital Transformation in the New Business Economy</label>
    </div>



        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-14" name="register_metadata[]" class="form-check-input" value="how-i-did-it-reimagining-impact" aria-labelledby="nf-label-field-26-11">
        <label for="register_metadata[]-14" id="nf-label-field-26-14" class="form-check-label"/>How I Did It: Reimagining Impact</label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-15" name="register_metadata[]" class="form-check-input" value="how-i-did-it-the-art-of-building-the-ship" aria-labelledby="nf-label-field-26-15">
        <label for="register_metadata[]-15" id="nf-label-field-26-15" class="form-check-label"/>How I Did It: The Art of Building "The Ship" </label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-16" name="register_metadata[]" class="form-check-input" value="how-i-did-it-the-competitive-advantage" aria-labelledby="nf-label-field-26-16">
        <label for="register_metadata[]-16" id="nf-label-field-26-16" class="form-check-label"/>How I Did It: The Competitive Advantage </label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-17" name="register_metadata[]" class="form-check-input" value="how-i-did-it-the-power-of-pivot" aria-labelledby="nf-label-field-26-17">
        <label for="register_metadata[]-17" id="nf-label-field-26-17" class="form-check-label"/>How I Did It: The Power of Pivot </label>
    </div>

    

        <div class="form-check">
        <input type="checkbox" id="register_metadata[]-18" name="register_metadata[]" class="form-check-input" value="how-i-did-it-creating-opportunity-building-legacy" aria-labelledby="nf-label-field-26-15">
        <label for="register_metadata[]-18" id="nf-label-field-26-18" class="form-check-label"/>How I Did It: Creating Opportunity. Building Legacy. </label>
    </div>
</div>
</div>
  </fieldset>
       
  <fieldset class="form-group has-feedback field-name{{ $errors->has('name') ? ' has-error' : '' }}"> 

    <div class="row">
      <legend class="col-form-label col-sm-2 pt-0"><strong>Your Information</strong></legend>
                
                <span class="glyphicon glyphicon-user form-control-feedback"></span>

                @if ($errors->has('register_metadata'))
                    <span class="help-block">
                        <strong>{{ $errors->first('register_metadata') }}</strong>
                    </span>
                @endif


      <div class="col-sm-16">




            <div class="form-group has-feedback field-name{{ $errors->has('name') ? ' has-error' : '' }}">
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>

                @if ($errors->has('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group has-feedback field-phone{{ $errors->has('phone') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[phone]" class="form-control" placeholder="Phone" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('phone'))
                    <span class="help-block">
                        <strong>{{ $errors->first('phone') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group has-feedback field-email{{ $errors->has('email') ? ' has-error' : '' }}">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group has-feedback field-password{{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group has-feedback field-password_confirmation{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>


            <div class="form-group has-feedback">
                <input type="text" name="register_metadata[job_title]" class="form-control" value="{{ isset($data['job_title']) ? $data['job_title'] : '' }}" placeholder="Job Title"/>

                @error('job_title')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>


            <div class="form-group has-feedback">
                <input type="text" name="register_metadata[company]" class="form-control" value="{{ isset($data['company']) ? $data['company'] : '' }}" placeholder="Company/Organization Name" required/>

                @error('company')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>




            <div class="form-group has-feedback field-address{{ $errors->has('address') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[address]" class="form-control" id="autocomplete" data-address="" placeholder="Address" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('address'))
                    <span class="help-block">
                        <strong>{{ $errors->first('address') }}</strong>
                    </span>
                @endif
            </div>


            <div class="row">
            <div class="col-sm-4">


            <div class="form-group has-feedback field-city{{ $errors->has('city') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[city]" class="form-control"  data-city="" placeholder="City">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('city'))
                    <span class="help-block">
                        <strong>{{ $errors->first('city') }}</strong>
                    </span>
                @endif
            </div>

        </div>
            <div class="col-sm-3" data-us-hide="" style="display: none;">


            <div class="form-group has-feedback field-province{{ $errors->has('province') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[province]" class="form-control" data-province="" placeholder="Province (optional)">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('province'))
                    <span class="help-block">
                        <strong>{{ $errors->first('province') }}</strong>
                    </span>
                @endif
            </div>

        </div>
            <div class="col-sm-3" data-us-show="">


            <div class="form-group has-feedback field-state{{ $errors->has('state') ? ' has-error' : '' }}">
                {{ $data['state'] = '' }}
{{ Form::select('register_metadata[state]',array(
    'AL'=>'ALABAMA',
    'AK'=>'ALASKA',
    'AS'=>'AMERICAN SAMOA',
    'AZ'=>'ARIZONA',
    'AR'=>'ARKANSAS',
    'CA'=>'CALIFORNIA',
    'CO'=>'COLORADO',
    'CT'=>'CONNECTICUT',
    'DE'=>'DELAWARE',
    'DC'=>'DISTRICT OF COLUMBIA',
    'FM'=>'FEDERATED STATES OF MICRONESIA',
    'FL'=>'FLORIDA',
    'GA'=>'GEORGIA',
    'GU'=>'GUAM GU',
    'HI'=>'HAWAII',
    'ID'=>'IDAHO',
    'IL'=>'ILLINOIS',
    'IN'=>'INDIANA',
    'IA'=>'IOWA',
    'KS'=>'KANSAS',
    'KY'=>'KENTUCKY',
    'LA'=>'LOUISIANA',
    'ME'=>'MAINE',
    'MH'=>'MARSHALL ISLANDS',
    'MD'=>'MARYLAND',
    'MA'=>'MASSACHUSETTS',
    'MI'=>'MICHIGAN',
    'MN'=>'MINNESOTA',
    'MS'=>'MISSISSIPPI',
    'MO'=>'MISSOURI',
    'MT'=>'MONTANA',
    'NE'=>'NEBRASKA',
    'NV'=>'NEVADA',
    'NH'=>'NEW HAMPSHIRE',
    'NJ'=>'NEW JERSEY',
    'NM'=>'NEW MEXICO',
    'NY'=>'NEW YORK',
    'NC'=>'NORTH CAROLINA',
    'ND'=>'NORTH DAKOTA',
    'MP'=>'NORTHERN MARIANA ISLANDS',
    'OH'=>'OHIO',
    'OK'=>'OKLAHOMA',
    'OR'=>'OREGON',
    'PW'=>'PALAU',
    'PA'=>'PENNSYLVANIA',
    'PR'=>'PUERTO RICO',
    'RI'=>'RHODE ISLAND',
    'SC'=>'SOUTH CAROLINA',
    'SD'=>'SOUTH DAKOTA',
    'TN'=>'TENNESSEE',
    'TX'=>'TEXAS',
    'UT'=>'UTAH',
    'VT'=>'VERMONT',
    'VI'=>'VIRGIN ISLANDS',
    'VA'=>'VIRGINIA',
    'WA'=>'WASHINGTON',
    'WV'=>'WEST VIRGINIA',
    'WI'=>'WISCONSIN',
    'WY'=>'WYOMING',
    'AE'=>'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
    'AA'=>'ARMED FORCES AMERICA (EXCEPT CANADA)',
    'AP'=>'ARMED FORCES PACIFIC'
) , 
$data['state'], 
array(
    'class'       => 'form-control',
    'id'          => 'state_select',
    'data-state'  => ''
)) }}
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('state'))
                    <span class="help-block">
                        <strong>{{ $errors->first('state') }}</strong>
                    </span>
                @endif
            </div>


        </div>
            <div class="col-sm-3">


            <div class="form-group has-feedback field-zip{{ $errors->has('zip') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[zip]" class="form-control" data-zip="" placeholder="Zip (optional)">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('zip'))
                    <span class="help-block">
                        <strong>{{ $errors->first('zip') }}</strong>
                    </span>
                @endif
            </div>

        </div>
        </div>
            <div class="row">
            <div class="col-sm-12">


            <div class="form-group has-feedback field-country{{ $errors->has('country') ? ' has-error' : '' }}">
                <input type="text" name="register_metadata[country]" class="form-control" data-country="" value="US" placeholder="Country">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('country'))
                    <span class="help-block">
                        <strong>{{ $errors->first('country') }}</strong>
                    </span>
                @endif
            </div>

        </div>
        </div>
            <div class="row">
            <div class="col-sm-12">
{{ '' . (join('', $data['industry'] = [])) }}

                        <div class="form-group has-feedback">
<select name="register_metadata[industry][]" id="industry_select" class="form-control" multiple="multiple">
<option value="agriculture"{{ (in_array('agriculture', $data['industry']) ? ' selected' : '') }}>Agriculture</option>
<option value="construction"{{ (in_array('construction', $data['industry']) ? ' selected' : '') }}>Construction</option>
<option value="educational_services"{{ (in_array('educational_services', $data['industry']) ? ' selected' : '') }}>Educational Services</option>
<option value="finance_and_insurance"{{ (in_array('finance_and_insurance', $data['industry']) ? ' selected' : '') }}>Finance and Insurance</option>
<option value="healthcare_services"{{ (in_array('healthcare_services', $data['industry']) ? ' selected' : '') }}>Healthcare Services</option>
<option value="marketing_and_communications"{{ (in_array('marketing_and_communications', $data['industry']) ? ' selected' : '') }}>Marketing and Communications</option>
<option value="manufacturer_or_producer"{{ (in_array('manufacturer_or_producer', $data['industry']) ? ' selected' : '') }}>Manufacturer or Producer</option>
<option value="professional_services"{{ (in_array('professional_services', $data['industry']) ? ' selected' : '') }}>Professional Services</option>
<option value="research_and_development"{{ (in_array('research_and_development', $data['industry']) ? ' selected' : '') }}>Research and Development</option>
<option value="restaurant_and_food_services"{{ (in_array('restaurant_and_food_services', $data['industry']) ? ' selected' : '') }}>Restaurant and Food Services</option>
<option value="retail_and_wholesale"{{ (in_array('retail_and_wholesale', $data['industry']) ? ' selected' : '') }}>Retail and Wholesale</option>
<option value="technology"{{ (in_array('technology', $data['industry']) ? ' selected' : '') }}>Technology</option>
<option value="travel_and_entertainment"{{ (in_array('travel_and_entertainment', $data['industry']) ? ' selected' : '') }}>Travel and Entertainment</option>
<option value="non-profit_organization"{{ (in_array('profit_organization', $data['industry']) ? ' selected' : '') }}>Non-Profit Organization</option>

</select>       
        
                            @error('industry')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                        </div>



            <div class="form-group col-sm-12 mx-0 px-0 has-feedback">
              <label for="make_public mx-0  px-0">
              Are you an MBDA Business Center? If so, please enter name of your Center here:
              </label>

              <input type="text" name="register_metadata[mbda_business_center_name]" class="form-control" value="{{ isset($data['mbda_business_center_name']) ? $data['mbda_business_center_name'] : '' }}"/>


                            @error('mbda_business_center_name')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
            </div>
</div>
</div>
</fieldset>

                    <span class="help-block" id="form_errors">
                        <ul></ul>
                    </span>

    <div class="row">
      <div class="col-form-label col-sm-2">&nbsp;</div>
      <div class="col-form-label col-sm-10">
        <p>By clicking Register you are agreeing to the terms in our <a href="https://events.vizzi.live/eventprivacypolicy/" target="_new">  Privacy Policy <i class="fa fa-external-link-alt"></i></a></p>
                    <button type="submit" class="btn btn-primary pull-right btn-flat">Register</button>
    </div>
</div>

</div>
</div>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<footer>
<div class="col-sm-12" style="background-color: #030309; color: #fff; font-weight: bold; text-align: center;">
    Copyright &copy; 2020 Vizzi & Virtual Creative Studios
</div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>

var autocomplete;

function initAutocomplete() {
  if (document.getElementById('autocomplete')) {
    autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'), {types: ['geocode']});
    autocomplete.setFields(['address_component']);
    autocomplete.addListener('place_changed', function() {
      fillInAddress(autocomplete);
    });
  }
}

$(function () {

$('.field-email inputx').blur(function () {

  $.ajax({
    type: "GET",
    url: '{{ url('/register-check-email') }}',
    data: {email:$(this).val()}, 
    success: function(data) {
        if (data.exists) {

        }
    }
    });
});


$("#register-form").submit(function(e) {
  e.preventDefault(); // avoid to execute the actual submit of the form.

  $('#form_errors ul').html('');


  var form = $(this);
  var url = form.attr('action');

  $.ajax({
    type: "POST",
    url: url,
    data: form.serialize(), // serializes the form's elements.
    success: function(data) {
        document.location.replace('{{ url('/register-event') }}');    
    },
    error: function (data) {
        var errors = data.responseJSON.errors;
        var error;
        if (errors.length == 0) {
            document.location.href = '{{ url('/register-event') }}';    
        }
        $.each(errors, function (field, errors) {
            error = errors[0];
            $('.field-' + field + ' input').addClass('is-invalid');
            $('#form_errors ul').append($('<li/>').text(errors))
        });
    }
    });

});

  $('.form-control').blur(function () {
    $('input').each(function () {
        $(this).removeClass('is-invalid');
        $('#form_errors ul').html('');
    });
  });
  $('.form-control').focus(function () {
    $('input').each(function () {
        $(this).removeClass('is-invalid');
        $('#form_errors ul').html('');
    });
  });

  $('#industry_select').chosen({
    placeholder_text_multiple: 'Select Industries'
  });
});
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-176810890-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-176810890-1');
</script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_KEY') }}&language=en&libraries=places&callback=initAutocomplete"></script>
    <script src="{{asset('assets/js/gapiv3.min.js')}}"></script>
    <script src="{{asset('assets/chosen/chosen.jquery.min.js')}}"></script>
</body>
</html>
