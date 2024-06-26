@extends('base')

@section('title', $title)



@section('canonical_tag')
<link rel="canonical" href="{{ config('app.url') }}/{{ $langCode->name }}/profile/{{ $user->username }}" />
@endsection

@section('meta_description', str(strip_tags($user->about_html))->limit(250));

@section('content')
    <div class="row profile">
        <div class="col-md-2 mb-3"><img src="{{ $user->avatar(128) }}" class="img-thumbnail rounded-circle"></div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-3  text-start"> <h3> {{ $user->name }} </h3> </div>
                <div class="col-md-9  text-end ">
                    @if ($user->is(auth()->user()))
                        <a 
                            class="btn btn-outline-primary btn-sm"
                            href="{{ route('edit_profile', ['user' => $user]) }}">{{ __('Edit profile') }}</a>
                    @endif
                    
                    @auth
                        @if ($user->isAdministrator()) 
                            <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.users.edit', ['user' => $user]) }}">{{ __('Edit profile as Admin') }}</a> 
                        @endif
                    @endauth
                </div>            
            
                <div  class="col-12 mt-2 about markdown-body">
                    @if ($user->about_html)
                        {!! $user->about_html !!}
                    @else
                        {{ $user->about ?? "" }}
                    @endif
               </div>
                     
                    <p class="pt-2">
                    @if ($user->instagram)
                        <a href="https://www.instagram.com/{{ $user->instagram }}"><img src="{{ asset('/static/icons/instagram.png') }}" alt="Instagram" width="32" height="32"></a>
                    @endif
                    
                    @if ($user->twitter)
                        <a href="https://www.twitter.com/{{ $user->twitter }}"><img src="{{ asset('/static/icons/twitter.png') }}" alt="Twitter" width="32" height="32"></a>
                    @endif
                    
                    @if ($user->facebook)
                        <a href="https://www.facebook.com/{{ $user->facebook }}"><img src="{{ asset('/static/icons/facebook.png') }}" alt="Facebook" width="32" height="32"></a>
                    @endif
                    
                    @if ($user->github)
                        <a href="https://www.github.com/{{ $user->github }}"><img src="{{ asset('/static/icons/github.png') }}" alt="Github" width="32" height="32"></a>
                    @endif
                    
                    @if ($user->youtube)
                        <a href="https://www.youtube.com/{{ $user->youtube }}"><img src="{{ asset('/static/icons/youtube.png') }}" alt="Youtube" width="32" height="32"></a>
                    @endif
                    </p>
                
                <div class="col-md-6 mt-3">
                    <h5 class="border-bottom pb-2"> {{ __("Latest posts") }}</h5>
                    
                    @php
                    $posts = $user->posts()->whereLangCode($langCode->name)->whereActive(true)->orderByDesc('created_at')->take(10)->get();
                    @endphp

                    @if ($posts)
                        @foreach ($posts as $post)
                            <a href="{{ route('post', ['langCode' => $post->lang_code, 'post' => $post]) }}">{{$post->title }}</a> <br>
                        @endforeach
                    @endif        
                </div>
                
                <div class="col-md-6 mt-3 mb-3">
                    <h5 class="border-bottom pb-2"> {{ __("Latest comments") }}</h5>
                    
                    @php
                    $comments = $user->comments()->whereHas('post', function ($builder) use ($langCode) {
                        return $builder->where('lang_code', $langCode->name);
                    })->whereActive(true)->orderByDesc('created_at')->take(10)->get();
                    @endphp

                    @if ($comments)
                        @foreach ($comments as $comment)
                            <a href="{{ route('post', ['langCode' => $comment->post->lang_code, 'post' => $comment->post]) }}#{{ $comment->id }}">
                                {{ str($comment->comment)->limit(50) }}</a> <br>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>  
    </div>

@endsection