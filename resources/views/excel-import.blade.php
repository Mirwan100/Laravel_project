@extends('layouts.app')

@section('content')
  <h1>Import {{ ucfirst($form) }}</h1>
  <form action="{{ route('excel.import', $form) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit" class="btn btn-primary">Import</button>
  </form>
@endsection
