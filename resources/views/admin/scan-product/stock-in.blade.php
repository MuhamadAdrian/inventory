@extends('layouts.scan')

@section('content')
  @include('admin.scan-product.template.scan', ['stock_type' => 'in'])
@endsection