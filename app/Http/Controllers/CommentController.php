<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
class CommentController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function save(Request $request){
        //validacion
        $validate = $this->validate($request, [
            'image_id' => 'integer|required',
            'content' => 'string|required'
        ]);
        //recoger datos
        $user = \Auth::user();
        $image_id = $request->input('image_id');
        $content = $request->input('content');
        

        //Asingo los valores a mi nuevo objeto a guardar
        $comment = new Comment();
        $comment->user_id= $user->id;
        $comment->image_id = $image_id;
        $comment->content = $content;
        //Guardar en BD
        
        $comment->save();
        
        //Redireccion
        
        return redirect()->route('image.detail', ['id'=> $image_id])->with([
            'message'=> 'Has publicado tu comentario correctamente'
        ]);
        
        
    }
    
    public function delete($id){
        
        //Conseguir datos del usuario identificado
        $user = \Auth::user();
        
        //Conseguir objeto del comentario
        //find saca un unico objeto
        $comment = Comment::find($id);
        
        //Comprobar si soy el dueño del comentario el $user para saber si estoy identificado
        if($user && ($comment->user_id == $user->id || $comment->image->user_id == $user->id)){
            $comment->delete();
        
            return redirect()->route('image.detail', ['id'=> $comment->image->id])->with([
            'message'=> 'Comentario eliminado correctamente'
            ]);
            
        }else{
            return redirect()->route('image.detail', ['id'=> $comment->image->id])->with([
            'message'=> 'El comentario no se ha eliminado'
            ]);
        }
        
    }

}
