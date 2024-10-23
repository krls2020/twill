<nav class="mb-10 border-b border-b-primary md:sticky md:z-10 md:top-0 md:py-5 md:bg-white">
    <ul class="px-5 md:flex md:flex-row md:flex-nowrap md:justify-center md:px-0">
        @foreach($links as $link)
            <li class="py-5 border-t border-t-secondary first:border-t-0 md:py-0 md:px-5 md:border-t-0 md:border-l md:border-l-secondary md:first:border-l-0">
                <a href="{{route('frontend.page', [$link->getRelated('page')->first()->slug])}}">
                    {{$link->title}}
                </a>
            </li>
        @endforeach

        <li class="py-5 border-t border-t-secondary first:border-t-0 md:py-0 md:px-5 md:border-t-0 md:border-l md:border-l-secondary md:first:border-l-0">
            <a href="/admin" class="px-4 py-2 bg-slate-900 text-white rounded hover:bg-slate-700 transition-colors duration-200">
                Administration
            </a>
        </li>
    </ul>
</nav>