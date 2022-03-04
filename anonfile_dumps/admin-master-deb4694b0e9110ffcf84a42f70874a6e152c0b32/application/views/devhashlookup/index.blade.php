@include('TEMPLATE.errors', ['errors' => $errors])
@include('devhashlookup/template/filter', [
    'lastactivity_options' => $lastactivity_options,
    'pagination' => $pagination,
    'post' => $post
])

@if(isset($post['apply_filter']))
    @include('devhashlookup/template/table', [
        'devhashes' => $devhashes,
        'pagination' => $pagination
    ])

    {!! $pagination !!}
@endif