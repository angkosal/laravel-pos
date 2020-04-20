@if (Session::has('success'))
<div class="alert alert-primary">
    {{Session::get('success')}}
</div>
@endif