<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ url('/') }}</loc>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ route('tools.index') }}</loc>
    <changefreq>daily</changefreq>
    <priority>0.9</priority>
  </url>
  <url>
    <loc>{{ route('quizzes.index') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <url>
    <loc>{{ route('blog.index') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>{{ route('privacy') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.3</priority>
  </url>
@foreach($tools as $tool)
  <url>
    <loc>{{ route('tools.show', $tool) }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
@endforeach
@foreach($quizzes as $quiz)
  <url>
    <loc>{{ route('quizzes.show', $quiz) }}</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
@endforeach
@foreach($posts as $post)
  <url>
    <loc>{{ route('blog.show', $post) }}</loc>
    <lastmod>{{ $post->updated_at->toDateString() }}</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
@endforeach
</urlset>
