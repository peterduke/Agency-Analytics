@extends('layout')

@section('content')
    <h5>Crawled URL: {{ $url }}</h5>

    <p>Number of pages crawled: {{ $count }}</p>
    <p>Number of a unique images: {{ $img }}</p>
    <p>Number of unique internal links: {{ $intLinks }}</p>
    <p>Number of unique external links: {{ $extLinks }}</p>
    <p>Avg page load: {{ $pageLoad }} seconds</p>
    <p>Avg word count: {{ $wordCount }}</p>
    <p>Avg Title length: {{ $titleLength }} characters</p>

    <table class="table">
        <tr><th>page</th><th>status</th></tr>
        @foreach ($crawled as $page => $status )
        <tr><td>{{ $page }}</td><td>{{ $status }}</td></tr>
        @endforeach
    </table>
@endsection