@extends('layouts.guest')

@section('title', 'Artikel Edukasi')
@section('meta_description', 'Baca artikel edukasi tentang pengelolaan sampah, bahaya sampah liar, pemilahan sampah, dan kebersihan lingkungan.')

@section('content')
<section id="articles-page" class="py-12 lg:py-16">
    <div class="max-w-[1280px] mx-auto px-6">
        {{-- Header --}}
        <div class="mb-10">
            <h1 class="font-delight font-semibold text-3xl lg:text-4xl text-ink tracking-tight mb-3" style="letter-spacing: -1.5px">Artikel edukasi lingkungan</h1>
            <p class="text-lg text-body max-w-lg">Pelajari lebih lanjut tentang pengelolaan sampah dan cara menjaga kebersihan lingkungan.</p>
        </div>

        {{-- Articles Grid --}}
        @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articles as $article)
                <a href="{{ route('article.detail', $article->slug) }}" class="card-article group hover:shadow-card-md transition-all duration-300 animate-fade-in-up" id="article-{{ $article->id }}">
                    <div class="aspect-video bg-canvas-soft-2 flex items-center justify-center overflow-hidden">
                        @if($article->gambar_sampul)
                            <img src="{{ asset($article->gambar_sampul) }}" alt="{{ $article->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full eco-gradient opacity-20 flex items-center justify-center">
                                <i data-lucide="newspaper" class="w-10 h-10 text-primary opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-2.5">
                            <span class="text-xs text-mute">{{ $article->diterbitkan_pada->format('d M Y') }}</span>
                            @if($article->penulis)
                                <span class="text-xs text-mute">•</span>
                                <span class="text-xs text-mute">{{ $article->penulis->name }}</span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-ink tracking-tight mb-2 group-hover:text-primary transition-colors line-clamp-2" style="letter-spacing: -0.5px">{{ $article->judul }}</h3>
                        <p class="text-sm text-body line-clamp-3">{{ $article->kutipan }}</p>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-canvas-soft-2 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-text" class="w-8 h-8 text-mute"></i>
                </div>
                <h3 class="text-lg font-semibold text-ink mb-2">Belum ada artikel</h3>
                <p class="text-body">Artikel edukasi akan segera hadir. Pantau terus!</p>
            </div>
        @endif
    </div>
</section>
@endsection
