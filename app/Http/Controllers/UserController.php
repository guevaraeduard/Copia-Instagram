<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function index($search = null){
        
        if(!empty($search)){
            $users = User::where('nick', 'LIKE', '%'.$search.'%')
                    ->orWhere('name', 'LIKE', '%'.$search.'%')
                    ->orWhere('surname', 'LIKE', '%'.$search.'%')
                    ->orderBy('id', 'desc')->paginate(5);
        }else{
            $users = User::orderBy('id', 'desc')->paginate(5);
        }
        
        return view('user.index', [
            'users'=>$users
        ]);
    }
    
    public function config(){
        return view('user.config');
    }

    public function pass_update(){
        return view('user.cambiar_pass');
    }

    public function save_pass_update(Request $request){
        $user = \Auth::user();
        $validate = $this->validate($request, [
            'password_ac' => 'required|string|',
            'password' => 'required|confirmed|string|min:8'
        ]);

        $password_ac = $request->input('password_ac');
        $password_nueva = $request->input('password');

        $cambio = password_verify($password_ac, $user->password);
        
        if($cambio && ($password_ac != $password_nueva)){
            
            $user->password = Hash::make($password_nueva);
            
        }else{
            
            return redirect()->route('user.pass')->with(['message'=> 'No se pudo actualizar la contraseña.
            Verifique los datos']);
        
        }

        $user->update();
        
        return redirect()->route('user.pass')->with(['message'=> 'Contraseña actualizada correctamente']);
        
        
    }
    
    public function update(Request $request){

        //conseguir usuairo identificado
        $user = \Auth::user();
        $id = $user->id;
        /*En la validacion del email y el nick se pone el id
        porque se realiza una busqueda en la BD para verificar que 
        estos no se repitan aceptuando el mismo dato del usuario
        identificado*/
        $validate = $this->validate($request, [
            'name' => 'required|alpha|max:255',
            'surname' => 'required|alpha|max:255',
            'nick' => 'required|string|max:255|unique:users,nick, '.$id,
            'email' => 'required|string|max:255|unique:users,email, '.$id
        ]);
        //recoger datos del formulario
        $name = $request->input('name');
        $surname = $request->input('surname');
        $nick = $request->input('nick');
        $email = $request->input('email');
        
        //Asignar nuevos valores al objeto del usurio
        $user->name = $name;
        $user->surname = $surname;
        $user->nick = $nick;
        $user->email = $email;
        //Subir la imagen
        $image_path = $request->file('image_path');
        if($image_path){
            //poner nombre unico
            $image_path_name = time().$image_path->getClientOriginalName();
            //guardarla en la carpeta storage/app/users
            Storage::disk('users')->put($image_path_name, File::get($image_path));
            //seteo el nombre de la imagen en el objeto
            $user->image= $image_path_name;
        }
        //Ejecutar consulta y cambios en la base de datos
        $user->update();
        
        return redirect()->route('config')->with(['message'=> 'Usuario actualizado correctamente']);
        
        
    }
    
    public function getImage($filename){
        $file = Storage::disk('users')->get($filename);
        return new Response($file, 200);
    }
    
    public function profile($id){
        
        $user = User::find($id);
        
        return view('user.profile', [
            'user' =>$user
        ]);
        
    }
    
    
}
