<?php

namespace LaravelAnnotation\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class AnnotationCache extends Command
{

    public function configure()
    {
        $this
            ->setName('annotation:cache')
            ->setDescription('cache annotation config');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \ReflectionException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = app()->make('config');
        $controllerPaths = $config->get('annotation.search_path');
        $searchPaths = [];
        foreach ($controllerPaths as $path) {
            $searchPaths[] = app()->basePath($path);
        }

        $basePath = app()->basePath();
        $bootCachePath = app()->bootstrapPath($config->get('annotation.cache_file'));
        $baseNamespace = app()->getNamespace();
        $annotationPrefix = $config->get('annotation.annotation_prefix');
        $cacheData = [];
        foreach (Finder::create()->files()->name($config->get('annotation.file_patten'))->in($searchPaths) as $file) {
            $realPath = $file->getRealPath();
            $class = ltrim(str_replace([$basePath . DIRECTORY_SEPARATOR, '.php', 'app', '/'], ['','', trim($baseNamespace, '\\'), '\\'], $realPath), DIRECTORY_SEPARATOR);

            $reflectClass = new \ReflectionClass($class);
            foreach($reflectClass->getMethods() as $method) {
                $doc = $method->getDocComment();

                foreach ($annotationPrefix as $prefix) {
                    $regex = "/@${prefix}_([a-zA-Z]+) (.*)/i";
                    if (preg_match_all($regex, $doc, $matches)) {
                        $data = array_combine($matches[1], $matches[2]);
                        $cacheData[$prefix][$class][$method->getName()] = $data;
                    }
                }
            }
        }
        if ($cacheData) {
            file_put_contents($bootCachePath, "<?php \r\n return  " . var_export($cacheData, true) . ";");
            $this->comment('annotation config cached success');
            return;
        }

        $this->comment('nothing to cached');
    }

}