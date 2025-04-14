@extends('layouts.template')
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Halo, Apa Kabar?</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <p>Selamat datang semua, ini adalah halaman utama dari aplikasi ini.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Profil Pengguna</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="text-center d-flex justify-content-center align-items-center mb-4">
                        @if (Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Foto Profil"
                                class="img-fluid rounded-circle mx-auto d-block"
                                style="height: 200px; width: 200px; object-fit: cover;" id="preview-image">
                        @else
                            <div id="default-image"
                                class="bg-light d-flex align-items-center justify-content-center rounded-circle mx-auto"
                                style="height: 200px; width: 200px;">
                                <i class="fas fa-user fa-5x text-secondary"></i>
                            </div>
                        @endif
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('/welcome/update-photo') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="profile_photo"
                                            name="profile_photo" accept="image/*" onchange="previewImage(event)" required>
                                        <label class="custom-file-label" for="profile_photo">Pilih file</label>
                                    </div>
                                    @error('profile_photo')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Simpan Foto</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Informasi Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Username</th>
                                        <td>{{ auth()->user()->username }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ auth()->user()->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level</th>
                                        <td>{{ auth()->user()->getRoleName() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        const preview = document.getElementById('preview-image');
        const defaultImage = document.getElementById('default-image');

        if (event.target.files && event.target.files[0]) {
            reader.onload = function () {
                preview.src = reader.result;
                preview.style.display = 'block';
                if (defaultImage) {
                    defaultImage.style.display = 'none';
                }
            }
            reader.readAsDataURL(event.target.files[0]);

            let fileName = event.target.files[0].name;
            $(event.target).next('.custom-file-label').addClass("selected").html(fileName);
        }
    }
</script>
@endpush
