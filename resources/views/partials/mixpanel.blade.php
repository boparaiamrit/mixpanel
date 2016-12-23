<script>
	 {!! file_get_contents(public_path('mixpanel/js/mixpanel.js')) !!}
   mixpanel.init("{{ config('services.mixpanel.token')}}");
</script>
