@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @include('includes.message')
            <div class="card pub_image pub_image_detail">
                <div class="card-header">
                    @if($image->user->image)
                    <div class="container_avatar">
                        <img src="{{route('user.avatar', ['filename'=>$image->user->image])}}"/>
                    </div>
                    @endif
                    <div class="data-user">
                        {{$image->user->name.' '. $image->user->surname}}
                        <span class="nickname">
                            {{ ' | @'.$image->user->nick}}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="image_container image-detail">
                        <img src="{{ route('image.file', ['filename' => $image->image_path])}}"/>
                    </div>

                    <div class="description">
                        <span class="nickname">{{ '@'.$image->user->nick }}</span>
                        <span class="nickname date">{{' | '.\FormatTime::LongTimeFilter($image->created_at)}}</span>

                        <p>{{$image->description}}</p>
                    </div>
                    <div class="likes">

                        <!-- Comprobar si el usuario le dio like a la imagen -->
                        <?php $user_like = false; ?>
                        @foreach($image->likes as $like)
                            @if($like->user->id == Auth::user()->id)
                                <?php $user_like = true; ?>
                            @endif
                        @endforeach

                        @if($user_like)
                            <img src="{{asset('img/heart-red.png')}}" data-id="{{$image->id}}" class="btn-dislike"/>
                        @else
                            <img src="{{asset('img/heart-blue.png')}}" data-id="{{$image->id}}" class="btn-like"/>
                        @endif

                        <samp class="number_likes">{{count($image->likes)}}</samp>

                    </div>
                    @if(Auth::user() && Auth::user()->id == $image->user->id)
                        <div class="actions">

                            <a href="{{route('image.edit', ['id'=>$image->id])}}" class="btn btn-sm btn-primary">Actualizar</a>
                            <!--<a href="{{route('image.delete', ['id'=>$image->id])}}" class="btn btn-sm btn-danger">Borrar</a>-->
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Eliminar
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">¿Estas seguro?</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Si eliminas esta imagen nunca podras recuperarla, ¿Estas seguro que la deseas eliminar?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cancelar</button>
                                            <a href="{{route('image.delete', ['id'=>$image->id])}}" class="btn btn-danger">Borrar definitivamente</a>
                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                    <div class="clearfix"></div>
                    <div class="comments">

                        <h2>Comentarios {{count($image->comments)}}</h2>     
                        <hr>
                        <form method="POST" action="{{route('comment.save')}}">
                            @csrf
                            <input type="hidden" name="image_id" value="{{$image->id}}"/>
                            <p>
                                <textarea class="form-control {{$errors->has('content') ? 'is-invalid' : ''}}" name="content"></textarea>
                                @if($errors->has('content'))
                                <span class="invalid-feedback" role='alert'>
                                    <strong>{{$errors->first('content')}}</strong>
                                </span>
                                @endif
                            </p>
                            <button type="submit" class="btn btn-success">Enviar </button>
                        </form>
                        <hr>
                        @foreach($image->comments as $comment)
                        <div class="comment">
                            <span class="nickname">{{ '@'.$comment->user->nick }}</span>
                            <span class="nickname date">{{' | '.\FormatTime::LongTimeFilter($comment->created_at)}}</span>
                            <!-- Esto es un comentario
                                auth::check() comprueba si estoy identificado -->
                            <p>{{$comment->content}}<br>
                                @if(Auth::check() && ($comment->user_id == Auth::user()->id || $comment->image->user_id == Auth::user()->id))
                                <a href="{{route('comment.delete', ['id' => $comment->id])}}" class="btn btn-sm btn-danger">Eliminar</a>
                                @endif
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>

    </div>
</div>
@endsection
