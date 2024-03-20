@extends('base')

@section('title', 'Register')

@section('content')

<section class="vh-100 bg-image"  style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
	<div class="mask d-flex align-items-center h-100 gradient-custom-3">
		<div class="container h-100">
			<div class="row d-flex justify-content-center align-items-center h-100">
				<div class="col-12 col-md-9 col-lg-7 col-xl-6">
					<div class="card" style="border-radius: 15px;">
						<div class="card-body p-5">
							<h2 class="text-uppercase text-center mb-5">Crea una cuenta</h2>
							<form method="POST" action="{{ route('postRegister') }}">
								@csrf
								
								@if( session('success'))
									<div class="alert alert-success" role="alert">
										{{ session('success') }}
									</div>
								@endif
								<div class="form-outline mb-4">
									<label class="form-label" for="inp_name">Your Name</label>
									<input type="text" id="inp_name" name="name" class="form-control form-control-lg" required />
									@error('name')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="form-outline mb-4">
									<label class="form-label" for="inp_email">Your Email</label>
									<input type="email" id="inp_email" name="email" class="form-control form-control-lg" required/>
									@error('email')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="form-outline mb-4">
									<label class="form-label" for="inp_pass1">Password</label>
									<input type="password" id="inp_pass1" name="password" class="form-control form-control-lg" required />
									@error('password')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="g-recaptcha form-outline mb-4" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}" 
								id="captcha" required></div>
								@error('g-recaptcha-response')
									<span class="text-danger">{{ $message }}</span>
								@enderror

								<div class="d-flex justify-content-center">
								<button type="submit"
									class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Register</button>
								</div>

								<p class="text-center text-muted mt-5 mb-0">¿Ya tienes una cuenta? <a href="{{ route('auth') }}"
									class="fw-bold text-body"><u>Inicia sesión aqui</u></a></p>
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
