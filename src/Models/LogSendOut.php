<?php

namespace Hasanablak\JwtAuth\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSendOut extends Model
{
	use HasFactory;

	protected $fillable = ['data', 'user_id', 'type_id'];
}
