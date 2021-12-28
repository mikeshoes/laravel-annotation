<?php


namespace LaravelAnnotation\Commands;


use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnnotationClear extends Command
{

    public function configure()
    {
        $this
            ->setName('annotation:clear')
            ->setDescription('clear annotation config cache');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = app()->make('config');
        $bootCachePath = app()->bootstrapPath($config->get('annotation.cache_file'));

        @unlink($bootCachePath);

        $this->comment('annotation config clear success');
        return 0;
    }
}