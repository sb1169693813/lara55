@if(count($feed_items)) 
<ol class="statuses">
	@foreach($feed_items as $item)
		@include('statuses._status', ['user' => $item->user, 'status' => $item])
	@endforeach
	{!! $feed_items->render() !!}
</ol>
@endif
