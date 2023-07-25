<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicInterest extends Model
{
    use HasFactory;
    protected $table = 'topic_interests';
    protected $fillable = ['name'];
}