<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    
    protected $table = 'images';
    
    //relacion One to many/ de uno a muchos
    
    public function comments(){
        return $this->hasMany('App\Models\Comment')->orderBy('id', 'desc');
    }
    
    /*hasMany saca todos los registros de la base de datos en un
    array de objetos
    Ejemplo: si yo le paso una imagen con un id y llamo a un metodo
    que tenga una relacion de uno a muchos este me saca
    todos los registros que coincidan con el id solicitado
    es decir si yo envio una imagen con id 4 y llamo al metodo
    comments este me trae todos los comentarios que tenga dicha
    imagen con ese id igual pasa con el metodo likes*/

    public function likes(){
        return $this->hasMany('App\Models\Like');
    }
    
    //Relacion de muchos a uno/ Many to one
    
    /*belongsTo recibe 2 parametros el modelo(tabla) con el 
    que se relaciona, y el dato que se tiene en el propio
    objeto(variable relacionada). Este metodo se encarga de 
    buscar en el otro objeto los objetos cuyos id sean igual 
    al parametro que estoy pasando. Ejemplo si paso el 
    user_id 4 me trae todas las 
    imagenes que este usuario haya creado   */
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    
}
