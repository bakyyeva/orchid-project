<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Screen\AsSource;

class Task extends Model
{
    use HasFactory, AsSource, Attachable;

    protected $fillable = ['name', 'description', 'image', 'category'];

}
