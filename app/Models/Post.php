<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['published_at']; //agregamos la propiedad o atributo $dates para que acepte propiedades de carbon porque ya es instancia de carbon

    public function scopeAllowed($query)
    {
        //if( auth()->user()->hasRole('Admin') )
        //view es un método de las politicas de acceso PostPolicy.php
        //$this porque se tiene que pasar la instancia de post aunque sea vacia
        /// if (auth()->user()->can('view', $this)) {
        // $posts = Post::all();
        ///return $query;
        // return $query->Post::all(); no funcina
        //parece que por defecto le pone all
        ///}

        //   $posts = auth()->user()->posts;
        //return $query->where('user_id', auth()->id());
        return $query->where('user_id', Auth::id());
    }

    /* public function getRouteKeyTitle()
     {
         return 'slug';

     } */


    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    //de uno a muchos
    public function photoposts()
    {
        return $this->hasMany(Photopost::class);
    }

    public function photomposts()
    {
        return $this->hasMany(Photompost::class);
    }

    //de uno a muchos
    /*  public function photocategoryposts()
    {
        return $this->hasMany(Photocategorypost::class);
    } */

    public function setCategorypostIdAttribute($categorypost)
    {
        $this->attributes['categorypost_id'] = Categorypost::find($categorypost)
            ? $categorypost
            : Categorypost::create(['name' => $categorypost])->id;
    }


    public function syncTags($tags)
    {

        $tagIds = collect($tags)->map(function ($tag) {
            return Tag::find($tag) ? $tag : Tag::create(['name' => $tag])->id;
        });

        return $this->tags()->sync($tagIds);
    }

    public function categorypost()
    {
        return $this->belongsTo(Categorypost::class);
    }
}
