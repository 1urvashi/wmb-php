@if($title == 'detail' && isset($type))
<section id="hero">
    <div class="caption tags">
         <div class="container">
             <ul>
                 <li class="{{strtolower($type)}}">{{strtoupper($type)}}</li>
             </ul>
        </div>
     </div>
</section>
@else
<section id="hero">
    <div class="caption">
         <div class="container">
            <p>
                <span>@if($specialTitle) {{$title}} @else {{$title}} @endif</span>
            </p>
        </div>
     </div>
</section>
@endif
