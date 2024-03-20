@extends('base')

@section('title', 'Verificar Codigo')

@section('content')

<section class="vh-100 bg-image"  style="background-image: url('https://mdbcdn.b-cdn.net/img/Photos/new-templates/search-box/img4.webp');">
	<div class="mask d-flex align-items-center h-100 gradient-custom-3">
		<div class="container h-100">
			<div class="row d-flex justify-content-center align-items-center h-100">
				<div class="col-12 col-md-9 col-lg-7 col-xl-6">
					<div class="card" style="border-radius: 15px;">
						<div class="card-body p-5">
							<h2 class="text-uppercase text-center mb-5">Verificar Codigo (01)</h2>
							<form method="POST" action=" {{ route('postVerify') }}">
								@csrf
								<!-- <input type="hidden" name="_token" value="{{ csrf_token() }}" /> -->
								@if( session('error'))
									<div class="alert alert-danger" role="alert">
										{{ session('error') }}
									</div>
								@endif

								<div class="form-outline mb-4">
									<label class="form-label" for="inp_code">Your Code</label>
									<input type="hidden" name="id" value="{{ $id }}">
									<input type="text" id="inp_code" name="code" class="form-control form-control-lg" required />
									@error('code')
										<span class="text-danger">{{ $message }}</span>
									@enderror
								</div>

								<div class="d-flex justify-content-center">
								<button type="submit"
									class="btn btn-success btn-block btn-lg gradient-custom-4 text-body">Ingresar</button>
								</div>
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
