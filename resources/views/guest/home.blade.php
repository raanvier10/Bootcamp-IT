@extends('layouts.guest')

@section('title', 'Beranda')
@section('meta_description', 'TrashReport — Laporkan titik sampah liar, pantau penanganannya, dan jaga kebersihan lingkungan bersama komunitas.')

@section('content')
<div id="react-landing-root" data-props="{{ json_encode([
    'stats' => $stats ?? ['total_reports'=>0,'reports_completed'=>0,'reports_processing'=>0],
    'mapReports' => $mapReports ?? [],
    'latestArticles' => $latestArticles ?? [],
    'authRoute' => auth()->check() ? route('user.report.create') : route('register'),
    'mapRoute' => route('map'),
    'articlesRoute' => route('articles'),
    'loginRoute' => route('login'),
    'registerRoute' => route('register')
]) }}"></div>
@endsection
