@extends('layouts.app')
@section('content')
ádaksdhk
    <form action="{{ url('/vnpay_payment') }} " method="post">
        @csrf
        <button type="submit">thanh toan</button>
    </form>
@endsection
