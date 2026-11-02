{{-- SHARED TEMPLATE FOR PUBLIC FOOTER "@include" --}}
{{-- THIS PUBLIC-FOOTER & HEADER ONLY USED IN GUEST PAGES ONLY --}}

<footer class="site-footer">
    <div class="container footer-content">
        <a class="brand" href="{{ url('/') }}"> {{-- makes the logo clickable to homepage || url('/') refer to web.php line 7 which is homepage --}}
            <img src="{{ asset('rescuebite.png') }}" alt="RescueBite" class="brand-mark">
            <span>Rescue<span>Bite</span></span>
        </a>
        <p>Making surplus food matter.</p>
    </div>
</footer>
