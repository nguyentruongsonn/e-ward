@extends('admin.layout')

@section('title', 'Thêm Lĩnh vực')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-plus"></i> Thêm Lĩnh vực mới</h3>
                    </header>
                    <div class="panel-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.tthc.linhvuc.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="tenLinhVuc">Tên lĩnh vực <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tenLinhVuc" name="tenLinhVuc" 
                                       value="{{ old('tenLinhVuc') }}" required maxlength="500">
                                <small class="form-text text-muted">Tối đa 500 ký tự</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Lưu
                                </button>
                                <a href="{{ route('admin.tthc.linhvuc.index') }}" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

