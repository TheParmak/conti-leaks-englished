@if($errors)
    @foreach($errors as $item)
        <div class="errorAlert alert alert-danger small">{!! $item !!}</div>
    @endforeach
@endif

<script>
    $('.errorAlert').trustAsHtml(
        this.html()
    );
</script>