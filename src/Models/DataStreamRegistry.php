<?php

namespace ClarionApp\EloquentMultiChainBridge\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ClarionApp\EloquentMultiChainBridge\EloquentMultiChainBridge;

class DataStreamRegistry extends Model
{
    use HasFactory, EloquentMultiChainBridge;

    protected $stream = "DataStreamRegistry";
}
