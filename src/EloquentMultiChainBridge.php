<?php

namespace ClarionApp\EloquentMultiChainBridge;

use ClarionApp\MultiChain\Facades\MultiChain;
use ClarionApp\EloquentMultiChainBridge\Models\DataStreamRegistry;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;
use ErrorException;
use Str;
use Auth;
use DateTimeInterface;

trait EloquentMultiChainBridge
{
    use SoftDeletes;

    protected function initializeEloquentMultiChainBridge()
    {
        $this->keyType = "string";
        $this->incrementing = false;
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) 
        {
            $model->id = (string) Str::uuid();
        });

        self::created(function($model)
        {
            self::chainUpdate($model);
        });

        self::updated(function($model)
        {
            self::chainUpdate($model);
        });

        self::deleted(function($model)
        {
            self::chainUpdate($model);
        });
    }

    public static function chainUpdate($model)
    {
        if(config('eloquent-multichain-bridge.disabled') == true) return;

        $stream = self::getModelStream(); 
        $user = Auth::user();

        $data = $model->toArray();

        $entry = array(
            'tableName'=>with(new static)->getTable(),
            'tableData'=>$data
        );

        try
        {
            $txid = MultiChain::publish($stream, $data['id'], bin2hex(json_encode($entry)));
        }
        catch(Exception $e)
        {
            $message = "Error saving ".get_class(new static())." to $stream.\n";
            $message.= $e->getMessage();
            throw new ErrorException($message);
        }
    }

    public static function getModelStream()
    {
        $c = new static();
        if(isset($c->stream))
        {
            self::createStream($c->stream);
            return $c->stream;
        }

        $className = get_class($c);
        $stream = DataStreamRegistry::where('class_name', $className)->first();
        if(!$stream)
        {
            throw new ErrorException("$className is not registered to a data stream.");
        }
        self::createStream($stream->data_stream);
        return $stream->data_stream;
    }

    public static function createStream($stream)
    {
        try
        {
            $results = MultiChain::liststreams($stream);
        }
        catch (Exception $e)
        {
            MultiChain::create('stream', $stream, false);
            MultiChain::subscribe($stream);
        }
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
