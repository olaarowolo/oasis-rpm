@extends('layouts.supervisor')

@section('title', 'Supervisor Hub | TheOAsis Research Supervision System')

@section('content')
  @include('partials.dashboards.supervisor-welcome')
  @include('partials.dashboards.supervisor-metrics')
  @include('partials.dashboards.supervisor-content')
  @include('partials.dashboards.supervisor-roster')
  @include('partials.dashboards.supervisor-quick-actions')
  @include('partials.dashboards.supervisor-script')
@endsection