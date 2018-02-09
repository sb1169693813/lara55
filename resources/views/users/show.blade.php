@extends('layouts.default')
@section('title', $user->name)
@section('content')
<div class="row">
  <div class="col-md-offset-2 col-md-8">
    <div class="col-md-12">
      <div class="col-md-offset-2 col-md-8">
        <section class="user_info">
          @include('shared._user_info', [$user])
        <section>
      </div>
    </div>
    <div class="col-md-12">
    	@if(count($userStatuses) > 0)
    		<ul>
    			@foreach($userStatuses as $status) 
					@include('statuses._status')
    			@endforeach
    		</ul>
    		{!! $userStatuses->render() !!}
    	@endif
    </div>
  </div>
</div>
@stop
