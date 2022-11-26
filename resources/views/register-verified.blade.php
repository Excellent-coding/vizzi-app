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
  <link href="{{asset('assets/dropzone/min/dropzone.min.css')}}" rel="stylesheet">
  <link href="{{asset('assets/dropzone/min/basic.min.css')}}" rel="stylesheet">
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

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
    }

    .user_avatar {
      border: #c0c0c0 1px solid;
      border-radius: 50%;
      min-width: 150px;
      min-height: 150px;
      max-width: 150px;
      max-height: 150px;
      margin-bottom: 10px;
    }

    .dropzone {
      border-color: #0033FF;
      border-style: dashed;
      border-width: 3px;
      position: inherit;
    }

    .text-md-center {

      margin:  0 auto;
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

                    @if ($edit)
<div class="col-sm-12">  <h3><div class="alert alert-success"><i class="fa fa-check-circle"></i> Settings Edited</div></h3>

                    @else
<div class="col-sm-12">  <h3><div class="alert alert-success"><i class="fa fa-check-circle"></i> Email Verified</div></h3>
                    @endif
</div>
</div>
<div class="row">
<div class="col-sm-1">&nbsp;</div>
<div class="register-box col-sm-10">
@if ($logged_in)
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="px-3 justify-content-center">

<h3><center>MED Week 2020 - Your Settings</center></h3>
<h3><center>Hello, {{ $user->name }}</center></h3>
<p><center>We are excited to celebrate 2020 National Minority Enterprise Development Week as a virtual experience!</center></p>
</div>
            <div class="card">
                <div class="card-header">{{ __('Profile Photo') }}</div>

                <div class="card-body">
<div class="row">
  <div class="form-group col-sm-8 text-md-center">
                    @if ($edit)
                      <img src="{{ env('AWS_URL') }}{{ $user->avatar }}" class="user_avatar"/>
                    @endif

{!! Form::open( [ 'url' => '/register-settings' , 'method' => 'post', 'files' => true, 'id' => 'upload_form', 
      'class' => "dropzone" ] ) !!}

                <input type="hidden" name="make_public" value="1" id="make_public_upload"/>
{!! Form::close() !!}

  </div>
  <div class="form-group col-sm-8 text-md-center">
            <div class="form-check col-sm-12 has-feedback">
                <input type="checkbox" name="make_public" class="form-check-input" value="1" id="make_public" {{ (isset($data['make_public']) && intval($data['make_public']) === 1 ? ' checked' : '') }}/>
              <label for="make_public">Would you like to share your profile photo with MED Week 2020 to incorporate your photo into the event experience?</label>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
          </div>
</div>
                </div>
                </div>
              </div>
            </div>
          </div>

<br/><br/>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Profile Details') }}</div>

                <div class="card-body">
{!! Form::open( [ 'url' => '/register-settings' , 'method' => 'post', 'id' => 'settings-form' ] ) !!}
                        @csrf
                        <input type="hidden" name="redirect" value="/email/verify/{{ $user->id }}"/>
                        <input type="hidden" name="make_public" value="1" id="make_public_settings"/>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ? old('email') : $user->email }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-3">
                                <input id="first_name" type="first_name" class="form-control @error('first_name') is-invalid @enderror" name="register_metadata[first_name]" value="{{ isset($data['first_name']) ? $data['first_name'] : '' }}" required autocomplete="first_name" placeholder="First Name" autofocus>
                                <div class="under-label fz14 px-3"><small>First</small></div>
                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <input id="last_name" type="last_name" class="form-control @error('last_name') is-invalid @enderror" name="register_metadata[last_name]" value="{{ isset($data['last_name']) ? $data['last_name'] : '' }}" required autocomplete="last_name" placeholder="Last Name" autofocus>
                                <div class="under-label fz14 px-3"><small>Last</small></div>

                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
            
                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Job Title</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[job_title]" class="form-control"value="{{ isset($data['job_title']) ? $data['job_title'] : '' }}" placeholder="Job Title"/>

                            @error('job_title')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Company/Organization Name</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[company]" class="form-control"value="{{ isset($data['company']) ? $data['company'] : '' }}" placeholder="Company/Organization Name"/>

                            @error('company')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Address</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[address]" class="form-control" id="autocomplete" value="{{ isset($data['address']) ? $data['address'] : '' }}" data-address="" placeholder="Address"/>

                            @error('address')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">City</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[city]" class="form-control" value="{{ isset($data['city']) ? $data['city'] : '' }}" data-city="" placeholder="City"/>

                            @error('city')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                {{ ((!isset($data['state'])) ? ($data['state'] = '') : '') }}
                {{ ((!isset($data['province'])) ? ($data['province'] = '') : '') }}
                        <div class="form-group row" data-us-hide=""{!! isset($data['province']) && strlen($data['province']) > 0 ? '' : ' style="display: none;"' !!}>
                          <label class="col-md-4 col-form-label text-md-right">Province (optional)</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[province]" class="form-control" value="{{ isset($data['province']) ? $data['province'] : '' }}" data-province="" placeholder="Province"/>

                            @error('zip')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row" data-us-show=""{!! isset($data['state']) && strlen($data['state']) > 0 ? '' : ' style="display: none;"' !!}>
                          <label class="col-md-4 col-form-label text-md-right">State (optional)</label>
                            <div class="col-md-6">
{{ Form::select('register_metadata[state]',array(
    ''=>'Select State...',
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
        
                            @error('state')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Zip (optional)</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[zip]" class="form-control" value="{{ isset($data['zip']) ? $data['zip'] : '' }}" data-zip="" placeholder="Zip"/>

                            @error('zip')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Country</label>

                            <div class="col-md-6">
                            <input type="text" name="register_metadata[country]" class="form-control" value="{{ isset($data['country']) ? $data['country'] : '' }}" data-country="" placeholder="Country"/>

                            @error('country')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
                          </div>
                        </div>

        </div>

                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">Industry</label>
                            <div class="col-md-6">
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
                        </div>



                        <div class="form-group row">
                          <label class="col-md-4 col-form-label text-md-right">&nbsp;</label>
                            <div class="col-md-6">
              <label for="center_name">
              Are you an MBDA Business Center?<br/>If so, please enter name of your Center here:
              </label>

              <input type="text" name="register_metadata[mbda_business_center_name]" class="form-control" value="{{ isset($data['mbda_business_center_name']) ? $data['mbda_business_center_name'] : '' }}" id="center_name" placeholder="Name of your center"/>


                            @error('mbda_business_center_name')
                              <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                              </span>
                            @enderror
            </div>
          </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save Settings') }}
                                </button>

                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">&nbsp;

                            </div>
                        </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{!! Form::close() !!}

                    @else
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="redirect" value="/email/verify/{{ $user->id }}"/>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ? old('email') : $user->email }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

                    @endif

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
<script src="{{asset('assets/dropzone/min/dropzone.min.js')}}"></script>
<script src="{{asset('assets/chosen/chosen.jquery.min.js')}}"></script>
<script type="text/javascript">

{{ ((!isset($data['state'])) ? ($data['state'] = '') : '') }}
{{ ((!isset($data['province'])) ? ($data['province'] = '') : '') }}

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


var myDropzone;

$(function () {
  Dropzone.options.uploadForm = {
dictDefaultMessage: 'Drop photo here to upload or click to browse'
};
  $('.dz-message').text('Drop photo here to upload or click to browse');

  $("#make_public").change(function () {
    $("#make_public_settings").val($(this).prop("checked") ? '1' : '0');
    $("#make_public_upload").val($(this).prop("checked") ? '1' : '0');
  });

  $('#industry_select').chosen();

  $('[data-state]').val('{{ $data['state'] }}');
  $('[data-state]').trigger('change');
  $('[data-province]').val('{{ isset($data['province']) ? $data['province'] : '' }}');

  $('[data-state]').change(function () {
    $('[data-province]').val('');
  });

  $('[data-province]').input(function () {
    if ($(this).val().length > 0) {
      $('[data-state]').val('');
      $('[data-state]').trigger('change');
    }
  });

  if ($('[data-province]').length > 0) {
  }

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

</body>
</html>