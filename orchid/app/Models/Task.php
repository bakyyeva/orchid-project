<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Platform\Concerns\Sortable;
use Orchid\Screen\AsSource;

class Task extends Model
{
    use HasFactory, AsSource, Attachable, Filterable, Sortable;

    protected $fillable = ['name', 'description', 'image', 'category'];

    protected $allowedFilters  = ['name' => Like::class];

    protected $allowedSorts = ['id'];
}
