@extends('layouts.backend')

@push('styles')
@endpush

@section('content')
<style>
    .card {
        background-color: #141414;
        color: rgba(255, 255, 255, .85);
        border: transparent;
        border-radius: .5vw;
    }

    .card-header {
        font-size: 18px;
        background-color: #1d1d1d;
        color: rgba(255, 255, 255, .85);
        border-bottom: 1px solid #2d2d2d;
        border-top-left-radius: .5vw !important;
        border-top-right-radius: .5vw !important;
        align-items: center;
        min-height: 4vw;
        max-height: 4vw;
    }

    table#meeting-table tbody {
        background-color: #141414 !important;
        color: rgba(255, 255, 255, .85) !important;
    }

    table#meeting-table thead {
        background-color: #1d1d1d !important;
        color: rgba(255, 255, 255, .85) !important;
    }

    .container-fluid {
        min-height: 600px;
    }

    .c-icon {
        color: #3b89e8;
    }

    .c-icon:hover {
        color: #41b8f8;
    }

    .btn-primary {
        background-color: #3b89e8;
        font-size: 14px;
    }

    .btn-primary:hover {
        background-color: #41b8f8;
    }

    .btn.btn-success.meeting_detail {
        color: #56a22a;
        background-color: #162312;
        border: 1px solid #223d14;
        min-width: 5vw;
        max-width: 5vw;
    }

    .btn.btn-success.meeting_detail:hover {
        filter: brightness(120%);
    }

    .btn.btn-info.edit_record {
        color: #2c7adc;
        background-color: #111a2c;
        border: 1px solid #142c4f;
        min-width: 5vw;
        max-width: 5vw;
    }

    .btn.btn-info.edit_record:hover {
        filter: brightness(120%);
    }

    .btn.btn-danger.hapus_record {
        color: #da3735;
        background-color: #2a1215;
        border: 1px solid #4c161a;
        min-width: 5vw;
        max-width: 5vw;
    }

    .btn.btn-danger.hapus_record:hover {
        filter: brightness(120%);
    }

    .fas.fa-check-circle {
        color: #1f8329;
    }

    .badge {
        min-width: 4vw;
    }

    .badge.badge-primary {
        padding: 5px 10px;
        color: #59a52a;
        background-color: #162312;
        border: 1px solid #234015;
    }

    .badge.badge-primary.sakit {
        padding: 5px 10px;
        color: #26a5a3;
        background-color: #112123;
        border: 1px solid #133e3f;
    }

    .badge.badge-primary.izin {
        padding: 5px 10px;
        color: #2c78da;
        background-color: #111a2c;
        border: 1px solid #142c4f;
    }

    .badge.badge-primary.alfa {
        padding: 5px 10px;
        color: #d7862c;
        background-color: #2b1d11;
        border: 1px solid #4d3114;
    }

    .dataTables_length select {
        appearance: none;
        color: rgba(255, 255, 255, .85) !important;
        background-color: #141414 !important;
        border: 1px solid #363636 !important;
        border-radius: 5px !important;
        padding: 5px !important;
        width: 4vw !important;
    }

    .dataTables_length .custom-select::after {
        color: #141414 !important;
    }

    .dataTables_length option {
        color: rgba(255, 255, 255, .85) !important;
        background-color: #141414 !important;
    }

    .dataTables_length option:hover {
        background-color: #41b8f8 !important;
        color: #fff !important;
    }

    .dataTables_length select:focus {
        border-color: #41b8f8 !important;
        outline: 0 !important;
    }

    .dataTables_filter input {
        color: white !important;
        background-color: #141414 !important;
        border: 1px solid #363636 !important;
        border-radius: 5px !important;
        padding: 5px !important;
    }

    .dataTables_filter input::before {
        color: white !important;
    }

    .dataTables_filter input:focus {
        border-color: #41b8f8 !important;
        outline: 0 !important;
    }
</style>
<main class="c-main">
    @if (session('success'))
    <div class="success-session" data-flashdata="{{ session('success') }}"></div>
    @endif
    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h4>List Rapat</h4>
                    </div>
                    @can('rapat-detail-edit')
                    <div class="d-flex">
                        @if ($meeting->status === 'open')
                        <a href="{{ route('admin.meeting.edit.status', $meeting->id) }}" class="btn btn-danger mx-2"
                            onclick="event.preventDefault();document.getElementById('form-status').submit();">
                            <i class="fas fa-lock"></i>
                            Tutup Rapat
                        </a>
                        <form id="form-status" action="{{ route('admin.meeting.edit.status', $meeting->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="closed">
                        </form>
                        @else
                        <a href="{{ route('admin.meeting.edit.status', $meeting->id) }}" class="btn btn-success mx-2"
                            onclick="event.preventDefault();document.getElementById('form-status').submit();">
                            <i class="fas fa-lock-open"></i>
                            Buka Rapat
                        </a>
                        <form id="form-status" action="{{ route('admin.meeting.edit.status', $meeting->id) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="open">
                        </form>
                        @endif

                        <button data-toggle="modal" data-target="#modal-peserta" class="btn btn-primary mx-2">
                            <i class="fas fa-plus"></i>
                            Tambahkan Peserta Rapat
                        </button>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h3>{{ $meeting->name }}</h3>
                        <div>

                            <a href="{{ route('admin.meeting.byid.export', $meeting->id) }}"
                                class="btn btn-primary mx-5 my-2">Export Data Presensi</a> <br>
                            @if ($meeting->status === 'closed')
                            @can('rapat-detail-edit')
                            <a href="{{ route('admin.meeting.user.notlist') }}" class="btn btn-danger mx-5"
                                onclick="event.preventDefault();document.getElementById('form-notlist').submit();">Generate
                                User Menjadi Alfa</a>
                            <form action="{{ route('admin.meeting.user.notlist') }}" method="post" id="form-notlist">
                                @csrf
                                <input type="hidden" name="id" value="{{ $meeting->id }}">
                            </form>
                            @endcan
                            @endif

                        </div>
                    </div>
                    <div class="d-flex mx-auto mb-3">
                        <img class="rounded mx-auto p-2 bg-white"
                            src="{{ asset('storage/pertemuan/qrcode/' . $meeting->qrcode) }}" width="20%">

                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-6 col-6">
                            <p>Detail : {{ $meeting->detail }}</p>
                            <p>Kategori : {{ $meeting->meeting_category->name }}</p>
                            <p>Status :
                                @if ($meeting->status === 'open')
                                <span class="badge badge-pill badge-success py-1 px-2">Open</span>
                                @else
                                <span class="badge badge-pill badge-danger py-1 px-2">Closed</span>
                                @endif
                            </p>
                            <p>Tanggal : {{ $meeting->begin_date }}</p>
                        </div>
                        <div class="col-xl-6 col-6">
                            <p>Jam Mulai : {{ $meeting->start_meet_at }}</p>
                            <p>Jam Selesai : {{ $meeting->end_meet_at }}</p>
                            <p>Jam Absensi Mulai : {{ $meeting->start_presence }}</p>
                            <p>Jam Absensi Selesai : {{ $meeting->end_presence }}</p>
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" id="meeting-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NRP</th>
                                <th>Nama</th>
                                <th>Jam Absensi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</main>
@endsection

@push('scripts')
@can('rapat-detail-edit')
<div class="modal fade" id="modal-peserta" tabindex="-1" aria-labelledby="modal-pesertaTitle" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-pesertaTitle">Modal Daftar Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped w-100" id="peserta-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NRP</th>
                            <th>Nama</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="save-peserta" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Absensi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.meeting.user.edit') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="pivot_id" id="pivot_id" value="">
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <input type="hidden" name="meeting_id" id="meeting_id" value="">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" readonly="true" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">NRP</label>
                        <input type="text" class="form-control @error('nrp') is-invalid @enderror" id="nrp" name="nrp"
                            readonly="true" required>
                        @error('nrp')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Status</label>
                        <select class="form-control @error('status') is-invalid @enderror" name="status" id="" required>
                            <option value="">- Pilih Salah Satu -</option>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="alfa">Alfa</option>
                            <option value="sakit">Sakit</option>
                        </select>
                        @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endcan
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.5/dist/sweetalert2.all.min.js"
    integrity="sha256-NHQE05RR3vZ0BO0PeDxbN2N6dknQ7Z4Ch4Vfijn9Y+0=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
            let flashdatasukses = $('.success-session').data('flashdata');
            if (flashdatasukses) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: flashdatasukses,
                    type: 'success'
                })
            }
        })
        @can('rapat-detail-edit')
            let pesertaTable = $('#peserta-table').DataTable({
                pageLength: 25,
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.meeting.user.get', $meeting->id) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nrp',
                        name: 'nrp'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },

                ],
                select: {
                    style: 'multi'
                }
            });
        @endcan
        let table = $('#meeting-table').DataTable({
            pageLength: 25,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.meeting.byid', $meeting->id) }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'nrp',
                    name: 'nrp'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        function reload_table(callback, resetPage = false) {
            table.ajax.reload(callback, resetPage); //reload datatable ajax 
        }

        $('#meeting-table').on('click', '.hapus_record', function(e) {
            let pivot = $(this).data('pivot')
            let name = $(this).data('name')
            let meeting_name = "{{ $meeting->name }}"
            e.preventDefault()
            Swal.fire({
                title: 'Apakah Yakin?',
                text: `Apakah Anda yakin ingin menghapus ${name} pada rapat dengan nama : ${meeting_name}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: "{{ url('admin/meeting/user/delete') }}",
                        type: 'POST',
                        data: {
                            _token: CSRF_TOKEN,
                            _method: "delete",
                            pivot
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                `${name} berhasil dihapus pada rapat dengan nama : ${meeting_name}.`,
                                'success'
                            )
                            reload_table(null, true)
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: 'error',
                                type: 'error',
                                title: 'Error saat delete data',
                                showConfirmButton: true
                            })
                        }
                    })
                }
            })
        })
        $('#meeting-table').on('click', '.edit_record', function(e) {
            e.preventDefault()
            let meeting_id = "{{ $meeting->id }}"
            let id = $(this).data('id')
            let name = $(this).data('name')
            let nrp = $(this).data('nrp')
            let pivot_id = $(this).data('pivot')
            $("#user_id").val(id)
            $("#meeting_id").val(meeting_id)
            $("#nrp").val(nrp)
            $("#name").val(name)
            $("#pivot_id").val(pivot_id)
        })
        @can('rapat-detail-edit')
            $('#save-peserta').click(function() {
                let dataMahasiswa = pesertaTable.rows('.selected').data()
                let peserta = []
                dataMahasiswa.map(mahasiswa => {
                    peserta.push(mahasiswa)
                })
                let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('admin.meeting.user.create', $meeting->id) }}",
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN,
                        peserta
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        Swal.fire(
                            'Updated!',
                            `Peserta telah diupdate`,
                            'success'
                        )
                        pesertaTable.ajax.reload(null, false)
                        reload_table(null, true)
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            icon: 'error',
                            type: 'error',
                            title: 'Error saat update data',
                            showConfirmButton: true
                        })
                    }
                })
            });
        @endcan
</script>
@endpush