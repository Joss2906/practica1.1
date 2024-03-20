@extends('base')

@section('title', 'Login')

@section('content')

<section class="vh-100 bg-image"  style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
	<div class="mask d-flex align-items-center h-100 gradient-custom-3">
		<div class="container h-100">
			<div class="row d-flex justify-content-center align-items-center h-100">
				<div class="col-12 col-md-9 col-lg-7 col-xl-6">
					<div class="card" style="border-radius: 15px;">
						<div class="card-body p-5">
							<h2 class="text-uppercase text-center mb-5">Iniciar Sesion</h2>
							<form method="POST" action="{{ route('postAuth') }}">
								@csrf

								@if( session('error'))
									<div class="alert alert-danger" role="alert">
										{{ session('error') }}
									</div>
								@endif
								
								<div class="form-outline mb-4">
									<label class="form-label" for="inp_email">Your Email</label>
									<input type="email" id="inp_email" name="email" class="form-control form-control-lg" />
									@error('email')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="form-outline mb-4">
									<label class="form-label" for="inp_pass1">Password</label>
									<input type="password" id="inp_pass1" name="password" class="form-control form-control-lg" />
									@error('password')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="g-recaptcha form-outline mb-4" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}" 
								id="captcha" required></div>
								@error('g-recaptcha-response')
									<span class="text-danger">{{ $message }}</span>
								@enderror
							
								<div class="d-flex justify-content-center mt-4">
								<button type="submit"
									class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Iniciar Sesion</button>
								</div>

								<p class="text-center text-muted mt-5 mb-0">¿No tienes una cuenta? <a href="{{ route('register') }}"
									class="fw-bold text-body"><u>Registrate aqui</u></a></p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@push('scripts')
<script>

</script>
@endpush
