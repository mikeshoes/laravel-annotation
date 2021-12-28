<?php


namespace LaravelAnnotation\Middleware;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Annotation
{
    protected $isOpen = true;

    protected $callbacks = [];

    protected $config = [];

    public function __construct()
    {
        $annotationConfig = app()->make('config')->get('annotation');

        $this->isOpen = $annotationConfig['is_open'] ?? true;

        $this->callbacks = $annotationConfig['callbacks'] ?? [];

        $file = app()->bootstrapPath($annotationConfig['cache_file']);

        if (is_file($file)) {
            $this->config = require $file;
        }
    }

    public function handle(Request $request, \Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        if (!$this->isOpen) {
            return;
        }

        $method = $request->route()->getActionName();
        $controller = Arr::first(explode('@', $method));
        $method = Arr::last(explode('@', $method));
        $handles = $this->callbacks['terminate'] ?? [];

        foreach ($handles as $key => $handle) {
             $config = $this->config[$key][$controller][$method] ?? [];

            list($class, $method) = Str::parseCallback($handle, 'handle');
            if (!empty($class)) {
                $classObj = app()->make($class);
                $classObj->$method($config, $request);
            }
        }
    }
}