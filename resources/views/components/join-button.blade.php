  @guest
      <a href="{{ route('shops.create') }}" class="text-white d-lg-inline-block btn-login btn btn-success">
          {{ translate('Join The Club') }}
          <i class="la la-angle-right "></i>
      </a>
  @else
      <a href="{{ route('dashboard') }}" class="text-white d-lg-inline-block btn-login btn btn-success">
          {{ translate('Company Profile') }}
          <i class="la la-angle-right "></i>
      </a>
  @endguest
