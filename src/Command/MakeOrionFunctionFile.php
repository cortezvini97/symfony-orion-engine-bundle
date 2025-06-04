<?php

namespace Orion\OrionEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'orion:make:function_file',
    description: 'Create function file php',
)]
class MakeOrionFunctionFile extends Command
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('valor', null, InputOption::VALUE_REQUIRED, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if(!file_exists($this->dir)){
            $io->error('Not found functions directory and php files.');
            return Command::FAILURE;
        }

        $name = $io->ask("Function File");

        $file_path = $this->dir.DIRECTORY_SEPARATOR.$name.".php";

        if(file_exists($file_path)){
            $io->error("File exists!");
            return Command::FAILURE;
        }

        // Conteúdo do arquivo PHP
        $content = "<?php";

        // Criar o arquivo
        $result = file_put_contents($file_path, $content);

        if ($result === false) {
            $io->error("Failed to create file: $file_path");
            return Command::FAILURE;
        }

        $io->success("Function file created successfully: $file_path");

        return Command::SUCCESS;
    }
}