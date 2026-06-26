@extends('layouts.guest')

@section('title', $article->judul)
@section('meta_description', $article->kutipan)

@section('content')
<article id="article-detail" class="py-12 lg:py-16">
    <div class="max-w-3xl mx-auto px-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-mute mb-8" id="article-breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-primary transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <a href="{{ route('articles') }}" class="hover:text-primary transition-colors">Artikel</a>
            <i data-lucide="chevron-right" class="w-4 h-4"></i>
            <span class="text-ink font-medium truncate max-w-[200px]">{{ $article->judul }}</span>
        </nav>

        {{-- Article Header --}}
        <header class="mb-8">
            <h1 class="font-delight font-semibold text-3xl lg:text-4xl text-ink tracking-tight mb-4 leading-tight" style="letter-spacing: -1.5px">{{ $article->judul }}</h1>

            <div class="flex items-center gap-4 text-sm text-mute">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    {{ $article->diterbitkan_pada->format('d M Y') }}
                </div>
                @if($article->author)
                    <div class="flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        {{ $article->penulis->name }}
                    </div>
                @endif
            </div>
        </header>

        {{-- Thumbnail --}}
        @if($article->gambar_sampul)
            <div class="aspect-video rounded-xl overflow-hidden mb-8 shadow-card-sm">
                <img src="{{ asset($article->gambar_sampul) }}" alt="{{ $article->judul }}" class="w-full h-full object-cover">
            </div>
        @endif

        {{-- Article Content --}}
        <div class="prose prose-lg max-w-none mb-12
                     prose-headings:font-jakarta prose-headings:font-semibold prose-headings:text-ink prose-headings:tracking-tight
                     prose-p:text-body prose-p:leading-relaxed
                     prose-a:text-primary prose-a:no-underline hover:prose-a:underline
                     prose-strong:text-ink
                     prose-ul:text-body prose-ol:text-body
                     prose-img:rounded-xl prose-img:shadow-card-sm
                     " id="article-content">
            {!! $article->isi !!}
        </div>

        {{-- Share --}}
        <div class="border-t border-hairline pt-6 mb-12">
            <p class="text-sm font-medium text-ink mb-3">Bagikan artikel ini</p>
            <div class="flex gap-2">
                <a href="https://wa.me/?text={{ urlencode($article->judul . ' — ' . url()->current()) }}" target="_blank" class="btn-ghost" id="share-whatsapp">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    WhatsApp
                </a>
                <button onclick="navigator.clipboard.writeText(window.location.href); this.textContent = 'Tersalin!'; setTimeout(() => { this.innerHTML = '<i data-lucide=\'link\' class=\'w-4 h-4\'></i> Salin Link'; lucide.createIcons(); }, 2000)" class="btn-ghost" id="share-copy">
                    <i data-lucide="link" class="w-4 h-4"></i>
                    Salin Link
                </button>
            </div>
        </div>
    </div>

    {{-- Related Articles --}}
    @if($relatedArticles->count() > 0)
    <div class="max-w-[1280px] mx-auto px-6">
        <div class="border-t border-hairline pt-12">
            <h2 class="text-xl font-semibold text-ink tracking-tight mb-6" style="letter-spacing: -0.5px">Artikel terkait</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                <a href="{{ route('article.detail', $related->slug) }}" class="card-article group hover:shadow-card-md transition-all" id="related-{{ $related->id }}">
                    <div class="aspect-video bg-canvas-soft-2 flex items-center justify-center overflow-hidden">
                        @if($related->thumbnail)
                            <img src="{{ asset($related->thumbnail) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full eco-gradient opacity-20 flex items-center justify-center">
                                <i data-lucide="newspaper" class="w-8 h-8 text-primary opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <span class="text-xs text-mute">{{ $related->diterbitkan_pada->format('d M Y') }}</span>
                        <h3 class="text-sm font-semibold text-ink mt-1 group-hover:text-primary transition-colors line-clamp-2">{{ $related->judul }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</article>
@endsection
